<?php

namespace App\Controller;
use App\Form\ContactType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mime\Address;
use App\Entity\User;
use Symfony\Component\Mailer\MailerInterface;

class ContactController extends AbstractController
{
    /**
     * @Route("contact", name="contact")
     */
    public function index(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository, EntityManagerInterface $manager, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ContactType::class);
        $form ->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            //find user by role admin dql
            $userRepository = $manager->getRepository(User::class);
            $query = $userRepository->createQueryBuilder('u')
                ->where('u.roles LIKE :role')
                ->setParameter('role', '%"ROLE_ADMIN"%')
                ->getQuery();
            $admin = $query->getResult();
            $adminMail = $admin[1];
            $userInfo = $request->get('contact');
            $nom = $userInfo['Nom'];
            $prenom = $userInfo['Prenom'];
            $mail = $userInfo['Mail'];
            $sujet = $userInfo['Sujet'];
            $msg = $userInfo['Message'];

            $templatedEmail = new TemplatedEmail();
            $templatedEmail->from(new Address($mail, "$nom $prenom"));
            $templatedEmail->to($adminMail->getEmail());
            $templatedEmail->subject($sujet);
            $templatedEmail->htmlTemplate('contact/contactEmail.html.twig');

            $context = $templatedEmail->getContext();

            $context['Nom'] = $nom;
            $context['Prenom'] = $prenom;
            $context['Mail'] = $mail;
            $context['Sujet'] = $sujet;
            $context['Message'] = $msg;

            $templatedEmail ->context($context);


            $mailer ->send($templatedEmail);

            $this->addFlash("success", "Votre message a bien été envoyé !");
        }
        return $this->render("contact/contact.html.twig", [
            'form' => $form->createView()
        ]);
    }
}
?>