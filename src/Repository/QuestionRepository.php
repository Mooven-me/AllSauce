<?php

namespace App\Repository;

use App\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Question>
 */
class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Question::class);
    }

    public function deleteByManagerClass(string $class){
        $query = $this->createQueryBuilder('q')
                ->andWhere('q.managerClass = :managerClass')
                ->setParameter('type', $class)
                ->delete()
                ->getQuery()
                ->execute();
        return $query;
    }

    public function findOneRandomly() : Question {
        $managerClass = $this->createQueryBuilder('q')
                ->select('q.managerClass')
                ->groupBy('q.managerClass')
                ->orderBy('RANDOM()')
                ->setMaxResults(1)
                ->getQuery()
                ->getSingleScalarResult();
        
        return $this->createQueryBuilder('q')
                ->orderBy('RANDOM()')
                ->andWhere('q.managerClass = :managerClass')
                ->setParameter('managerClass', $managerClass)
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

    }
}
