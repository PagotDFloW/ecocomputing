<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Categories;
use App\Form\CategoryType;
use App\Repository\CategoriesRepository;

/**
 * @Route("/admin", name="admin_")
 */
class CategoryController extends AbstractController
{
    
    /**
     * @Route("/categories", name="categories")
     */
    public function indexCategories(CategoriesRepository $repo) {
        
        $categories = $repo->findBy([], ['name' => 'ASC']);

        return $this->render('back/categories/indexCategories.html.twig', [
            'categories' => $categories
        ]);
    }


    /**
     * @Route("/category/new", name="new_category")
     */
    public function createCategory(EntityManagerInterface $manager, Request $request) {
        $category = new Categories();

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($category);
            $manager->flush();

            return $this->redirectToRoute('admin_categories');
        }

        return $this->render('back/categories/editionCategory.html.twig', [
            'catgegory' => $category,
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/category/{id}/edit", name="edit_category")
     */
    public function editCategory(Categories $category, EntityManagerInterface $manager, Request $request) {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($category);
            $manager->flush();

            return $this->redirectToRoute('admin_categories');
        }

        return $this->render('back/categories/editionCategory.html.twig', [
            'form' => $form->createView(),
            'category' => $category
        ]);
    }


    /**
     * @Route("/category/{id}/delete", name="delete_category")
     */
    public function deleteCategory(Categories $category, EntityManagerInterface $manager) {

        $categoryName = $category->getName();
        
        //  Manager parent-sub categories
        $subCategories = $category->getSubCategories();
        foreach ($subCategories as $sub) {
            $sub->setCategoryParent(null);
            $manager->persist($sub);
        }

        $parentCategory = $category->getCategoryParent();
        if ($parentCategory !== null) {
            $parentCategory->removeSubCategory($category);
            $manager->persist($parentCategory);
        }

        $deleteCategory = $manager->createQuery('DELETE FROM App\Entity\Categories c WHERE c.id = ' .$category->getId());
        $deleteCategory = $deleteCategory->getResult();

        $manager->flush();

        $this->addFlash('success', 'La catégorie "' . $categoryName . '" a bien été supprimée.');
        return $this->redirectToRoute('admin_categories');
    }
}
