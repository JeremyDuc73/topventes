<?php

namespace App\Controller;

use App\Entity\Order;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

#[Route('/my/orders')]
class MyOrdersController extends AbstractController
{
    #[Route('/', name: 'app_my_orders', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('my_orders/index.html.twig');
    }

    #[Route('/{id}', name: 'app_my_orders_show', methods: ['GET'])]
    public function show(Order $order): Response
    {
        if($this->getUser()->getProfile() !== $order->getProfile())
        {
            return $this->redirectToRoute('app_home');
        }

        return $this->render('my_orders/show.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route('/mailinvoice/{id}', name: 'app_mail_invoice')]
    public function mailInvoice(MailerInterface $mailer, Pdf $pdfmaker, Environment $twig, Order $order)
    {
        $html = $twig->render('home/invoice.html.twig',[
            'order'=>$order
        ]);
        $pdf = $pdfmaker->getOutputFromHtml($html);

        $email = (new Email())
            ->from()
            ->to('nashoba.jeremy@gmail.com')
            ->subject('Votre facture')
            ->text('Voici votre facture pour votre dernière commande !')
            ->attach($pdf, sprintf('facture.pdf'));

        $mailer->send($email);
        return $this->redirectToRoute('app_my_orders_show', ['id'=>$order->getId()]);
    }
}
