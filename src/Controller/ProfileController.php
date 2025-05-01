<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Profile;
use App\Form\ImageType;
use App\Form\ProfileType;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


final class ProfileController extends AbstractController
{
    #[Route('/profile/see/{id}', name: 'see_profile')]
    public function profile(int $id, EntityManagerInterface $manager, Request $request, Profile $profile,): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $user = $this->getUser();

        $form = $this->createForm(ProfileType::class, $profile);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($profile);
            $manager->flush();
            return $this->redirectToRoute('see_profile', ['id' => $id]);
        }
        return $this->render('profile/index.html.twig', [
            'form' => $form->createView(),
            'profile' => $profile,
            'user' => $user,
        ]);
    }


    #[Route('/profile/addimage/{id}', name: 'app_addimageprofile')]
    public function addImageProfile( EntityManagerInterface $manager, Request $request, UserRepository $userRepository): Response {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        $image = new Image();
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image->setProfile($this->getUser()->getProfile());

            $manager->persist($image);
            $manager->flush();

            return $this->redirectToRoute('see_profile', ['id' => $user->getProfile()->getId()]);
        }

        return $this->render('profile/addimage.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/profile/removepicture/{id}', name: 'add_removepictureprofile')]
    public function removeImage(Image $image, EntityManagerInterface $manager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $profile = $image->getProfile();
        if ($profile) {
            $profile->setPicture(null);
        }

        $manager->remove($image);
        $manager->flush();

        return $this->redirectToRoute('app_addimageprofile',['id' => $this->getUser()->getProfile()->getId()
        ]);
    }
}
