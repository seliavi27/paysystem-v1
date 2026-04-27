<?php
declare(strict_types=1);

namespace PaySystem\Service;

use DateTime;
use Doctrine\DBAL\Connection;
use PaySystem\DTO\CreatePaymentRequest;
use PaySystem\Entity\Payment;
use PaySystem\Entity\Transaction;
use PaySystem\Enum\PaymentStatus;
use PaySystem\Enum\TransactionType;
use PaySystem\Exception\NotFoundException;
use PaySystem\Exception\PaymentException;
use PaySystem\Factory\PaymentMethodFactory;
use PaySystem\Interface\LogServiceInterface;
use PaySystem\Repository\PaymentRepositoryInterface;
use PaySystem\Repository\TransactionRepositoryInterface;

class PaymentService implements PaymentServiceInterface
{
    public function __construct(
        private PaymentMethodFactory $processorFactory,
        private PaymentRepositoryInterface $repository,
        private TransactionRepositoryInterface $transactionRepository,
        private NotificationServiceInterface $notifier,
        private LogServiceInterface $logger,
        private Connection $connection
    ) {
    }

    public function create(CreatePaymentRequest $request): Payment
    {
        $payment = Payment::create(
            userId: $request->userId,
            amount: $request->amount,
            description: $request->description,
            currency: $request->currency,
            method: $request->method,
        );

        $this->repository->saveEntity($payment);
        $this->process($payment);

        return $payment;
    }

    public function process(Payment $payment): void
    {
        $this->connection->transactional(function () use ($payment) {
            $payment->status = PaymentStatus::PROCESSING;
            $this->repository->update($payment);

            $processor = $this->processorFactory->create($payment->method);
            $processor->process($payment);

            $payment->status = PaymentStatus::COMPLETED;
            $this->repository->update($payment);

            $this->transactionRepository->saveEntity(new Transaction(
                userId:      $payment->userId,
                paymentId:   $payment->id,
                type:        TransactionType::EXPENSE,
                currency:    $payment->currency,
                amount:      $payment->amount,
                description: 'Payment processed',
                timestamp:   new DateTime(),
            ));
        });
    }

    public function refund(string $id): void
    {
        $payment = $this->repository->findById($id);

        if (!$payment instanceof Payment)
        {
            throw new NotFoundException("Payment {$id} not found");
        }

        if ($payment->status !== PaymentStatus::COMPLETED)
        {
            throw new PaymentException('Only completed payments can be refunded');
        }

        $this->connection->transactional(function () use ($payment) {
            $this->processorFactory->create($payment->method)->refund($payment);
            $payment->status = PaymentStatus::REFUNDED;
            $this->repository->update($payment);

            $this->transactionRepository->saveEntity(new Transaction(
                userId:      $payment->userId,
                paymentId:   $payment->id,
                type:        TransactionType::REFUND,
                currency:    $payment->currency,
                amount:      $payment->amount,
                description: 'Payment refunded',
                timestamp:   new DateTime(),
            ));
        });
    }

    public function show(string $id): ?Payment
    {
        $payment = $this->repository->findById($id);

        if (!$payment instanceof Payment)
        {
            throw new NotFoundException("Payment {$id} not found");
        }

        return $payment;
    }

    public function showAllByUserId(string $userId): array
    {
        return $this->repository->findByUserId($userId);
    }

    public function showAllByStatus(string $userId, string $status): array
    {
        $paymentStatus = PaymentStatus::from($status);

        return array_values(
            array_filter(
                $this->repository->findByUserId($userId),
                fn(Payment $p): bool => $p->status === $paymentStatus
            )
        );
    }
}
