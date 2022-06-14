<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PromotionsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Promotions;
use App\Form\PromoType;
use Symfony\Component\Validator\Constraints\DateTime;


class PromoController extends AbstractController
{
    /**
     * @Route("/admin/promotions", name="admin_promos")
     */
    public function indexPromos(PromotionsRepository $repo) {

        // $promosEnCours = $
        $allPromos = $repo->findBy([], ['id' => 'DESC']);

        return $this->render('back/promos/indexPromos.html.twig', [
            'allPromos' => $allPromos
        ]);
    }


    /**
     * @Route("/admin/promotion/new", name="admin_new_promo")
     */
    public function createPromotion(EntityManagerInterface $manager, Request $request) {
        $promo = new Promotions();
        $promo->setStartAt(new \DateTime);

        $form = $this->createForm(PromoType::class, $promo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // dd($promo->getStartAt(), $promo->getEndAt());
            $manager->persist($promo);
            $manager->flush();

            $this->addFlash('success', 'La promotion a bien été enregistrée et sera appliquée au temps fixé.');
            return $this->redirectToRoute('admin_promos');
        }

        return $this->render('back/promos/editionPromo.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/admin/promotions/{id}/edit", name="admin_edit_promo")
     */
    public function editPromotion(Promotions $promo, EntityManagerInterface $manager, Request $request) {
        
        $form = $this->createForm(PromoType::class, $promo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($promo);
            $manager->flush();

            return $this->redirectToRoute('admin_promos');
        }

        return $this->render('back/promos/editionPromo.html.twig', [
            'form' => $form->createView(),
            'promo' => $promo
        ]);
    }


    /**
     * @Route("/admin/promotion/{id}/delete", name="admin_delete_promo")
     */
    public function deletePromotion(Promotions $promo, EntityManagerInterface $manager) {

        $categorie = $promo->getCategory();
        if ($categorie !== null) {
            $categorie->removePromotion($promo);
        }

        $produit = $promo->getProduit();
        if ($produit !== null) {
            $produit->removePromotion($promo);
        }

        $deletePromo = $manager->createQuery('DELETE FROM App\Entity\Promotions p WHERE p.id = ' . $promo->getId());
        $deletePromo = $deletePromo->getResult();

        return $this->redirectToRoute('admin_promos');
    }

    /**
     * @Route("/promotions", name="promos")
     */
    public function showPromotions(PromotionsRepository $repo) {
        $now = new \DateTime();
        $now = $now->format('Y-m-d');

        $promotions = $repo->findBy([], ['startAt' => 'ASC']);

        $enCours = [];
        foreach ($promotions as $promo) {
            $start = $promo->getStartAt();
            $start = $start->format('Y-m-d');
            
            $end = $promo->getEndAt();
            $end = $end->format('Y-m-d');

            if ($end >= $now && $start <= $now) {
                $enCours[] = $promo;
            }
        }

        return $this->render('home/promotions.html.twig', [
            'promotions' => $enCours
        ]);
    }
}
