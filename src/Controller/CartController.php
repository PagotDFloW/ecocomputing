<?php

namespace App\Controller;

use App\Entity\Commandes;
use App\Form\CommandeType;
use App\Security\EmailVerifier;
use App\Entity\ProduitsCommande;
use App\Service\Cart\CartService;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\Session;
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
    public function changeQuantity($id, CartService $cartService) {
        $cartService->addProduct($id);
        $this->addFlash('success', 'Le produit a bien été ajouté au panier');

        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("/panier/add-service/{id}", name="cart_add_service")
     * @IsGranted("ROLE_USER")
     */
    public function addServiceToCart($id, CartService $cartService)
    {
        $cartService->addService($id);

        return $this->redirectToRoute('cart');
    }


    /**
     * @Route("/panier/remove/{id}", name="cart_remove")
     */
    public function removeFromCart($id, CartService $cartService)
    {
        $cartService->removeProduct($id);
        $this->addFlash('success', 'Le produit a bien été supprimé du panier');

        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("/panier/remove-service/{id}", name="cart_remove_service")
     */
    public function removeServiceFromCart($id, CartService $cartService)
    {
        $cartService->removeService($id);

        return $this->redirectToRoute('cart');
    }



    /**
     * @Route("/panier/valider", name="cart_validate")
     * @IsGranted("ROLE_USER")
     */
    public function validateCart(CartService $cartService, Session $session, MailerInterface $mailer, EntityManagerInterface $manager, Request $request) {
        $commande = new Commandes();
        $commande->setUser($this->getUser());

        $cart = $cartService->getFullCartWithData();

        foreach ($cart['products'] as $data) {
            $produit = new ProduitsCommande();
            $produit->setCommande($commande);
            $produit->setProduit($data['product']);
            $produit->setQuantity($data['quantity']);

            $commande->addProduit($produit);
            $manager->persist($produit);
        }

        foreach ($cart['services'] as $data) {
            $data['service']->setCommande($commande);
            $manager->persist($data['service']);
        }

        $commande->setTotal($cartService->getTotal());


        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commande->setDatetime(new \DateTime);
            $commande->setStatus("payée");


            $manager->persist($commande);
            $manager->flush();

            // supprimer le panier de la session
            $session->remove('panierServices');
            $session->remove('panier');

            $email = (new TemplatedEmail())
                ->from(new Address('service@ecocomputing.com', 'Team ECO COMPUTING'))
                ->to($this->getUser()->getEmail())
                ->subject('Confirmation de votre commande')

                // path of the Twig template to render
                ->htmlTemplate('cart/cart_confirmation_email.html.twig')

                // pass variables (name => value) to the template
                ->context([
                    'commande' => $commande,
                ])
            ;

            // dd($email->getContext()['commande']);
            $mailer->send($email);


            // envoyer d'un second mail avec le devis si un service se trouve dans le panier


            // return $this->render('cart/cartValidation.html.twig', ['commande' => $commande]);
            return $this->redirectToRoute('order_checkout', ["id" => $commande->getId()]);
        }

        return $this->render('cart/payment.html.twig', [
            'form' => $form->createView(),
            'commande' => $commande
        ]);
    }
}
