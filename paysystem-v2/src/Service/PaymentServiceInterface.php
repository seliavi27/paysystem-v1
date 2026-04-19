<?php
declare(strict_types=1);

namespace PaySystem\Service;

use PaySystem\DTO\CreatePaymentRequest;
use PaySystem\Entity\Payment;

interface PaymentServiceInterface
{
    public function create(CreatePaymentRequest $request): Payment;
    public function process(Payment $payment): void;
    public function refund(string $id): void;
    public function show(string $id): ?Payment;
    public function showAllByUserId(string $userId): array;
    public function showAllByStatus(string $userId, string $status): array;
}