<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Entity\Quotes;
use App\Form\QuotesType;
use App\Entity\Commandes;
use App\Entity\Prestations;
use Konekt\PdfInvoice\InvoicePrinter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class QuoteController extends AbstractController
{
    /**
     * @Route("/quote", name="app_quote")
     */
    public function index(): Response
    {
        return $this->render('quote/index.html.twig', [
            'controller_name' => 'QuoteController',
        ]);
    }

    /**
     * @Route("/quote/{id}", name="app_quote_save")
     */
    public function save(EntityManagerInterface $manager, Commandes $commande)
    {
        $quote = new Quotes();

        $prestations = $commande->getPrestations();


        $quote->setUser($commande->getUser()->getId());   //logo image path

        // créer le PDF
        $invoice = new InvoicePrinter();
        $invoice->addTitle("Devis n°" . $quote->getId());
        $total = 0;
        foreach ($prestations as $prestation) {
            $total = $total + $prestation->getPrice();
            $invoice->addItem($prestation->getService(), null, 1, null, $prestation->getPrice(), null, $total);
        }
        $invoice->addTotal("Total", $total);
        $tva = $total * 20 / 100;

        $filename = md5(uniqid()) . '.pdf';
        $quote->setFilename($filename);
        $quote->setDate(new DateTime('now'));

        $manager->persist($quote);
        $manager->flush();

        $this->addFlash('message', 'Le devis à été créé');

        return $this->redirectToRoute('devis/{id}', ['id' => $quote->getId()]);
    }
}
