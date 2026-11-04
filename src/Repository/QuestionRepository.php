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
}
