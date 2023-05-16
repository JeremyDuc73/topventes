<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Entity\User;
use App\Form\ProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile/{id}', name: 'app_profile')]
    public function index(User $user): Response
    {
        return $this->render('profile/index.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/profile/edit/{id}', name: 'app_profile_edit')]
    public function edit(Request $request, EntityManagerInterface $manager, Profile $profile): Response
    {
        $profileForm = $this->createForm(ProfileType::class, $profile);
        $profileForm->handleRequest($request);
        if ($profileForm->isSubmitted() && $profileForm->isValid()) {
            $manager->remove($profile->getImage());
            $manager->flush();
            $manager->persist($profile);
            $manager->flush();
            return $this->redirectToRoute('app_profile', ['id'=>$profile->getProfileUser()->getId()]);
        }
        return $this->renderForm('profile/edit.html.twig', [
            'profileForm'=>$profileForm
        ]);
    }
}
