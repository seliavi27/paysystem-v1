<?php
declare(strict_types=1);

namespace PaySystem\Interface;

use PaySystem\Entity\Payment;

interface ValidatorInterface
{
    public function validate(Payment $payment): bool;

    public function getErrors(): array;
}