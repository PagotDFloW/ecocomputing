<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ServicesProductsController extends AbstractController
{
    /**
     * @Route("/services/products", name="app_services_products")
     */
    public function index(): Response
    {
        return $this->render('services_products/index.html.twig', [
            'controller_name' => 'ServicesProductsController',
        ]);
    }
}
