<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Product;
use App\Entity\Profile;
use App\Form\ImageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImageController extends AbstractController
{
    #[Route('/admin/image/product/{id}', name: 'app_image')]
    public function index(Product $product): Response
    {
        $image = new Image();
        $formImage = $this->createForm(ImageType::class, $image);
        return $this->renderForm('image/index.html.twig', [
                'product' => $product,
                'formImage'=>$formImage
            ]);
    }

    #[Route('/image/addtoprofile', name: 'app_image_profile_add')]
    #[Route('/admin/image/addtoproduct/{id}', name: 'app_image_product_add')]
    public function addImage(Product $product=null, Request $request, EntityManagerInterface $manager): Response
    {
        $routeName = $request->attributes->get("_route");
        $image = new Image();
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($product && $routeName == "app_image_product_add"){
                $image->setProduct($product);
            }elseif ($routeName == "app_image_profile_add"){
                $oldImage = $this->getUser()->getProfile()->getImage();
                if ($oldImage){
                    $manager->remove($oldImage);
                }
                $image->setProfile($this->getUser()->getProfile());
            }

            $manager->persist($image);
            $manager->flush();
        }
        if ($routeName == "app_image_profile_add"){
            return $this->redirectToRoute('app_profile');
        }
        return $this->redirectToRoute('app_image', ['id' => $product->getId()]);
    }

    #[Route('/admin/image/removefromproduct/{id}', name: 'app_image_delete')]
    public function removeFromProduct(Image $image, EntityManagerInterface $manager): Response
    {
        if ($image) {
            $product = $image->getProduct();
            $manager->remove($image);
            $manager->flush();
        }
        return $this->redirectToRoute('app_image', ['id' => $product->getId()]);
    }
}
