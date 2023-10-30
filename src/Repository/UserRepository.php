<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\AccessToken;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @implements PasswordUpgraderInterface<User>
 *
 * @method null|User find($id, $lockMode = null, $lockVersion = null)
 * @method null|User findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly AccessTokenRepository $tokenRepository
    ) {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if ( ! $user instanceof User)
        {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function countUsers(): int
    {
        try
        {
            return $this
                ->createQueryBuilder('u')
                ->select('count(u.username)')
                ->getQuery()
                ->getSingleScalarResult()
            ;
        } catch (NoResultException)
        {
            return 0;
        }
    }

    /**
     * Checks if db has at least one admin.
     */
    public function hasUser(): bool
    {
        return $this->countUsers() > 0;
    }

    public function generateOrGetToken(User $user): AccessToken
    {
        $valid = null;

        $em    = $this->tokenRepository->getEntityManager();

        /** @var AccessToken $token */
        foreach ($user->getTokens() as $token)
        {
            if ( ! $token->isExpired())
            {
                $valid = $token;
            } elseif ( ! $token->isPermanent())
            {
                $em->remove($token);
            }
        }

        if ( ! isset($valid))
        {
            $em->persist($valid = new AccessToken($user));
        }

        $em->flush();
        return $valid;
    }

    public function loadUserByUsernameOrEmail(string $identifier): ?User
    {
        if (
            $user = $this->createQueryBuilder('u')
                ->andWhere('u.enabled = 1')
                ->andWhere('u.username = :val')
                ->orWhere('u.email = :val')
                ->setParameter('val', $identifier)
                ->getQuery()
                ->getOneOrNullResult()
        ) {
            $this->tokenRepository->cleanUpTokens();
        }

        return $user;
    }

    //    /**
    //     * @return User[] Returns an array of User objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?User
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
