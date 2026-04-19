<?php
declare(strict_types=1);

namespace PaySystem\Service;

use PaySystem\DTO\CreatePaymentRequest;
use PaySystem\Entity\Payment;
use PaySystem\Enum\PaymentStatus;
use PaySystem\Exception\NotFoundException;
use PaySystem\Exception\PaymentException;
use PaySystem\Factory\PaymentMethodFactory;
use PaySystem\Interface\LogServiceInterface;
use PaySystem\Repository\PaymentRepositoryInterface;
use Throwable;

class PaymentService implements PaymentServiceInterface
{
    public function __construct(
        private PaymentMethodFactory $processorFactory,
        private PaymentRepositoryInterface $repository,
        private NotificationServiceInterface $notifier,
        private LogServiceInterface $logger
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
        $payment->status = PaymentStatus::PROCESSING;
        $this->repository->update($payment);

        try
        {
            $processor = $this->processorFactory->create($payment->method);
            $processor->process($payment);
            $payment->status = PaymentStatus::COMPLETED;
        }
        catch (Throwable $e)
        {
            $payment->status = PaymentStatus::FAILED;
            $this->logger->error($e->getMessage());
            $this->repository->update($payment);
            throw $e;
        }

        $this->repository->update($payment);
        $this->logger->info("Payment completed: {$payment->id}");
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

        $this->processorFactory->create($payment->method)->refund($payment);
        $payment->status = PaymentStatus::REFUNDED;
        $this->repository->update($payment);
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
