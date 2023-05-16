<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Form\ProductType;
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
    public function index(ProductRepository $productRepository, CategoryRepository $categoryRepository, Category $category=null): Response
    {
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
        return $this->render('product/show.html.twig', [
            'product' => $product,
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
           if($edit){
               foreach ($product->getImages() as $img){
                   $manager->remove($img); $manager->flush();
               }
           }
           $images = $productForm->getData()->getImages();
           foreach($images as $image){
               $image->setProduct($product);
           }
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
