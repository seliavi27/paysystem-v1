<?php
declare(strict_types=1);

namespace PaySystem\Repository;

use PaySystem\Entity\Payment;
use PaySystem\Enum\PaymentStatus;

class PaymentRepository implements PaymentRepositoryInterface
{
    private string $filePath;

    public function __construct(string $filePath = PAYMENTS_FILE)
    {
        $this->filePath = $filePath;
        $this->ensureFileExists();
    }

    public function save(object $entity): bool
    {
        $payments = $this->loadData();
        $payments[$entity->id] = $entity->toArray();
        return $this->writeData($payments);
    }

    public function update(Payment $payment): bool
    {
        $payments = $this->loadData();

        if (!isset($payments[$payment->id]))
        {
            return false;
        }

        $payments[$payment->id] = $payment->toArray();
        return $this->writeData($payments);
    }

    public function delete(string $id): bool
    {
        $payments = $this->loadData();

        if (!isset($payments[$id]))
        {
            return false;
        }

        unset($payments[$id]);
        return $this->writeData($payments);
    }

    public function findById(string $id): ?Payment
    {
        $payments = $this->loadData();

        if (isset($payments[$id]))
        {
            return Payment::fromArray($payments[$id]);
        }

        return null;
    }

    public function findByUserId(string $userId): array
    {
        $payments = $this->findAll();

        return array_filter($payments, fn(Payment $p) => $p->userId === $userId);
    }

    public function findByStatus(PaymentStatus $status): array
    {
        $payments = $this->findAll();

        return array_filter($payments, fn(Payment $p) => $p->status === $status);
    }

    public function findAll(): array
    {
        return array_map(
            fn(array $data) => Payment::fromArray($data),
            $this->loadData()
        );
    }

    private function loadData(): array
    {
        if (!file_exists($this->filePath))
        {
            return [];
        }

        return json_decode(file_get_contents($this->filePath), true) ?? [];
    }

    private function writeData(array $payments): bool
    {
        $result = file_put_contents(
            $this->filePath,
            json_encode($payments, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );

        return $result !== false;
    }

    private function ensureFileExists(): void
    {
        if (!file_exists($this->filePath))
        {
            file_put_contents($this->filePath, '{}');
        }
    }
}
