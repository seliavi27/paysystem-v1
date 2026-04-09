<?php
declare(strict_types=1);

namespace PaySystem\Service;

use PaySystem\Enum\PaymentStatus;
use PaySystem\DTO\CreatePaymentRequest;
use PaySystem\Entity\Payment;
use PaySystem\Interface\PaymentProcessorInterface;
use PaySystem\Notification\NotificationChannelInterface;
use PaySystem\Repository\PaymentRepositoryInterface;
use RuntimeException;
use Throwable;

class PaymentService
{
    private PaymentProcessorInterface $processor;
    private PaymentRepositoryInterface $repository;
    private NotificationChannelInterface $notifier;
    //private LoggerInterface $logger;

    public function __construct(
        PaymentProcessorInterface $processor,
        PaymentRepositoryInterface $repository,
        NotificationChannelInterface $notifier,
//        LoggerInterface $logger
    )
    {
        $this->processor = $processor;
        $this->repository = $repository;
        $this->notifier = $notifier;
        //$this->logger = $logger;
    }

    public function create(CreatePaymentRequest $request): Payment
    {
        $payment = Payment::create(
            userId: $request->userId,
            amount: $request->amount,
            description: $request->description,
            currency: $request->currency,
            type: $request->paymentMethod,
        );

        $this->repository->save($payment);

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

    public function refund(Payment $payment): void
    {
        if ($payment->status !== PaymentStatus::COMPLETED)
        {
            throw new RuntimeException('Only completed payments can be refunded');
        }

        $this->processor->refund($payment);

        $payment->status = PaymentStatus::REFUNDED;

        $this->repository->update($payment);
    }
}