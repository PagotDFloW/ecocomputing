<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Entity\Commandes;
use App\Service\Cart\CartService;
use App\Repository\FavorisRepository;
use App\Repository\CommandesRepository;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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


    // /**
    //  * @Route("{id}/mes-favoris", name="favoris")
    //  */
    // public function getUserFavorites(FavorisRepository $repo, User $user) {
    //     dd($user);
    //     return $this->render('profile/profileFavorites.html.twig', [
    //         'favoris' => $repo->find(['user' => $user->getId()], ['dateTime' => 'DESC'])
    //     ]);
    // }


    /**
     * @Route("{id}/favoris", name="favoris")
     */
    public function getFavoris(User $user, FavorisRepository $FavorisRepository) {


        $favoris = $FavorisRepository->findBy(['user' => $this->getUser()->getId()], ['dateTime' => 'DESC']);


        return $this->render('profile/includes/favoritesList.html.twig', [
            'favoris' => $favoris
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

        foreach ($user->getCommandes() as $commande) {
            foreach ($commande->getProduits() as $produit) {
                $deleteProduit = $manager->createQuery('DELETE FROM App\Entity\ProduitsCommande c WHERE c.id = ' . $produit->getId());
                $deleteProduit = $deleteProduit->getResult();
            }
            $deleteCommande = $manager->createQuery('DELETE FROM App\Entity\Commandes c WHERE c.id = ' . $commande->getId());
            $deleteCommande = $deleteCommande->getResult();
        }


        foreach ($user->getFavoris() as $favoris) {
            $deleteFavoris = $manager->createQuery('DELETE FROM App\Entity\Favoris c WHERE c.id = ' . $favoris->getId());
            $deleteFavoris = $deleteFavoris->getResult();
        }

        $deleteUser = $manager->createQuery('DELETE FROM App\Entity\User u WHERE u.id = ' .$user->getId());
        $deleteUser = $deleteUser->getResult();

        return $this->redirectToRoute('home');
    }
}
