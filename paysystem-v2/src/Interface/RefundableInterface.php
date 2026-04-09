<?php
declare(strict_types=1);

interface RefundableInterface
{
    public function refund(Payment $payment): void;
}
