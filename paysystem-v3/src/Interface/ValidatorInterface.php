<?php
declare(strict_types=1);

namespace App\Interface;

use App\Entity\Payment;

interface ValidatorInterface
{
    public function validate(Payment $payment): bool;

    public function getErrors(): array;
}