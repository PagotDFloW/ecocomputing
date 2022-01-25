<?php

namespace App\Repository;

use App\Entity\ProduitImages;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProduitImages|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProduitImages|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProduitImages[]    findAll()
 * @method ProduitImages[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitImagesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProduitImages::class);
    }

    // /**
    //  * @return ProduitImages[] Returns an array of ProduitImages objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ProduitImages
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
