<?php
declare(strict_types=1);

class PaymentService
{
    private PaymentProcessorInterface $processor;
    private ValidatorInterface $validator;
    private StorageInterface $storage;

    public function __construct(
        PaymentProcessorInterface $processor,
        ValidatorInterface $validator,
        StorageInterface $storage
    )
    {
        $this->processor = $processor;
        $this->validator = $validator;
        $this->storage = $storage;
    }

    public function processPayment(Payment $payment): void
    {
        $this->validator->validate($payment);

        try
        {
            $this->processor->process($payment);
            $this->storage->save($payment);

        }
        catch (Throwable $e)
        {
            $payment->status = PaymentStatus::FAILED;
            log_error('Payment failed: ' . $e->getMessage());
            $this->storage->save($payment);

            throw $e;
        }
    }

    public function refundPayment(Payment $payment): void
    {
        try
        {
            $this->processor->refund($payment);
            $this->storage->save($payment);

        }
        catch (Throwable $e)
        {
            log_error('Refund failed: ' . $e->getMessage());
            throw $e;
        }
    }
}