<?php
declare(strict_types=1);

namespace PaySystem\Repository;

use Doctrine\ORM\EntityRepository;

use PaySystem\Entity\User;

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
        $em = $this->getEntityManager();
        $em->persist($entity);
        $em->flush();
        return true;
    }

    public function update(User $user): bool
    {
        $user->touch();
        $this->getEntityManager()->flush();
        return true;
    }

    public function delete(string $id): bool
    {
        $user = $this->find($id);
        if ($user === null) {
            return false;
        }

        $em = $this->getEntityManager();
        $em->remove($user);
        $em->flush();
        return true;
    }
}
