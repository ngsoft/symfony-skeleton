<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Option;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Option>
 *
 * @method null|Option find($id, $lockMode = null, $lockVersion = null)
 * @method null|Option findOneBy(array $criteria, array $orderBy = null)
 * @method Option[]    findAll()
 * @method Option[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OptionRepository extends ServiceEntityRepository
{
    private static array $options = [];

    private static array $counts  = [];

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Option::class);

        //        if (empty(self::$options))
        //        {
        //            if ( ! $this->hasOption('autoload'))
        //            {
        //                $this->getEntityManager()->persist(
        //                    (new Option())
        //                        ->setName('autoload')
        //                        ->setDescription('Autoload Options on each requests')
        //                        ->setValue(true)
        //                );
        //                $this->getEntityManager()->flush();
        //            }
        //
        //            if ($this->getOption('autoload'))
        //            {
        //                foreach ($this->findBy(['autoload' => true, 'user' => null]) as $option)
        //                {
        //                    self::$options[$option->getName()] = $option->getValue();
        //                }
        //            }
        //        }
    }

    //    public function getOption(string $name, mixed $defaultValue = null): mixed
    //    {
    //        $value ??= self::$options[$name] ??= $this->findOneBy(['name' => $name, 'user' => null])?->getValue();
    //
    //        if (null === $value)
    //        {
    //            if (null !== $value = value($defaultValue))
    //            {
    //                $this->addOption($name, $value);
    //                self::$options[$name] = $value;
    //            }
    //        }
    //
    //        return $value;
    //    }

    //    public function hasOption(string $name): bool
    //    {
    //        $cache = &self::$counts;
    //
    //        if (empty($cache[$name]))
    //        {
    //            $cache[$name] = (1 === $this->count(['name' => $name, 'user' => null]));
    //        }
    //        return $cache[$name];
    //    }

    //    public function addOption(string $name, mixed $value, ?bool $autoload = null): void
    //    {
    //        $this->addOptions([$name => $value], $autoload);
    //    }

    //    public function addOptions(array $values, ?bool $autoload = null): void
    //    {
    //        $newValues = [];
    //
    //        foreach ($values as $name => $value)
    //        {
    //            if ($this->hasOption($name))
    //            {
    //                continue;
    //            }
    //            $newValues[$name] = $value;
    //        }
    //
    //        if (count($newValues))
    //        {
    //            $this->setOptions($newValues, $autoload);
    //        }
    //    }

    //    public function setOptions(array $values, ?bool $autoload = null): void
    //    {
    //        $autoload ??= $this->getOption('autoload');
    //
    //        try
    //        {
    //            foreach ($values as $name => $value)
    //            {
    //                $option               = $this->findOneBy(['name' => $name, 'user' => null]) ?? new Option();
    //                $this->getEntityManager()->persist(
    //                    $option
    //                        ->setAutoload($autoload)
    //                        ->setName($name)
    //                        ->setValue($value)
    //                );
    //
    //                self::$options[$name] = $value;
    //            }
    //        } finally
    //        {
    //            $this->getEntityManager()->flush();
    //        }
    //    }

    //    public function setOption(string $name, mixed $value, ?bool $autoload = null): void
    //    {
    //        $this->setOptions([$name => $value], $autoload);
    //    }

    //    public function deleteOption(string $name): void
    //    {
    //        if ($this->hasOption($name))
    //        {
    //            $this->getEntityManager()
    //                ->createQueryBuilder()
    //                ->delete(Option::class, 'o')
    //                ->where('o.name = :name')
    //                ->setParameter('name', $name)
    //                ->getQuery()->getResult()
    //            ;
    //        }
    //
    //        unset(self::$options[$name], self::$counts[$name]);
    //    }
}
