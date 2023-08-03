<?php

namespace App\Repository;

use App\Entity\RegisteredProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RegisteredProduct>
 *
 * @method RegisteredProduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method RegisteredProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method RegisteredProduct[]    findAll()
 * @method RegisteredProduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RegisteredProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RegisteredProduct::class);
    }

//    /**
//     * @return RegisteredProduct[] Returns an array of RegisteredProduct objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?RegisteredProduct
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
