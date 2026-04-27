<?php
declare(strict_types=1);

namespace PaySystem\Repository;

use Doctrine\ORM\EntityRepository;

use PaySystem\Entity\Payment;
use PaySystem\Enum\PaymentStatus;

class PaymentRepository extends EntityRepository implements PaymentRepositoryInterface
{
    public function findById(string $id): ?object
    {
        return $this->find($id);
    }

    public function findByUserId(string $userId): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('IDENTITY(p.user) = :uid')
            ->orderBy('p.createdAt', 'DESC')
            ->setParameter('uid', $userId)
            ->getQuery()
            ->getResult();
    }

    public function findByUserIdAndStatus(string $userId, PaymentStatus $status): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('IDENTITY(p.user) = :uid')
            ->andWhere('p.status = :status')
            ->orderBy('p.createdAt', 'DESC')
            ->setParameter('uid', $userId)
            ->setParameter('status', $status)
            ->getQuery()
            ->getResult();
    }

    public function countCompletedForUser(string $userId): int
    {
        return (int)$this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->andWhere('IDENTITY(p.user) = :uid')
            ->andWhere('p.status = :status')
            ->setParameter('uid', $userId)
            ->setParameter('status', PaymentStatus::COMPLETED)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findByStatus(PaymentStatus $status): array
    {
        return $this->findBy(['status' => $status]);
    }

    public function saveEntity(object $entity): bool
    {
        /** @var Payment $entity */
        $em = $this->getEntityManager();
        $em->persist($entity);
        $em->flush();
        return true;
    }

    public function update(Payment $payment): bool
    {
        $payment->touch();
        $this->getEntityManager()->flush();
        return true;
    }

    public function delete(string $id): bool
    {
        $payment = $this->find($id);
        if ($payment) {
            $em = $this->getEntityManager();
            $em->remove($payment);
            $em->flush();
            return true;
        }
        return false;
    }
}
