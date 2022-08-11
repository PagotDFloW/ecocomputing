<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Knp\Component\Pager\PaginatorInterface;
use App\Entity\User;

class UserController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        $this->addFlash('success','Déconnexion réussie.');
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    //get all user with pagination
    /**
     * @Route("/admin/users", name="admin_users")
     * @IsGranted("ROLE_ADMIN")
     */
    public function users(PaginatorInterface $paginator, Request $request)
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        $pagination = $paginator->paginate(
            $users, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            50/*limit per page*/
        );
        return $this->render('back/user/users.html.twig', [
            'users' => $pagination
        ]);
    }

    /**
     * @Route("/user/{id}/delete", name="admin_delete_user")
     * @IsGranted("ROLE_ADMIN")
     */
    public function deleteUser($id): Response
    {
        $user = $this->getDoctrine()->getRepository(\App\Entity\User::class)->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($user);
        $entityManager->flush();
        $this->addFlash('success','Utilisateur supprimé.');
        return $this->redirectToRoute('admin_users');
    }

}
