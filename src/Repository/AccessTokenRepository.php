<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\AccessToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AccessToken>
 *
 * @method null|AccessToken find($id, $lockMode = null, $lockVersion = null)
 * @method null|AccessToken findOneBy(array $criteria, array $orderBy = null)
 * @method AccessToken[]    findAll()
 * @method AccessToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccessTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AccessToken::class);
    }

    /**
     * Delete expired tokens.
     */
    public function cleanUpTokens(): void
    {
        $this->getEntityManager()->createQueryBuilder()
            ->delete(AccessToken::class, 't')
            ->andWhere('t.permanent = 0')
            ->andWhere('t.expiresAt < :now')
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()->execute()
        ;
    }

    public function findOneByValue(string $accessToken): ?AccessToken
    {
        $this->cleanUpTokens();

        return
            $this->createQueryBuilder('t')
                ->andWhere('t.token = :val')
                ->setParameter('val', $accessToken)
                ->getQuery()
                ->getOneOrNullResult()
        ;
    }
}
