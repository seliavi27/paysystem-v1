<?php
declare(strict_types=1);

namespace PaySystem\Repository;

use PaySystem\Entity\Payment;
use PaySystem\Enum\PaymentStatus;

interface PaymentRepositoryInterface extends RepositoryInterface
{
    public function update(Payment $payment): bool;
    public function findByUserId(string $userId): array;
    public function findByStatus(PaymentStatus $status): array;

}