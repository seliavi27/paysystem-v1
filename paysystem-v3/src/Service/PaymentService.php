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
use PaySystem\Repository\UserRepositoryInterface;

class PaymentService implements PaymentServiceInterface
{
    public function __construct(
        private PaymentMethodFactory $processorFactory,
        private PaymentRepositoryInterface $paymentRepository,
        private UserRepositoryInterface $userRepository,
        private TransactionRepositoryInterface $transactionRepository,
        private NotificationServiceInterface $notifier,
        private LogServiceInterface $logger,
        private Connection $connection,
    ) {
    }

    public function create(CreatePaymentRequest $request): Payment
    {
        $user = $this->userRepository->findById($request->userId)
            ?? throw new NotFoundException("User {$request->userId} not found");

        $payment = Payment::create(
            user: $user,
            amount: $request->amount,
            description: $request->description,
            currency: $request->currency,
            method: $request->method,
        );

        $this->paymentRepository->saveEntity($payment);

        $this->process($payment);
        return $payment;
    }

    public function process(Payment $payment): void
    {
        $this->connection->transactional(function () use ($payment) {
            $payment->status = PaymentStatus::PROCESSING;
            $this->paymentRepository->update($payment);

            $processor = $this->processorFactory->create($payment->method);
            $processor->process($payment);

            $payment->status = PaymentStatus::COMPLETED;
            $this->paymentRepository->update($payment);

            $this->transactionRepository->saveEntity(new Transaction(
                userId:      $payment->user->id,
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
        $payment = $this->paymentRepository->findById($id);

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
            $this->paymentRepository->update($payment);

            $this->transactionRepository->saveEntity(new Transaction(
                userId:      $payment->user->id,
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
        $payment = $this->paymentRepository->findById($id);

        if (!$payment instanceof Payment)
        {
            throw new NotFoundException("Payment {$id} not found");
        }

        return $payment;
    }

    public function showAllByUserId(string $userId): array
    {
        return $this->paymentRepository->findByUserId($userId);
    }

    public function showAllByStatus(string $userId, string $status): array
    {
        $paymentStatus = PaymentStatus::from($status);

        return array_values(
            array_filter(
                $this->paymentRepository->findByUserId($userId),
                fn(Payment $p): bool => $p->status === $paymentStatus
            )
        );
    }
}
