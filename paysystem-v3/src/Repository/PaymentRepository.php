<?php
declare(strict_types=1);

namespace PaySystem\Repository;

use Doctrine\DBAL\Connection;
use PaySystem\Entity\Payment;
use PaySystem\Enum\PaymentStatus;

class PaymentRepository implements PaymentRepositoryInterface
{
    public function __construct(
        private Connection $connection
    ) {}

    public function findAll(): array
    {
        return $this->connection->createQueryBuilder()
            ->select('*') ->from('payments')
            ->executeQuery()
            ->fetchAllAssociative();
    }

    public function findById(string $id): ?Payment
    {
        $row = $this->connection->createQueryBuilder()
            ->select('*')->from('payments')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->executeQuery()
            ->fetchAssociative();

        return $row ? $this->hydrate($row) : null;
    }

    public function findByUserId(string $userId): array
    {
        $rows = $this->connection->createQueryBuilder()
            ->select('*')->from('payments')
            ->where('user_id = :uid')
            ->orderBy('created_at', 'DESC')
            ->setParameter('uid', $userId)
            ->executeQuery()
            ->fetchAllAssociative();

        return array_map(fn(array $r) => $this->hydrate($r), $rows);
    }

    public function findByStatus(PaymentStatus $status): array
    {
        $rows = $this->connection->createQueryBuilder()
            ->select('*')->from('payments')
            ->where('status = :s')
            ->setParameter('s', $status->value)
            ->executeQuery()
            ->fetchAllAssociative();

        return array_map(fn(array $r) => $this->hydrate($r), $rows);
    }

    public function saveEntity(object $entity): bool
    {
        /** @var Payment $entity */
        $this->connection->insert('payments', [
            'id'          => $entity->id,
            'user_id'     => $entity->userId,
            'amount'      => $entity->amount,
            'description' => $entity->description,
            'currency'    => $entity->currency->value,
            'status'      => $entity->status->value,
            'method'      => $entity->method->value,
            'created_at'  => $entity->createdAt->format('Y-m-d H:i:s.u O'),
            'updated_at'  => $entity->updatedAt->format('Y-m-d H:i:s.u O'),
        ]);
        return true;
    }

    public function update(Payment $payment): bool
    {
        return $this->connection->update('payments', [
                'status'     => $payment->status->value,
                'amount'     => $payment->amount,
                'updated_at' => (new \DateTime())->format('Y-m-d H:i:s.u O'),
            ], ['id' => $payment->id]) > 0;
    }

    public function delete(string $id): bool
    {
        return $this->connection->delete('payments', ['id' => $id]) > 0;
    }

    private function hydrate(array $row): Payment
    {
        return Payment::fromArray([
            'id'          => $row['id'],
            'userId'      => $row['user_id'],
            'amount'      => (float)$row['amount'],
            'description' => $row['description'],
            'currency'    => $row['currency'],
            'status'      => $row['status'],
            'method'      => $row['method'],
            'createdAt'   => $row['created_at'],
            'updatedAt'   => $row['updated_at'],
        ]);
    }
}
