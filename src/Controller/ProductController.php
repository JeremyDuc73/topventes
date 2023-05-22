<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Review;
use App\Form\ProductType;
use App\Form\ReviewType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ProductController extends AbstractController
{
    #[Route('/product', name: 'app_product')]
    #[Route('/product/filter/{id}', name: 'app_product_filtered')]
    #[Route('/search', name: 'app_product_search', methods: ['POST'])]
    public function index(ProductRepository $productRepository, Request $request, CategoryRepository $categoryRepository, Category $category=null): Response
    {
        if ($request->get("_route") === 'app_product_search'){
            $searchValue = $request->get('value');
            if ($searchValue == ''){
                return $this->redirectToRoute('app_product');
            }
            $products = $productRepository->findByExampleField($searchValue);
            return $this->render('product/index.html.twig', [
                'categories'=> $categoryRepository->findAll(),
                'products' => $products
            ]);
        }

        if ($category){
            return $this->render('product/index.html.twig',[
                'categories'=>$categoryRepository->findAll(),
                'products'=>$productRepository->findBy([
                    'category'=>$category
                ])
            ]);
        }
        return $this->render('product/index.html.twig', [
            'categories'=> $categoryRepository->findAll(),
            'products' => $productRepository->findAll()
        ]);
    }

    #[Route('/product/{id}', name: 'app_product_show')]
    public function show(Product $product): Response
    {
        $review = new Review();
        $reviewForm = $this->createForm(ReviewType::class, $review);

        return $this->renderForm('product/show.html.twig', [
            'product' => $product,
            'reviewForm'=>$reviewForm
        ]);
    }

    #[Route('/admin/product/create', name: 'app_product_create', priority: 2)]
    #[Route('/admin/product/edit/{id}', name: 'app_product_edit', priority: 2)]
    public function create(Request $request, EntityManagerInterface $manager, Product $product=null): Response
    {
        $edit = false;
        if ($product){
            $edit = true;
        }
        if (!$edit){
            $product = new Product();
        }
        $productForm = $this->createForm(ProductType::class, $product);
        $productForm->handleRequest($request);
        if ($productForm->isSubmitted() && $productForm->isValid())
        {
           $manager->persist($product);
           $manager->flush();
           return $this->redirectToRoute('app_product_show', ['id'=>$product->getId()]);
        }

        return $this->renderForm('product/create.html.twig', [
            'productForm'=>$productForm,
            'edit'=>$edit
        ]);
    }

    #[Route('/admin/product/delete/{id}', name: 'app_product_delete', priority: 2)]
    public function delete(Product $product, EntityManagerInterface $manager): Response
    {
        $manager->remove($product);
        $manager->flush();
        return $this->redirectToRoute('app_product');
    }
}
