<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Mobile_Detect;
use App\Entity\Produits;
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
    public function showProduct(Produits $product, ProduitsRepository $repo) {


        if ($product->getCategorie() !== null) {
            $categoryProducts = $repo->createQueryBuilder('p');
            $categoryProducts->select('p')
                             ->where('p.categorie = :category')->setParameter('category', $product->getCategorie()->getId())
                             ->andwhere('p.id != :id')->setParameter('id', $product->getId())
                             ->setMaxResults(5);
            $categoryProducts = $categoryProducts->getQuery()->getResult();
        }


        return $this->render('produits/product.html.twig', [
            'product' => $product,
            'categoryProducts' => (isset($categoryProducts) && !empty($categoryProducts)) ? $categoryProducts : null
        ]);
    }
}
