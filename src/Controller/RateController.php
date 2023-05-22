<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Rate;
use App\Repository\RateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RateController extends AbstractController
{
    #[Route('/rate/{id}/stars/{stars}', name: 'app_rate')]
    public function index(Product $product, $stars = null, EntityManagerInterface $manager, RateRepository $rateRepository): Response
    {
        $user= $this->getUser();
        if (!$user || !$product){
            return $this->redirectToRoute('app_home');
        }
        if (!ctype_digit($stars)){
            return $this->redirectToRoute('app_home');
        }
        if ($stars < 0 || $stars > 5){
            return $this->redirectToRoute('app_home');
        }

        $rate = $rateRepository->findOneBy([
            'author'=>$user,
            'product'=>$product
        ]);

        if (!$rate){
            $rate = new Rate();
            $rate->setProduct($product);
            $rate->setAuthor($user);
        }
        $rate->setStars($stars);
        $manager->persist($rate);
        $manager->flush();


        return $this->redirectToRoute('app_product_show', ['id'=>$product->getId()]);

    }
}
