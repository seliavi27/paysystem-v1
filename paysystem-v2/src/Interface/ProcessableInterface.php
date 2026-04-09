<?php
declare(strict_types=1);

interface ProcessableInterface
{
    public function process(Payment $payment): void;
}