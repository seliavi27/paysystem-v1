<?php
declare(strict_types=1);

namespace PaySystem\Repository;

use PaySystem\Entity\Payment;

class PaymentRepository implements PaymentRepositoryInterface
{
    private string $context;
    private array $payments = [];

    public function __construct(
        string $_context)
    {
        $this->context = $_context;
        $this->load();
    }

    private function load(): void
    {

    }

    private function saveToFile(): void
    {
        $data = array_map(fn(Payment $p) => $p->toArray(), $this->payments);
        // save to $_context
    }

    public function find(string $id): ?Payment
    {
        return $this->payments[$id] ?? null;
    }

    public function findAll(): array
    {
        return array_values($this->payments);
    }

    public function save(Payment $payment): bool
    {
        $this->payments[$payment->id] = $payment;
        $this->saveToFile();
        return true;
    }

    public function delete(string $id): bool
    {
        if (!isset($this->payments[$id]))
        {
            return false;
        }

        unset($this->payments[$id]);
        $this->saveToFile();
        return true;
    }
}
