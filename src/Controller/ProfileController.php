<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\CommandesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Form\UserType;
use App\Entity\Commandes;
use App\Service\Cart\CartService;

/**
 * @Route("profile/", name="profile_")
 * @IsGranted("ROLE_USER")
 */
class ProfileController extends AbstractController
{
    /**
     * @Route("{id}", name="user")
     */
    public function index(User $user)
    {
        return $this->render('profile/index.html.twig', [
            'controller_name' => 'ProfileController',
        ]);
    }


    /**
     * @Route("{id}/mes-commandes", name="commandes")
     */
    public function getUserCommandes(User $user, CommandesRepository $repo)
    {
        return $this->render('profile/profileCommandes.html.twig', [
            'commandes' => $repo->findBy(['user' => $user], ['datetime' => 'DESC'])
        ]);
    }


    /**
     * @Route("{user}/ma-commande/{commande}", name="commande_detail")
     */
    public function getUserCommandeDetail(User $user, Commandes $commande, CartService $cartService) {
        return $this->render('profile/commandeDetail.html.twig', [
            'user' => $user,
            'commande' => $commande
        ]);
    }


    /**
     * @Route("{id}/modifier-mon-profil", name="edit")
     */
    public function editProfile(User $user, EntityManagerInterface $manager, Request $request) {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($user);
            $manager->flush();

            $this->addFlash('success', 'Vos modifications ont bien été enregistrées !');
            return $this->redirectToRoute('profile_edit', ['id' => $user->getId()]);
        }

        return $this->render('profile/editProfile.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }



    /**
     * @Route("{id}/supprimer-mon-profil", name="delete")
     */
    public function deleteAccount(User $user, EntityManagerInterface $manager, Request $request) {

    }
}
