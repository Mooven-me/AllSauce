<?php

namespace App\Repository;

use App\Entity\SauceRoom;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SauceRoom>
 */
class SauceRoomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SauceRoom::class);
    }

    public function findOneBySlugAndCode(string $slug, string $roomCode): ?SauceRoom
    {
        return $this->createQueryBuilder('r')
            ->innerJoin('r.sauce', 's')
            ->andWhere('s.slug = :slug')
            ->andWhere('r.roomCode = :roomCode')
            ->setParameter('slug', $slug)
            ->setParameter('roomCode', $roomCode)
            ->getQuery()
            ->getOneOrNullResult();
    }

//    /**
//     * @return SauceRoom[] Returns an array of SauceRoom objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?SauceRoom
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
