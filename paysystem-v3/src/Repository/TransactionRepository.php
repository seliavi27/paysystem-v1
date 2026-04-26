<?php
declare(strict_types=1);

namespace PaySystem\Repository;

use Doctrine\DBAL\Connection;
use PaySystem\Entity\Transaction;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function __construct(
        private Connection $connection
    ) {}

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

    public function saveEntity(object $entity): bool
    {
        /** @var Transaction $entity */
        $this->connection->insert('transactions', [
            'id'         => $entity->id,
            'payment_id' => $entity->paymentId,
            'type'       => $entity->type->value,
            'amount'     => $entity->amount,
            'created_at' => $entity->timestamp->format('Y-m-d H:i:s.u O'),
        ]);
        return true;
    }

    public function delete(string $id): bool
    {
        return $this->connection->delete('transactions', ['id' => $id]) > 0;
    }

    private function hydrate(array $row): Transaction
    {
        // Минимальная гидратация — задача 03 фиксирует только payment_id/type/amount/created_at в схеме.
        // Поля userId/currency/description в Transaction не маппятся в эту таблицу — заполняем дефолтами.
        return new Transaction(
            userId:      '',
            paymentId:   (string)$row['payment_id'],
            type:        \PaySystem\Enum\TransactionType::from((string)$row['type']),
            currency:    \PaySystem\Enum\CurrencyType::RUB,
            amount:      (float)$row['amount'],
            description: '',
            timestamp:   new \DateTime((string)$row['created_at']),
        );
    }
}
