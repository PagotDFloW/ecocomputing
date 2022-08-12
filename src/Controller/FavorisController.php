<?php

namespace App\Controller;

use App\Entity\Favoris;
use App\Service\Produit\ProduitService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FavorisController extends AbstractController
{
    /**
     * @Route("/produits/favorites/add/{id}", name="products_add_to_favorites")
     */
    public function addToFavorites(ProduitService $produitService, Request $request) {
        if($produitService->addProductToFavorites($request->get('id'), $this->getUser())) {
            return new JsonResponse(['type' => "success", 'message' => 'Produit ajouté au favoris']);
        } else {
            return new JsonResponse(['type' => "error", 'message' => 'Produit déjà ajouté aux favoris']);
        }
    }


    /**
     * @Route("/produit/{id}/remove-from-favorites", name="remove_product_from_favorites")
     */
    public function removeFromFavorites(Favoris $favoris, EntityManagerInterface $manager) {
        $deleteFavoris = $manager->createQuery('DELETE FROM App\Entity\Favoris f WHERE f.id = ' . $favoris->getId());
        $deleteFavoris = $deleteFavoris->getResult();

        $this->addFlash('success', 'Le produit a bien été retiré de vos favoris');
        return $this->redirectToRoute('profile_favoris', ['id' => $this->getUser()->getId()]);
    }

}
