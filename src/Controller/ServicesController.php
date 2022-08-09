<?php

namespace App\Controller;

use App\Entity\Services;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\ServiceType;
use App\Repository\ServicesRepository;
use App\Entity\DemandeService;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Service\Cart\CartService;

class ServicesController extends AbstractController
{


    /**
     * @Route("services", name="services")
     */
    public function getAllServices(ServicesRepository $repo) {
        return $this->render('services/allServices.html.twig', [
            'services' => $repo->findBy([], ['name' => 'ASC'])
        ]);
    }


    /**
     * @Route("admin/services", name="admin_services")
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(ServicesRepository $repo)
    {
        return $this->render('back/services/index.html.twig', [
            'services' => $repo->findBy([], ['name' => 'ASC']),
        ]);
    }


    /**
     * @Route("admin/services/new", name="admin_new_service")
     * @IsGranted("ROLE_ADMIN")
     */
    public function createService(EntityManagerInterface $manager, Request $request) {
        $service = new Services();

        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($service);
            $manager->flush();

            $this->addFlash('success-back-services', 'Le nouveau service a bien été ajouté à la liste des prestations proposées.');
            return $this->redirectToRoute('admin_edit_service', ['id' => $service->getId()]);
        }

        return $this->render('back/services/editionService.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("admin/services/{id}/edit", name="admin_edit_service")
     * @IsGranted("ROLE_ADMIN")
     */
    public function editService(Services $service, EntityManagerInterface $manager, Request $request) {

        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($service);
            $manager->flush();

            $this->addFlash('success-back-services', 'Le nouveau service a bien été ajouté à la liste des prestations proposées.');
            return $this->redirectToRoute('admin_edit_service', ['id' => $service->getId()]);
        }

        return $this->render('back/services/editionService.html.twig', [
            'form' => $form->createView(),
            'service' => $service
        ]);
    }


    /**
     * @Route("admin/services/{id}/delete", name="admin_delete_service")
     * @IsGranted("ROLE_ADMIN")
     */
    public function deleteService(Services $service, EntityManagerInterface $manager) {
        $name = $service->getName();

        $deleteService = $manager->createQuery('DELETE FROM App\Entity\Services s WHERE s.id = ' . $service->getId());
        $deleteService = $deleteService->getResult();

        $this->addFlash('success-admin-services', $name . " a bien été supprimé.");
        return $this->redirectToRoute('admin_services');
    }


    /**
     * @Route("services/{id}/faire-une-demande", name="services_demande")
     */
    public function requestService(Services $service, CartService $cartService, EntityManagerInterface $manager, Request $request) {
        $requestService = new DemandeService();
        $requestService->setClient($this->getUser());
        $requestService->setService($service);



        $form = $this->createFormBuilder($requestService)
        ->add('computer', ChoiceType::class, [
                'required' => true,
                'placeholder' => '-',
                'choices' => [
                    'Macbook Pro 2021' => 'Macbook Pro 2021',
                    'Macbook Pro 2020' => 'Macbook Pro 2020',
                    'Macbook Air 2021' => 'Macbook Air 2021',
                    'Macbook Air 2020' => 'Macbook Air 2020',
                ]
            ])
        ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($requestService);
            $manager->flush();
            
            $cartService->addService($requestService->getId());

            return $this->redirectToRoute('cart');
        }


        return $this->render('services/requestService.html.twig', [
            'service' => $service,
            'form' => $form->createView()
        ]);
    }
}
