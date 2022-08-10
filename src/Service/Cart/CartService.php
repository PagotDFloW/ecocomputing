<?php

namespace App\Service\Cart;
use App\Repository\ProduitsRepository;
use App\Repository\ServicesRepository;
use App\Service\Produit\ProduitService;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\DemandeServiceRepository;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class CartService
{
    protected $session;
    protected $productRepo;
    protected $productService;

    public function __construct(SessionInterface $session, EntityManagerInterface $manager, DemandeServiceRepository $requestServiceRepo, ProduitsRepository $productRepo, ServicesRepository $serviceRepo, ProduitService $productService) {
        $this->session = $session;
        $this->manager = $manager;
        $this->productRepo = $productRepo;
        $this->serviceRepo = $serviceRepo;
        $this->productService = $productService;
        $this->requestServiceRepo = $requestServiceRepo;
    }


    public function addProduct($id) {
        $panier = $this->session->get('panier', []);

        if (!empty($panier[$id])) {
            $panier[$id]++;
        }
        else {
            $panier[$id] = 1;
        }

        $this->session->set('panier', $panier);
        return true;
    }

    public function addService($id) {
        $panierServices = $this->session->get('panierServices', []);

        $panierServices[$id] = 1;

        $this->session->set('panierServices', $panierServices);
    }


    public function removeProduct($id) {
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

    public function removeService($id) {
        $panier = $this->session->get('panierServices', []);

        unset($panier[$id]);

        $deleteServiceRequest = $this->manager->createQuery('DELETE FROM App\Entity\DemandeService ds WHERE ds.id = :id');
        $deleteServiceRequest->setParameter('id', $id);
        $deleteServiceRequest = $deleteServiceRequest->getResult();

        $this->session->set('panierServices', $panier);
    }


    public function getFullCartWithData(): array {
        $panier = $this->session->get('panier', []);
        $panierServices = $this->session->get('panierServices', []);
        
        $panierWithData = [];
        $panierServicesData = [];

        // products
        foreach ($panier as $id => $quantity) {
            $panierWithData[] = [
                'product' => $this->productRepo->findOneBy(['id' => $id]),
                'quantity' => $quantity,
                'promotion' => $this->productService->getPromoOnProduct($id)
            ];
        }

        // services
        foreach ($panierServices as $id => $quantity) {
            $panierServicesData[] = [
                'service' => $this->requestServiceRepo->findOneBy(['id' => $id])
            ];
        }


        return ["products" => $panierWithData, "services" => $panierServicesData];
    }


    public function getTotal() : float {
        $total = 0;

        // products
        foreach ($this->getFullCartWithData()['products'] as $data) {
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

        // services
        foreach ($this->getFullCartWithData()['services'] as $data) {
            $total += $data['service']->getService()->getPrice();
        }
        
        return $total;
    }


    public function getNbrItemsInCart() : int {
        $nbrItems = 0;

        // products
        foreach ($this->getFullCartWithData()['products'] as $cart) {
            $nbrItems += $cart['quantity'];
        }

        // services
        foreach ($this->getFullCartWithData()['services'] as $cart) {
            $nbrItems += 1;
        }

        return $nbrItems;
    }
};