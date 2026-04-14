<?php
declare(strict_types=1);

namespace PaySystem\Service;

use PaySystem\Enum\PaymentStatus;
use PaySystem\DTO\CreatePaymentRequest;
use PaySystem\Entity\Payment;
use PaySystem\Interface\LogServiceInterface;
use PaySystem\Interface\PaymentProcessorInterface;
use PaySystem\Notification\NotificationChannelInterface;
use PaySystem\Repository\PaymentRepositoryInterface;
use RuntimeException;
use Throwable;

class PaymentService implements PaymentServiceInterface
{
    private PaymentProcessorInterface $processor;
    private PaymentRepositoryInterface $repository;
    private NotificationServiceInterface $notifier;
    private LogServiceInterface $logger;

    public function __construct(
        PaymentProcessorInterface $processor,
        PaymentRepositoryInterface $repository,
        NotificationServiceInterface $notifier,
        LogServiceInterface $logger
    )
    {
        $this->processor = $processor;
        $this->repository = $repository;
        $this->notifier = $notifier;
        $this->logger = $logger;
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
        return $payment;
    }

    public function process(Payment $payment): void
    {
        $payment->status = PaymentStatus::PROCESSING;
        $this->repository->update($payment);

        try
        {
            $this->processor->process($payment);
            $payment->status = PaymentStatus::COMPLETED;
        }
        catch (Throwable $e)
        {
            $payment->status = PaymentStatus::FAILED;
            $this->logger->error($e->getMessage());
            throw $e;
        }

        $this->repository->update($payment);
        $this->logger->info("Payment COMPLETED");
    }

    public function refund(string $id): void
    {
        $payment = $this->repository->findById($id);

        if (!($payment instanceof Payment))
        {
            return;
        }

        if ($payment->status !== PaymentStatus::COMPLETED)
        {
            throw new RuntimeException('Only completed payments can be refunded');
        }

        $this->processor->refund($payment);

        $payment->status = PaymentStatus::REFUNDED;

        $this->repository->update($payment);
    }

    /**
     * @param string $id
     * @return ?Payment
     */
    public function show(string $id): ?Payment
    {
        $payment = $this->repository->findById($id);

        if (!($payment instanceof Payment))
        {
            $this->logger->error("Payment not found: {$id}");
            throw new RuntimeException("Payment with id {$id} not found");
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
        $paymentsAll = $this->repository->findAll();
        return array_filter($paymentsAll, fn($p) => ($p->userId === $userId) && ($p->status === $paymentStatus));
    }
}