<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Mobile_Detect;
use Proxies\__CG__\App\Entity\Produits;
use App\Repository\ProduitsRepository;
class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        $mobileDetector = new Mobile_Detect;
        $detect = ($mobileDetector->isMobile() || $mobileDetector -> isTablet()) ? true : false;

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'detect' => $detect
        ]);
    }


    /**
     * @Route("/produit/{id}", name="show_produit")
     */
    public function showProduct(int $id, ProduitsRepository $repo) {

        $product = $repo->findOneBy(['id' => $id]);

        return $this->render('produits/product.html.twig', [
            'product' => $product
        ]);
    }
}
