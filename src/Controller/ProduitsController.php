<?php

namespace App\Controller;

use App\Entity\Produits;
use App\Form\ProduitType;
use App\Repository\ProduitsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/admin", name="admin_")
 */
class ProduitsController extends AbstractController
{
    /**
     * @Route("/produits", name="produits")
     */
    public function index(ProduitsRepository $repo): Response
    {
        $produits = $repo->createQueryBuilder('p');
        $produits->select('p')
                 ->where('p.statut = :original')
                 ->orwhere('p.statut = :achete')
                 ->setParameters(['original' => "original", 'achete' => "acheté"]);
        $produits = $produits->getQuery()->getResult();

        return $this->render('produits/index.html.twig', [
            'controller_name' => 'ProduitsController',
            'produits' => $produits
        ]);
    }


    /**
     * @Route("/produit/new", name="new_produit")
     */
    public function createProduct(EntityManagerInterface $manager, Request $request) {

        $produit = new Produits();

        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Gestion des images
            if ($form->get('image1')->getData() !== null) {
                $file = $form->get('image1')->getData();
                $filename = md5(uniqid()) . '.' . $file->guessExtension();
                $file->move($this->getParameter('produits_images_directory'), $filename);
                $produit->setImage1($filename);
            }
            if ($form->get('image2')->getData() !== null) {
                $file = $form->get('image2')->getData();
                $filename = md5(uniqid()) . '.' . $file->guessExtension();
                $file->move($this->getParameter('produits_images_directory'), $filename);
                $produit->setImage2($filename);
            }
            if ($form->get('image3')->getData() !== null) {
                $file = $form->get('image3')->getData();
                $filename = md5(uniqid()) . '.' . $file->guessExtension();
                $file->move($this->getParameter('produits_images_directory'), $filename);
                $produit->setImage3($filename);
            }

            $produit->setStatut("original");
            $produit->setProduitCondition("neuf");

            $manager->persist($produit);
            $manager->flush();

            $this->addFlash('message', 'Le produit a bien été enregistré');
            return $this->redirectToRoute('admin_produits');
        }

        return $this->render('produits/createProduct.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
