<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Profile;
use App\Entity\User;
use App\Form\ImageType;
use App\Form\ProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(): Response
    {
        return $this->renderForm('profile/index.html.twig');
    }

    #[Route('/profile/edit', name: 'app_profile_edit')]
    public function edit(Request $request, EntityManagerInterface $manager): Response
    {
        $image = new Image();
        $formImage = $this->createForm(ImageType::class, $image);

        $profile = $this->getUser()->getProfile();
        $profileForm = $this->createForm(ProfileType::class, $profile);
        $profileForm->handleRequest($request);
        if ($profileForm->isSubmitted() && $profileForm->isValid()) {
            $manager->persist($profile);
            $manager->flush();
            return $this->redirectToRoute('app_profile');
        }
        return $this->renderForm('profile/edit.html.twig', [
            'profileForm'=>$profileForm,
            'formImage'=>$formImage
        ]);
    }
}
