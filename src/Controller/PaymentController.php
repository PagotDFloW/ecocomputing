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
        // $form = $this->createForm(PaymentType::class);
        // $form->handleRequest($request);
        // if ($form->isSubmitted() && $form->isValid()) {
        //     $token = $request->request->get('stripeToken');
        //     dump($token);
        dump($request->get('stripeToken'));

        // \Stripe\Stripe::setApiKey("sk_test_51LVYdyJqbezmWdjP8Jl1SvwJ8ThIvSBILSNyRQkhRliSaH72qTnwrVGUlGNGP3vXpfsDgmVlhxTctvZqNO8tSJRI00woXfhWE6");
        // \Stripe\Charge::create([
        //     'amount' => $total,
        //     'currency' => 'eur',
        //     'description' => 'Premium blog access',
        //     'source' => $request->get('stripeToken'),
        //     // 'receipt_email' => $user->getEmail(),
        // ]);
        // $charge = $stripe->charges->create(array(
        //     "amount" => $commande->getPrix(),
        //     "currency" => "eur",
        //     "source" => $request->get('stripeToken'),
        //     "description" => "Charge for hshshsh"
        // ));

        // dump(intval($commande->getTotal()));

        $token = $request->get('stripeToken');

        Stripe::setApiKey("sk_test_51LVYdyJqbezmWdjP8Jl1SvwJ8ThIvSBILSNyRQkhRliSaH72qTnwrVGUlGNGP3vXpfsDgmVlhxTctvZqNO8tSJRI00woXfhWE6");
        Charge::create(array(
            "amount" => $total,
            "currency" => "eur",
            "source" => 'tok_mastercard',
            "description" => "first"
        ));
        return  $this->render('payment/checkout.html.twig', ['total' => $total, 'commande' => $commande]);

        // }
        // return $this->render('cart/payment.html.twig', [
        //     'form' => $this->createForm(PaymentType::class),
        //     'commande' => $commande
        // ]);
    }
}
