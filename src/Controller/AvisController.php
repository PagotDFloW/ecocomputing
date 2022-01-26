<?php

namespace App\Controller;

use App\Entity\Avis; 
use App\Form\AvisType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;



class AvisController extends AbstractController
{
    /**
     * @Route("/avis", name="avis")
     */
    public function index(): Response
    {
        $avis = $this->getDoctrine()->getRepository(Avis::class)->findAll(); 
        return $this->render('avis/index.html.twig', [
            'avis' => $avis,
        ]);
    }

    /**
     * @Route("/avis/single/{id}", name="singleAvis")
     */
    public function single(Avis $avis): Response
    {
        return $this->render('avis/single.html.twig', [
            'avis' => $avis
        ]); 
    }

    /**
     * @Route("/avis/save", name= "saveAvis", methods = {"POST", "GET"})
     */
    public function save(Request $request,  ManagerRegistry $doctrine): Response
    {
        $avis = new Avis; 
        $form = $this->createForm(AvisType::class, $avis); 
        $form->handleRequest($request); 
        if($form->isSubmitted() && $form->isValid())
        {
            $avis->setDate(new \DateTime()); 
            $em = $doctrine->getManager(); 
            $em->persist($avis); 
            $em->flush(); 

            $this->addFlash("success", "Votre avis a été envoyé !");

            return $this->redirectToRoute("saveAvis");
        }
        return $this->render("avis/save.html.twig", [
            'form' => $form->createView()
        ]); 
    }
}