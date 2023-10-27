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

    public function findOneByValue(string $accessToken): ?AccessToken
    {
        return
            $this->createQueryBuilder('t')
                ->andWhere('t.token = :val')
                ->setParameter('val', $accessToken)
                ->getQuery()
                ->getOneOrNullResult()
        ;
    }
}
