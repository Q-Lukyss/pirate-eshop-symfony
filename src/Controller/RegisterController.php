<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RegisterController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        // Permet d'écouter la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            // envoyer un toast de succes
            $this->addFlash(
                'success',
                'Bienvenue à bord Moussaillon! Connecte-toi pour accéder au Marché.',
            );

            // redirection vers login
            return $this->redirectToRoute('app_login');
        }

        return $this->render('register/index.html.twig', [
            "controller_name" => "RegisterController",
            "register_form" => $form->createView(),
        ]);
    }
}
