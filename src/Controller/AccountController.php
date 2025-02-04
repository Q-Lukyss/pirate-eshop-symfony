<?php

namespace App\Controller;

use App\Form\PasswordUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class AccountController extends AbstractController
{
    #[Route('/dashboard', name: 'app_account')]
    public function index(): Response
    {
        return $this->render('account/index.html.twig', [
            'controller_name' => 'AccountController',
        ]);
    }

    #[Route('/modifier-mdp', name: 'app_modify_password')]
    public function modify_password(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(PasswordUserType::class, $user, [
            'password_hasheur' => $userPasswordHasher,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // envoyer en bdd -> flush
            $entityManager->flush();

            // envoyer un toast de succes
            $this->addFlash(
                'success',
                'Mot de Passe modifié avec success',
            );

            // rediriger vers dashboard
            return $this->redirectToRoute('app_account');
        }

        return $this->render('account/modify_password.html.twig', [
            'controller_name' => 'AccountController',
            'modify_password' => $form->createView(),
        ]);
    }
}
