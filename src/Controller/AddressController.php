<?php

namespace App\Controller;

use App\Entity\Address;
use App\Form\AddressType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/address')]
class AddressController extends AbstractController
{
    #[Route('/', name: 'app_address')]
    public function index(): Response
    {
        return $this->render('address/index.html.twig', [
        ]);
    }

    #[Route('/create', name: 'app_address_create', priority: 2)]
    #[Route('/edit/{id}', name: 'app_address_edit', priority: 2)]
    public function create(Request $request, EntityManagerInterface $manager, Address $address=null): Response
    {
        $edit = false;
        if ($address){
            $edit = true;
        }
        if (!$edit){
            $address = new Address();
        }
        $addressForm = $this->createForm(AddressType::class, $address);
        $addressForm->handleRequest($request);
        if ($addressForm->isSubmitted() && $addressForm->isValid())
        {
            if (!$edit){
                $address->setProfile($this->getUser()->getProfile());
            }
            $manager->persist($address);
            $manager->flush();
            return $this->redirectToRoute('app_address');
        }

        return $this->renderForm('address/create.html.twig', [
            'addressForm'=>$addressForm,
            'edit'=>$edit
        ]);
    }

    #[Route('/delete/{id}', name: 'app_address_delete', priority: 2)]
    public function delete(Address $address, EntityManagerInterface $manager): Response
    {
        $manager->remove($address);
        $manager->flush();
        return $this->redirectToRoute('app_address');
    }
}
