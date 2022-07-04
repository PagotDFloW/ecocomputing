<?php

namespace App\Controller;

use App\Entity\Commandes;
use App\Form\CommandeType;
use App\Entity\ProduitsCommande;
use App\Service\Cart\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    /**
     * @Route("/panier", name="cart")
     */
    public function index(CartService $cartService)
    {
        return $this->render('cart/index.html.twig', [
            'items' => $cartService->getFullCartWithData(),
            'total' => $cartService->getTotal(),
            'nbrItems' => $cartService->getNbrItemsInCart()
        ]);
    }


    /**
     * @Route("/panier/add/{id}", name="cart_add")
     */
    public function addToCart($id, CartService $cartService) {
        $cartService->add($id);

        return $this->redirectToRoute('cart');
    }


    /**
     * @Route("/panier/remove/{id}", name="cart_remove")
     */
    public function removeFromCart($id, CartService $cartService)
    {
        $cartService->remove($id);

        return $this->redirectToRoute('cart');
    }



    /**
     * @Route("/panier/valider", name="cart_validate")
     * @IsGranted("ROLE_USER")
     */
    public function validateCart(CartService $cartService, EntityManagerInterface $manager, Request $request) {
        $commande = new Commandes();
        $commande->setUser($this->getUser());

        foreach ($cartService->getFullCartWithData() as $data) {
            $produit = new ProduitsCommande();
            $produit->setCommande($commande);
            $produit->setProduit($data['product']);
            $produit->setQuantity($data['quantity']);

            $commande->addProduit($produit);
        }

        $commande->setTotal($cartService->getTotal());


        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commande->setDatetime(new \DateTime);
            dd($form->getData());
        }

        return $this->render('cart/payment.html.twig', [
            'form' => $form->createView(),
            'commande' => $commande,
            'promotions' => $cartService->getFullCartWithData()
        ]);
    }
}
