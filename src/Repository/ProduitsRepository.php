<?php

namespace App\Repository;

use App\Entity\Categories;
use App\Entity\Produits;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Produits|null find($id, $lockMode = null, $lockVersion = null)
 * @method Produits|null findOneBy(array $criteria, array $orderBy = null)
 * @method Produits[]    findAll()
 * @method Produits[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produits::class);
    }

    //get product join category
    public function getProductsWithFilter($filter)
    {
        $qb = $this->createQueryBuilder('p')
            ->select('p')
            ->innerJoin(
                'App\Entity\Categories',
                'c',
                'WITH',
                'p.categorie = c.id'
            );
        if(!empty($filter['search'])){
            $qb->andWhere('p.name LIKE :search')
                ->setParameter('search', '%'.$filter['search'].'%');
        }
        if(!empty($filter['category'])){
            foreach ($filter['category'] as $key => $value) {
                $qb->andWhere('c.name = :category'.$key)
                    ->setParameter('category'.$key, $value);
            }
        }
        if(!empty($filter['state'])){
            foreach ($filter['state'] as $key => $value) {
                $qb->andWhere('p.produitCondition = :state'.$key)
                    ->setParameter('state'.$key, $value);
            }
        }
        if(!empty($filter['minPrice'] && $filter['maxPrice'])){
            $qb->andWhere('p.prix BETWEEN :min AND :max')
                ->setParameter('min', $filter['minPrice'])
                ->setParameter('max', $filter['maxPrice']);
        }else{
            if(!empty($filter['minPrice'])){
                $qb->andWhere('p.prix >= :prix')
                    ->setParameter('prix', $filter['minPrice']);
            }
            if(!empty($filter['maxPrice'])){
                $qb->andWhere('p.prix <= :prix')
                    ->setParameter('prix', $filter['maxPrice']);
            }
        }
        if(!empty($filter['sortBy'])){
            if ($filter['sortBy'] == 'newest'){
                $qb->orderBy('p.createdAt', 'DESC');
            }else{
                $qb->orderBy('p.prix', $filter['sortBy'] == 'min' ? 'ASC' : 'DESC');
            }
        }
        return $qb->getQuery()->getResult();
    }

    // /**
    //  * @return Produits[] Returns an array of Produits objects
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
    public function findOneBySomeField($value): ?Produits
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
