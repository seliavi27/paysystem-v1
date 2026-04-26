<?php
declare(strict_types=1);

namespace PaySystem\Repository;

use PaySystem\Entity\Payment;
use PaySystem\Enum\PaymentStatus;
use PaySystem\Storage\StorageInterface;

class PaymentRepository implements PaymentRepositoryInterface
{
    private StorageInterface $storage;

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function saveEntity(object $entity): bool
    {
        $payments = $this->load();
        $payments[] = $entity;
        return $this->save($payments);
    }

    public function update(Payment $payment): bool
    {
        $payments = $this->load();

        if (!isset($payment->id))
        {
            return false;
        }

        foreach ($payments as $p)
        {
            if (isset($payment->id) && $p->id === $payment->id)
            {
                unset($p);
            }
        }

        return $this->save($payments);
    }

    public function delete(string $id): bool
    {
        $payments = $this->load();

        foreach ($payments as $payment)
        {
            if (isset($payment->id) && $payment->id === $id)
            {
                unset($payment);
            }
        }

        return $this->save($payments);
    }

    public function findById(string $id): ?object
    {
        $payments = $this->load();
        return array_find($payments, fn($payment) => isset($payment->id) && $payment->id === $id);
    }

    public function findByUserId(string $userId): array
    {
        $payments = $this->findAll();
        return array_filter($payments, fn($p) => $p->userId === $userId);
    }

    public function findByStatus(PaymentStatus $status): array
    {
        $payments = $this->findAll();
        return array_filter($payments, fn($p) => $p->status === $status);
    }

    public function findAll(): array
    {
        return $this->load();
    }

    private function load(): array
    {
        return array_map(
            fn($item) => Payment::fromArray($item),
            $this->storage->load()
        );
    }

    private function save(array $data): bool
    {
        return $this->storage->save($data);
    }
}
