<?php

namespace App\Repository;

use App\Entity\LikesHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LikesHistory>
 *
 * @method LikesHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method LikesHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method LikesHistory[]    findAll()
 * @method LikesHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LikesHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LikesHistory::class);
    }

//    /**
//     * @return LikesHistory[] Returns an array of LikesHistory objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?LikesHistory
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
