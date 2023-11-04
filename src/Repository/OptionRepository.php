<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Option;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

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

    private static array $has     = [];

    public function __construct(ManagerRegistry $registry, private readonly LoggerInterface $logger)
    {
        parent::__construct($registry, Option::class);
        $this->initialize();
    }

    /**
     * Set up a new Option.
     */
    public function setUpOption(Option $option): bool
    {
        if (null === $option->getId() && ! $this->hasOption($option))
        {
            if (empty($option->getName()))
            {
                $this->logger->warning('Cannot set up Option, name is empty');

                return false;
            }

            $this->getEntityManager()->persist($option);
            $this->getEntityManager()->flush();
        }

        if (is_int($option->getId()))
        {
            self::$options[$option->getName()] = $option;
            self::$has[$option->getName()]     = true;
            return true;
        }

        return $this->hasOption($option->getName());
    }

    /**
     * Checks if Option exists.
     */
    public function hasOption(Option|string $name): bool
    {
        $name = $this->getName($name);

        if ( ! isset(self::$has[$name]))
        {
            self::$has[$name] = (1 === $this->count(['name' => $name]));
        }

        return self::$has[$name];
    }

    /**
     * Get Value for an option.
     */
    public function getOption(Option|string $name, mixed $defaultValue = null): mixed
    {
        $name = $this->getName($name);

        if ($this->hasOption($name))
        {
            $entity = self::$options[$name] ??= $this->findOneBy(['name' => $name]);
            $value  = $entity->getValue();
        } elseif (null !== $value = value($defaultValue))
        {
            $this->addOption($name, $value);
        }
        return $value;
    }

    /**
     * Adds An Option if it does not exist.
     */
    public function addOption(Option|string $name, mixed $value, bool $autoload = true): void
    {
        if ($this->hasOption($name))
        {
            return;
        }

        $this->setOptions([$this->getName($name) => $value], $autoload);
    }

    public function setOption(Option|string $name, mixed $value, bool $autoload = true): void
    {
        $this->setOptions([$this->getName($name) => $value], $autoload);
    }

    /**
     * Set multiples options.
     */
    public function setOptions(array $options, bool $autoload = true): void
    {
        $added = [];

        foreach ($options as $name => $value)
        {
            $value   = value($value);

            if (null === $value)
            {
                $this->removeOption($name);
                continue;
            }

            $entity  = $this->getOptionEntity($name) ?? Option::new($name, $value);
            $entity->setAutoload($autoload);
            $added[] = $entity;
            $this->resetOption($name);
        }

        if ( ! empty($added))
        {
            foreach ($added as $option)
            {
                $this->getEntityManager()->persist($option);
            }

            $this->getEntityManager()->flush();
        }
    }

    public function removeOption(Option|string $name): void
    {
        $this->getEntityManager()
            ->createQueryBuilder()
            ->delete(Option::class, 'o')
            ->where('o.name = :name')
            ->setParameter('name', $name = $this->getName($name))
            ->getQuery()->execute()
        ;

        $this->resetOption($name);
    }

    /**
     * @return array<string,true>
     */
    public function getLoadedOptions(): array
    {
        return array_filter(self::$has, fn ($b) => true === $b);
    }

    /**
     * Autoload options.
     */
    private function initialize(): void
    {
        if (empty(self::$has))
        {
            $this->setUpOption(
                Option::new(
                    Option::AUTOLOAD,
                    true,
                    'Enable options autoload.'
                )->setAutoload(false)
            );

            foreach (Option::DEFAULT_OPTIONS as $args)
            {
                $this->setUpOption(
                    Option::new(...$args)
                );
            }

            if (true === $this->getOption('autoload'))
            {
                foreach ($this->findBy(['autoload' => true]) as $option)
                {
                    self::$options[$name = $option->getName()] = $option;
                    self::$has[$name]                          = true;
                }
            }
        }
    }

    private function getOptionEntity(Option|string $name): ?Option
    {
        $name = $this->getName($name);
        return self::$options[$name] ??= $this->findOneBy(['name' => $name]);
    }

    private function getName(Option|string $name): string
    {
        if ($name instanceof Option)
        {
            if (null === $name = $name->getName())
            {
                throw new \ValueError('$name is null');
            }

            return $name;
        }

        return $name;
    }

    private function resetOption(string $name): void
    {
        unset(self::$options[$name], self::$has[$name]);
    }
}
