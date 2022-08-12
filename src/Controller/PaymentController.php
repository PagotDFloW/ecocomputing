<?php

namespace App\Controller;

use Stripe\Charge;
use Stripe\Stripe;
use App\Entity\Commandes;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PaymentController extends AbstractController
{
    /**
     * @Route("/payment", name="app_payment")
     */
    public function index(): Response
    {
        return $this->render('payment/index.html.twig', [
            'controller_name' => 'PaymentController',
        ]);
    }

    /**
     * @Route("/checkout/{id}", name="order_checkout")
     * @IsGranted("ROLE_USER")
     */
    public function checkoutAction($id, Request $request)
    {
        $commande = $this->getDoctrine()->getRepository(Commandes::class)->find($id);
        $total = $commande->getTotal() * 100;
        $products = $commande->getProduits();
        dump($request->get('stripeToken'));

        $token = $request->get('stripeToken');

        Stripe::setApiKey("sk_test_51LVYdyJqbezmWdjP8Jl1SvwJ8ThIvSBILSNyRQkhRliSaH72qTnwrVGUlGNGP3vXpfsDgmVlhxTctvZqNO8tSJRI00woXfhWE6");
        Charge::create(array(
            "amount" => $total,
            "currency" => "eur",
            "source" => 'tok_mastercard',
            "description" => "first"
        ));
        if ($token) {
            return $this->render('cart/cartValidation.html.twig', ['commande' => $commande]);
        } else {
            return  $this->render('payment/checkout.html.twig', ['total' => $total, 'commande' => $commande]);
        }
    }
}
