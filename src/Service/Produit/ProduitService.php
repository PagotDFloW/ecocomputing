<?php

namespace App\Service\Produit;
use App\Entity\User;
use App\Entity\Favoris;
use App\Entity\Produits;
use App\Repository\ProduitsRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\FavorisRepository;


class ProduitService
{

    protected $productRepo;

    public function __construct(ProduitsRepository $productRepo, FavorisRepository $favRepo, EntityManagerInterface $manager) {
        $this->productRepo = $productRepo;
        $this->favRepo = $favRepo;
        $this->manager = $manager;
    }

    public function getPromoOnProduct(int $id) {
        $dateNow = date_format(new \DateTime(), 'Y/m/d');

        $promotion = null;

        $produit = $this->productRepo->findOneBy(['id' => $id]);

        foreach ($produit->getPromotions() as $promo) {
            if (date_format($promo->getEndAt(), 'Y/m/d') >= $dateNow && date_format($promo->getStartAt(), 'Y/m/d') <= $dateNow) {
                $promotion = $promo;
            }
        }

        return $promotion;
    }



    public function addProductToFavorites(int $id, User $user) {
        $produit = $this->productRepo->findOneBy(['id' => $id]);

        $favoris = new Favoris();
        $favoris->setProduct($produit);
        $favoris->setUser($user);
        $favoris->setDateTime(new \DateTime());

        $this->manager->persist($favoris);
        $this->manager->flush();

        return true;

    }

}