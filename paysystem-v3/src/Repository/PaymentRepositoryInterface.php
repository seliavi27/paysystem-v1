<?php
declare(strict_types=1);

namespace App\Repository;

use DateTime;
use App\Entity\Payment;
use App\Enum\PaymentStatus;

interface PaymentRepositoryInterface extends RepositoryInterface
{
    public function update(Payment $payment): bool;
    public function findByUserId(string $userId): array;
    public function findByStatus(PaymentStatus $status): array;
    public function findByUserIdAndStatus(string $userId, PaymentStatus $status): array;
    public function countCompletedForUser(string $userId): int;
    public function findSince(DateTime $since): array;
}