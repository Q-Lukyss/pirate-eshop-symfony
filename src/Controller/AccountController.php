<?php

namespace App\Controller;

use App\Entity\Adress;
use App\Form\AdressUserType;
use App\Form\PasswordUserType;
use App\Repository\AdressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
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

    #[Route('/compte/modifier-mdp', name: 'app_modify_password')]
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

    #[Route('/compte/adresse', name: 'app_address')]
    public function address(Request $request, EntityManagerInterface $entityManager): Response
    {
        return $this->render('account/address.html.twig', [
            'controller_name' => 'AccountController',
        ]);
    }

    #[Route('/compte/adresse/ajouter/{id}', name: 'app_address_add', defaults: ['id' => null])]
    public function addAddress(Request $request, EntityManagerInterface $entityManager,
                               ?int $id, AdressRepository $adressRepository): Response
    {
        // Id existe ?
        if ($id){
            $address = $adressRepository->findOneBy(['id' => $id]);
            // Si adresse n'appartient pas a utilisateur en cours, on redirige
            if (!$address || $address->getUser() !== $this->getUser()){
                return $this->redirectToRoute('app_account');
            }
        }
        else{
            $address = new Adress();

            $address->setUser($this->getUser());
        }


        $form = $this->createForm(AdressUserType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($address);
            $entityManager->flush();

            // envoyer un toast de succes
            $this->addFlash(
                'success',
                'Adresse ajoutée',
            );

            // rediriger vers dashboard
            return $this->redirectToRoute('app_address');
        }
        return $this->render('account/address_form.html.twig', [
            'address_form' => $form->createView(),
        ]);
    }

    #[Route('/compte/adresse/supprimer/{id}', name: 'app_address_delete')]
    public function deleteAddress(Request $request, EntityManagerInterface $entityManager,
                               int $id, AdressRepository $adressRepository): Response
    {
        $address = $adressRepository->findOneBy(['id' => $id]);
        // Si adresse n'appartient pas a utilisateur en cours, on redirige
        if (!$address || $address->getUser() !== $this->getUser()) {

            $this->addFlash(
                'danger',
                'Mdr NON!',
            );

            return $this->redirectToRoute('app_account');
        }
        // Requête de Delete
        $entityManager->remove($address);
        $entityManager->flush();

        // envoyer un toast de succes
        $this->addFlash(
            'warning',
            'Adresse supprimée',
        );
        // rediriger vers address
        return $this->redirectToRoute('app_address');

    }
}
