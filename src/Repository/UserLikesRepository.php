<?php

namespace App\Repository;

use App\Entity\UserLikes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserLikes>
 *
 * @method UserLikes|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserLikes|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserLikes[]    findAll()
 * @method UserLikes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserLikesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserLikes::class);
    }

//    /**
//     * @return UserLikes[] Returns an array of UserLikes objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?UserLikes
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
