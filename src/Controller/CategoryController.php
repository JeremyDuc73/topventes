<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    #[Route('/admin/category', name: 'app_category')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('category/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    #[Route('/admin/category/create', name: 'app_category_create', priority: 2)]
    #[Route('/admin/category/edit/{id}', name: 'app_category_edit', priority: 2)]
    public function create(Request $request, EntityManagerInterface $manager, Category $category=null): Response
    {
        $edit = false;
        if ($category){
            $edit = true;
        }
        if (!$edit){
            $category = new Category();
        }
        $categoryForm = $this->createForm(CategoryType::class, $category);
        $categoryForm->handleRequest($request);
        if ($categoryForm->isSubmitted() && $categoryForm->isValid())
        {
            $manager->persist($category);
            $manager->flush();
            return $this->redirectToRoute('app_category');
        }

        return $this->renderForm('category/create.html.twig', [
            'categoryForm'=>$categoryForm,
            'edit'=>$edit
        ]);
    }

    #[Route('/admin/category/delete/{id}', name: 'app_category_delete', priority: 2)]
    public function delete(Category $category, EntityManagerInterface $manager): Response
    {
        $manager->remove($category);
        $manager->flush();
        return $this->redirectToRoute('app_category');
    }
}
