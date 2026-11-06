<?php

namespace App\Repository;

use App\Entity\Question;
use App\Entity\SauceRoom;
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

    public function deleteByManagerClass(string $class)
    {
        $this->getEntityManager()->createQueryBuilder()
            ->update(SauceRoom::class, 'sr')
            ->set('sr.currentQuestion', ':null')
            ->where('sr.currentQuestion IN (SELECT q.id FROM ' . Question::class . ' q WHERE q.managerClass = :managerClass)')
            ->setParameter('null', null)
            ->setParameter('managerClass', $class)
            ->getQuery()
            ->execute();

        return $this->createQueryBuilder('q')
            ->andWhere('q.managerClass = :managerClass')
            ->setParameter('managerClass', $class)
            ->delete()
            ->getQuery()
            ->execute();
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
