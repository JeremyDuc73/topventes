<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Review;
use App\Form\ReviewType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/review')]
class ReviewController extends AbstractController
{
    #[Route('/', name: 'app_review')]
    public function index(): Response
    {
        return $this->render('review/index.html.twig');
    }

    #[Route('/create/{id}', name:'app_review_create')]
    public function create(Request $request, EntityManagerInterface $manager, Product $product):Response
    {
        $review = new Review();
        $reviewForm = $this->createForm(ReviewType::class,$review);
        $reviewForm->handleRequest($request);
        if($reviewForm->isSubmitted() && $reviewForm->isValid())
        {
            $review->setProduct($product);
            $review->setAuthor($this->getUser());
            $manager->persist($review);
            $manager->flush();
        }
        return $this->redirectToRoute('app_product_show', ['id'=>$product->getId()]);
    }

    #[Route('/delete/{id}', name: 'app_review_delete', priority: 2)]
    public function delete(Review $review, EntityManagerInterface $manager): Response
    {
        $manager->remove($review);
        $manager->flush();
        return $this->redirectToRoute('app_product_show', ['id'=>$review->getProduct()->getId()]);
    }

    #[Route('/edit/{id}', name: 'app_review_edit', priority: 2)]
    public function edit(Review $review, Request $request, EntityManagerInterface $manager): Response
    {
        $editReviewForm = $this->createForm(ReviewType::class, $review);
        $editReviewForm->handleRequest($request);
        if($editReviewForm->isSubmitted() && $editReviewForm->isValid())
        {
            $manager->persist($review);
            $manager->flush();
            return $this->redirectToRoute('app_product_show', ['id'=>$review->getProduct()->getId()]);
        }
        return $this->renderForm('review/edit.html.twig',[
            'editReviewForm'=>$editReviewForm
        ]);
    }
}
