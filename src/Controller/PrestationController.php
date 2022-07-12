<?php

namespace App\Controller;

use App\Entity\Prestations;
use App\Form\PrestationType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PrestationController extends AbstractController
{
    /**
     * @Route("/prestation", name="app_prestation")
     */
    public function index(): Response
    {
        return $this->render('prestation/index.html.twig', [
            'controller_name' => 'PrestationController',
        ]);
    }


    /**
     * @Route("/prestation/demander-une-prestation", name="new_prestation")
     * @IsGranted("ROLE_USER")
     */
    public function newPrestation()
    {
        $user = $this->getUser();
        $prestation = new Prestations();
        $prestation->setClient($user);

        $form = $this->createForm(PrestationType::class, $prestation);
        
        

        return $this->render('prestation/serviceRequest.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
