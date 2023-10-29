<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\UserOption;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Option>
 *
 * @method null|UserOption find($id, $lockMode = null, $lockVersion = null)
 * @method null|UserOption findOneBy(array $criteria, array $orderBy = null)
 * @method UserOption[]    findAll()
 * @method UserOption[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserOptionRepository extends ServiceEntityRepository
{
    private static array $options = [];

    private static array $counts  = [];

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserOption::class);
    }
}
