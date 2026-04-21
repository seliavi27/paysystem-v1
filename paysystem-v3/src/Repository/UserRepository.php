<?php
declare(strict_types=1);

namespace PaySystem\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityRepository;
use DateTime;

use PaySystem\Entity\User;
use PaySystem\Storage\StorageInterface;

/**
 * @extends EntityRepository<User>
 */
class UserRepository extends EntityRepository implements UserRepositoryInterface
{
    public function findById(string $id): ?User
    {
        return $this->find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->findOneBy(['email' => $email]);
    }

    public function saveEntity(object $entity): bool
    {
        /** @var User $entity */
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
        return true;
    }

    public function update(User $user): bool
    {
        $user->updatedAt = new DateTime();
        $this->getEntityManager()->flush();
        return true;
    }

    public function delete(string $id): bool
    {
        $user = $this->find($id);

        if ($user)
        {
            $this->getEntityManager()->remove($user);
            $this->getEntityManager()->flush();
            return true;
        }

        return false;
    }
}
