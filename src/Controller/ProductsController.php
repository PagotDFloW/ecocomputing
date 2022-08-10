<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Entity\Produits;
use App\Entity\User;
use App\Form\ProduitType;
use App\Form\EditProduitType;
use App\Repository\CategoriesRepository;
use App\Repository\ProduitsRepository;
use App\Service\Cart\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


class ProductsController extends AbstractController
{
    /**
     * @Route("/admin/produits", name="admin_produits")
     * @IsGranted("ROLE_ADMIN")
     */
    public function adminListeProduits(ProduitsRepository $repo): Response
    {
        $produits = $repo->createQueryBuilder('p');
        $produits->select('p')
                 ->where('p.statut = :original')
                 ->orwhere('p.statut = :achete')
                 ->orderBy('p.createdAt', 'DESC')
                 ->setParameters(['original' => "original", 'achete' => "acheté"]);
        $produits = $produits->getQuery()->getResult();

        return $this->render('produits/index.html.twig', [
            'controller_name' => 'ProduitsController',
            'produits' => $produits
        ]);
    }


    /**
     * @Route("/admin/produit/new", name="admin_new_produit")
     * @IsGranted("ROLE_ADMIN")
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

        return $this->render('produits/editionProduct.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/produit/{id}/edit", name="admin_edit_produit")
     * @IsGranted("ROLE_ADMIN")
     */
    public function editProduct(Produits $produit, EntityManagerInterface $manager, Request $request) {
        $image1 = $produit->getImage1();
        $image2 = $produit->getImage2();
        $image3 = $produit->getImage3();

        $form = $this->createForm(EditProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('image1')->getData() !== null) {
                $file = $form->get('image1')->getData();
                $filename = md5(uniqid()) . '.' . $file->guessExtension();
                $file->move($this->getParameter('produits_images_directory'), $filename);

                if ($image1 !== null) {
                    unlink(__DIR__.'/../../public/uploads/produits/'. $image1);
                }

                $produit->setImage1($filename);
            }
            if ($form->get('image2')->getData() !== null) {
                $file = $form->get('image2')->getData();
                $filename = md5(uniqid()) . '.' . $file->guessExtension();
                $file->move($this->getParameter('produits_images_directory'), $filename);

                if ($image2 !== null) {
                    unlink(__DIR__.'/../../public/uploads/produits/'. $image2);
                }

                $produit->setImage2($filename);
            }
            if ($form->get('image3')->getData() !== null) {
                $file = $form->get('image3')->getData();
                $filename = md5(uniqid()) . '.' . $file->guessExtension();
                $file->move($this->getParameter('produits_images_directory'), $filename);

                if ($image3 !== null) {
                    unlink(__DIR__.'/../../public/uploads/produits/'. $image3);
                }

                $produit->setImage3($filename);
            }

            $manager->persist($produit);
            $manager->flush();

            $this->addFlash('message', 'Le produit a bien été enregistré');
            return $this->redirectToRoute('admin_produits');
        }

