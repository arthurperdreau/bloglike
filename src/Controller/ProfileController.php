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

#[Route('/profile')]
final class ProfileController extends AbstractController
{
    #[Route('/see/{id}', name: 'see_profile')]
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


    #[Route('/addimage/{id}', name: 'app_addimageprofile')]
    public function addImageProfile( EntityManagerInterface $manager, Request $request, UserRepository $userRepository): Response {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        $imageProfile = new Image();
        $form = $this->createForm(ImageType::class, $imageProfile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageProfile->setProfile($this->getUser()->getProfile());

            $manager->persist($imageProfile);
            $manager->flush();

            return $this->redirectToRoute('see_profile', ['id' => $user->getId()]);
        }

        return $this->render('profile/addimage.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/removepicture/{id}', name: 'add_removepictureprofile')]
    public function removeImage(Image $image, EntityManagerInterface $manager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $manager->remove($image);
        $manager->flush();

        return $this->redirectToRoute('app_addimageprofile');
    }
}
