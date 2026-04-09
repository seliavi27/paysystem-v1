<?php
declare(strict_types=1);

namespace PaySystem\Repository;

use InvalidArgumentException;
use PaySystem\Entity\Payment;
use PaySystem\Enum\PaymentStatus;
use PaySystem\Storage\StorageInterface;

class PaymentRepository implements PaymentRepositoryInterface
{
    private StorageInterface $storage;
    private string $filePath;

    public function __construct(string $filePath = PAYMENTS_FILE)
    {
        $this->filePath = $filePath;
        $this->ensureFileExists();
    }

    public function save(object $entity): bool
    {
        $payments = $this->load();
        $payments[$entity->id] = serialize($entity);
        $result = file_put_contents($this->filePath, json_encode($payments));

        if ($result === false)
        {
            return false;
        }

        return true;
    }

    public function update(Payment $payment): bool
    {
        $payments = $this->load();

        if (!isset($payments[$payment->id]))
        {
            return false;
        }

        $payments[$payment->id] = serialize($payment);
        $result = file_put_contents($this->filePath, json_encode($payments));

        if ($result === false)
        {
            return false;
        }

        return true;
    }

    public function delete(string $id): bool
    {
        $payments = $this->load();

        if (!isset($payments[$id]))
        {
            return false;
        }

        unset($payments[$id]);
        $result = file_put_contents($this->filePath, json_encode($payments));

        if ($result === false)
        {
            return false;
        }

        return true;
    }

    public function findById(string $id): ?object
    {
        $payments = $this->load();

        if (isset($payments[$id]))
        {
            return unserialize($payments[$id]);
        }

        return null;
    }

    public function findByUserId(int $userId): array
    {
        $payments = $this->findAll();

        return array_filter($payments, fn($p) => $p->getUserId() === $userId);
    }

    public function findByStatus(PaymentStatus $status): array
    {
        $payments = $this->findAll();

        return array_filter($payments, fn($p) => $p->status === $status);
    }

    public function findAll(): array
    {
        return array_map(
            fn($data) => unserialize($data),
            $this->load()
        );
    }

    private function load(): array
    {
        if (!file_exists($this->filePath))
        {
            return [];
        }

        return json_decode(file_get_contents($this->filePath), true) ?? [];
    }

    private function ensureFileExists(): void
    {
        if (!file_exists($this->filePath))
        {
            file_put_contents($this->filePath, '{}');
        }
    }
}
