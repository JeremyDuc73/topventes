<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/order')]
class OrderController extends AbstractController
{
    #[Route('/selectaddress', name: 'app_select_address')]
    public function index(): Response
    {
        return $this->render('order/address_selection.html.twig');
    }

    #[Route('/payment/{id}', name: 'app_payment')]
    public function payment(Address $address): Response
    {
        return $this->render('order/payment.html.twig', [
            'address'=>$address
        ]);
    }

    #[Route('/makeorder/{id}', name: 'app_make_order')]
    public function makeOrder(Address $address, CartService $cartService, EntityManagerInterface $manager): Response
    {
        if (!$this->getUser()){
            return $this->redirectToRoute('app_home');
        }
        $order = new Order();
        $order->setProfile($this->getUser()->getProfile());
        $order->setAddress($address);
        $order->setStatus(2);
        $order->setTotal($cartService->getTotal());

        foreach ($cartService->getCart() as $item){
            $orderItem = new OrderItem();
            $orderItem->setProduct($item['product']);
            $orderItem->setQuantity($item['quantity']);
            $orderItem->setOfOrder($order);
            $manager->persist($orderItem);
        }
        $manager->flush();
        $cartService->emptyCart();


        return $this->redirectToRoute('app_my_orders_show', ['id'=>$order->getId()]);
    }
}
