<?php
declare(strict_types=1);

namespace App\Service;

use Doctrine\DBAL\Connection;
use App\DTO\CreatePaymentRequest;
use App\Entity\Payment;
use App\Enum\PaymentStatus;
use App\Exception\NotFoundException;
use App\Exception\PaymentException;
use App\Factory\PaymentMethodFactory;
use App\Interface\LogServiceInterface;
use App\Repository\PaymentRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use Throwable;

class PaymentService implements PaymentServiceInterface
{
    public function __construct(
        private PaymentMethodFactory $processorFactory,
        private PaymentRepositoryInterface $paymentRepository,
        private UserRepositoryInterface $userRepository,
        private NotificationServiceInterface $notifier,
        private LogServiceInterface $logger,
        private Connection $connection
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

        $this->processorFactory->create($payment->method)->refund($payment);
        $payment->status = PaymentStatus::REFUNDED;
        $this->paymentRepository->update($payment);
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
