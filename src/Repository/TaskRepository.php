<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function findByFilters(?string $name, ?int $priority, ?string $state): array
    {
        $qb = $this->createQueryBuilder('t');

        if ($name) {
            $qb->andWhere('t.name LIKE :name')
                ->setParameter('name', '%'.$name.'%');
        }

        if ($priority) {
            $qb->andWhere('t.priority = :priority')
                ->setParameter('priority', $priority);
        }

        if ($state) {
            $qb->andWhere('t.state = :state')
                ->setParameter('state', $state);
        }

        $qb->orderBy('t.priority', 'ASC');

        return $qb->getQuery()->getResult();
    }

    //    /**
    //     * @return Task[] Returns an array of Task objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Task
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
