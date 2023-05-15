<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/product', name: 'app_product')]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
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
            if (!$edit){
                $images = $productForm->getData()->getImages();
                foreach($images as $image){
                    $image->setGateau($product);
                }
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
}