        return $this->render('produits/editionProduct.html.twig', [
            'form' => $form->createView(),
            'produit' => $produit,
            'edition' => true
        ]);
    }



    /**
     * @Route("/admin/produit/{id}/delete", name="admin_delete_produit")
     * @IsGranted("ROLE_ADMIN")
     */
    public function deleteProduit(Produits $produit, EntityManagerInterface $manager, Request $request) {
        $image1 = $produit->getImage1();
        $image2 = $produit->getImage2();
        $image3 = $produit->getImage3();

        if ($image1 !== null) {
            if (file_exists(__DIR__.'/../../public/uploads/produits/'. $image1)) {
                unlink(__DIR__.'/../../public/uploads/produits/'.$image1);
            }
        }
        if ($image1 !== null) {
            if (file_exists(__DIR__.'/../../public/uploads/produits/'. $image2)) {
                unlink(__DIR__.'/../../public/uploads/produits/'.$image2);
            }
        }
        if ($image1 !== null) {
            if (file_exists(__DIR__.'/../../public/uploads/produits/'. $image3)) {
                unlink(__DIR__.'/../../public/uploads/produits/'.$image3);
            }
        }

        $query = $manager->createQuery('DELETE FROM App\Entity\Produits p WHERE p.id = :id');
        $query->setParameter('id', $produit->getId());

        $query = $query->getResult();

        return $this->redirectToRoute('admin_produits');
    }



    /**
     * @Route("/admin/produit/{id}/{photoname}/delete-photo", name="admin_delete_product_photo")
     * @IsGranted("ROLE_ADMIN")
     */
    public function deleteProductPhoto(Produits $produit, string $photoname, EntityManagerInterface $manager, Request $request) {

        if ($photoname === $produit->getImage1()) {
            if (file_exists(__DIR__.'/../../public/uploads/produits/'. $photoname)) {
                unlink(__DIR__.'/../../public/uploads/produits/'. $photoname);
            }
            $produit->setImage1(null);
        }
        elseif ($photoname === $produit->getImage2()) {
            if (file_exists(__DIR__.'/../../public/uploads/produits/'. $photoname)) {
                unlink(__DIR__.'/../../public/uploads/produits/'. $photoname);
            }
            $produit->setImage2(null);
        }
        elseif ($photoname === $produit->getImage3()) {
            if (file_exists(__DIR__.'/../../public/uploads/produits/'. $photoname)) {
                unlink(__DIR__.'/../../public/uploads/produits/'. $photoname);
            }
            $produit->setImage3(null);
        }

        $manager->persist($produit);
        $manager->flush();

        return $this->redirectToRoute('admin_edit_produit', ['id' => $produit->getId()]);
    }

    /**
     * @Route("/admin/produits/script", name="admin_script_produits")
     */
    public function scriptCreateProduct(EntityManagerInterface $manager, Request $request, CategoriesRepository $categoriesRepository) {
        $i = 0;
        while ($i < 10) {
            $newProduit = new Produits();
            $newProduit->setName('Produit '.$i);
            $newProduit->setCategorie($categoriesRepository->find(rand(1, 6)));
            $newProduit->setPrix(rand(10, 100));
            $newProduit->setDescription('Produit '.$i.' de la catégorie 3');
            $newProduit->setImage1('dcba2d9bf69e25372ab4a65ccd31f580.jpg');
            $newProduit->setStatut('original');
            $newProduit->setProduitCondition('utilisé');

            $manager->persist($newProduit);
            $manager->flush();
            $i++;
        }
        exit('Done');
    }



    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


    /**
     * @Route("/produits", name="produits")
     */
    public function getProduits(ProduitsRepository $produitsRepository, CategoriesRepository $categoriesRepository,Request $request, PaginatorInterface $paginator, EntityManagerInterface $manager) {
        $productPerCategory = [];
        $categories = $categoriesRepository->findAll();
        foreach ($categories as $category) {
            $productPerCategory[$category->getName()] = count($produitsRepository->findBy(['categorie' => $category], ['id' => 'DESC'], 3));
        }

        $products = $produitsRepository->findAll();
        $products = $paginator->paginate(
            $products,
            $request->query->getInt('page', 1),
            12
        );


        return $this->render('produits/allProducts.html.twig', [
            'categories' => $productPerCategory,
            'produits' => $products
        ]);
    }

    /**
     * @Route("/produits/list", name="products_list")
     */
     public function getProductList(ProduitsRepository $produitsRepository, Request $request, PaginatorInterface $paginator) {
         $page = ($request->get('page')) != ""? intval($request->get('page') ): 1;
         $filter = $request->get('filter');
         //get product sorted by price
         if(isset($filter)){
             $products = $produitsRepository->getProductsWithFilter($filter);
         }
         else{
             $products = $produitsRepository->findAll();
         }


        $products = $paginator->paginate(
            $products,
            $page,
            12
        );
        return $this->render('produits/list/productList.html.twig', [
            'produits' => $products
        ]);
     }

    /**
     * @Route("/produits/cart/add/{id}", name="products_add_to_cart")
     */
    public function addToCart(CartService $cartService, Request $request) {
        if($cartService->addProduct($request->get('id'))) {
            return new JsonResponse(['type' => "success", 'message' => 'Produit ajouté au panier']);
        } else {
            return new JsonResponse(['type' => "error", 'message' => 'Produit non ajouté au panier']);
        }
    }
}
