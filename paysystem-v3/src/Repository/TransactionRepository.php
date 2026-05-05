<?php
declare(strict_types=1);

namespace PaySystem\Repository;

use DateTime;
use Doctrine\DBAL\Connection;
use PaySystem\Entity\Transaction;
use PaySystem\Enum\CurrencyType;
use PaySystem\Enum\TransactionType;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function __construct(
        private Connection $connection
    ) {}

    public function findAll(): array
    {
        return $this->connection->createQueryBuilder()
            ->select('*')->from('transactions')
            ->executeQuery()
            ->fetchAllAssociative();
    }

    public function findById(string $id): ?Transaction
    {
        $row = $this->connection->createQueryBuilder()
            ->select('*')->from('transactions')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->executeQuery()
            ->fetchAssociative();

        return $row ? $this->hydrate($row) : null;
    }

    /**
     * @return Transaction[]
     */
    public function findByPaymentId(string $paymentId): array
    {
        $rows = $this->connection->createQueryBuilder()
            ->select('*')->from('transactions')
            ->where('payment_id = :pid')
            ->orderBy('created_at', 'DESC')
            ->setParameter('pid', $paymentId)
            ->executeQuery()
            ->fetchAllAssociative();

        return array_map(fn(array $r) => $this->hydrate($r), $rows);
    }

    /**
     * @return Transaction[]
     */
    public function findByUserId(string $userId): array
    {
        $rows = $this->connection->createQueryBuilder()
            ->select('*')->from('transactions')
            ->where('user_id = :uid')
            ->orderBy('created_at', 'DESC')
            ->setParameter('uid', $userId)
            ->executeQuery()
            ->fetchAllAssociative();

        return array_map(fn(array $r) => $this->hydrate($r), $rows);
    }

    public function saveEntity(object $entity): bool
    {
        /** @var Transaction $entity */
        $this->connection->insert('transactions', [
            'id'          => $entity->id,
            'payment_id'  => $entity->paymentId,
            'user_id'     => $entity->userId,
            'type'        => $entity->type->value,
            'currency'    => $entity->currency->value,
            'amount'      => $entity->amount,
            'description' => $entity->description,
            'created_at'  => $entity->timestamp->format('Y-m-d H:i:s.u O'),
        ]);
        return true;
    }

    public function delete(string $id): bool
    {
        return $this->connection->delete('transactions', ['id' => $id]) > 0;
    }

    private function hydrate(array $row): Transaction
    {
        return new Transaction(
            userId:      (string)$row['user_id'],
            paymentId:   (string)$row['payment_id'],
            type:        TransactionType::from((string)$row['type']),
            currency:    CurrencyType::from((string)$row['currency']),
            amount:      (float)$row['amount'],
            description: (string)$row['description'],
            timestamp:   new DateTime((string)$row['created_at']),
            id:          (string)$row['id'],
        );
    }
}
