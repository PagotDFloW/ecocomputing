<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\CommandesRepository;

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
}
