<?php

namespace App\Service\Produit;
use App\Entity\Produits;
use App\Repository\ProduitsRepository;


class ProduitService
{

    protected $productRepo;

    public function __construct(ProduitsRepository $productRepo) {
        $this->productRepo = $productRepo;
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

}