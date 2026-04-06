<?php
declare(strict_types=1);

interface ValidatorInterface
{
    public function validate(Payment $payment): bool;
    public function getErrors(): array;
}