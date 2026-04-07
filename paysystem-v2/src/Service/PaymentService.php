<?php
declare(strict_types=1);

class PaymentService
{
    private PaymentProcessorInterface $processor;
    private RepositoryInterface $repository;

    public function __construct(
        PaymentProcessorInterface $processor,
        RepositoryInterface $repository
    )
    {
        $this->processor = $processor;
        $this->repository = $repository;
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
            log_error($e->getMessage());
            throw $e;
        }

        $this->repository->update($payment);
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