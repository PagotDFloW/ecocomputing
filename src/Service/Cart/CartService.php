<?php

namespace App\Service\Cart;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Repository\ProduitsRepository;
use Symfony\Component\Validator\Constraints\DateTime;
use App\Service\Produit\ProduitService;


class CartService
{
    protected $session;
    protected $productRepo;
    protected $productService;

    public function __construct(SessionInterface $session, ProduitsRepository $productRepo, ProduitService $productService) {
        $this->session = $session;
        $this->productRepo = $productRepo;
        $this->productService = $productService;
    }


    public function add($id) {
        $panier = $this->session->get('panier', []);

        if (!empty($panier[$id])) {
            $panier[$id]++;
        }
        else {
            $panier[$id] = 1;
        }

        $this->session->set('panier', $panier);
    }


    public function remove($id) {
        $panier = $this->session->get('panier', []);

        if (!empty($panier[$id])) {
            if ($panier[$id] > 1) {
                $panier[$id] --;
            }
            else {
                unset($panier[$id]);
            }
        }


        $this->session->set('panier', $panier);
    }


    public function getFullCartWithData(): array {
        $panier = $this->session->get('panier', []);

        $panierWithData = [];

        foreach ($panier as $id => $quantity) {
            $panierWithData[] = [
                'product' => $this->productRepo->findOneBy(['id' => $id]),
                'quantity' => $quantity,
                'promotion' => $this->productService->getPromoOnProduct($id)
            ];
        }

        return $panierWithData;
    }


    public function getTotal() : float {
        $total = 0;
        foreach ($this->getFullCartWithData() as $data) {

            $promoPercentage = null;
            $now = new \DateTime();
            $now = date_format($now, "Y/m/d");

            foreach ($data['product']->getPromotions() as $promo) {
                if (date_format($promo->getEndAt(), "Y/m/d") >= $now && date_format($promo->getStartAt(), "Y/m/d") <= $now) {
                    $promoPercentage = $promo->getReduction();
                }
            }

            if ($promoPercentage !== null) {
                $newPrice = $data['product']->getPrix() - ($promoPercentage * $data['product']->getPrix() / 100);
                $total += $newPrice * $data['quantity'];
            }
            else {
                $total += $data['product']->getPrix() * $data['quantity'];
            }
        }

        return $total;
    }


    public function getNbrItemsInCart() : int {
        $nbrItems = 0;

        foreach ($this->getFullCartWithData() as $cart) {
            $nbrItems += $cart['quantity'];
        }

        return $nbrItems;
    }
};