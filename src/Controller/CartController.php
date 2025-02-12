<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CartController extends AbstractController
{
    #[Route('/commande/panier', name: 'app_cart')]
    public function index(Cart $cart): Response
    {
        return $this->render('cart/index.html.twig', [
            'cart' => $cart->getCart()
        ]);
    }

    #[Route('/commande/panier/add/{id}', name: 'app_cart_add')]
    public function add(int $id, Cart $cart, ProductRepository $productRepository, Request $request): Response
    {
        // Recup produit associé au cart
        $product = $productRepository->findOneBy(['id' => $id]);
        // Ajouter le produit
        $cart->add($product);

        $this->addFlash(
            'success',
            'Article ajouté au panier !'
        );

        // Rediriger avec Request header referer
        return $this->redirect($request->headers->get('referer'));
    }


    #[Route('/commande/panier/decrement/{id}', name: 'app_cart_decrement')]
    public function decrement(int $id, Cart $cart, ProductRepository $productRepository): Response
    {
        $product = $productRepository->findOneBy(['id' => $id]);

        $cart->decrement_quantity($product);

        $this->addFlash(
            'warning',
            'Article Enlevé du panier !'
        );

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/commande/panier/remove/{id}', name: 'app_cart_remove')]
    public function remove(int $id, Cart $cart, ProductRepository $productRepository): Response
    {
        $product = $productRepository->findOneBy(['id' => $id]);

        $cart->remove($product);

        $this->addFlash(
            'warning',
            'Article Supprimé'
        );

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/commande/panier/clear', name: 'app_cart_clear')]
    public function clear (Cart $cart): Response
    {
        $cart->clear();

        $this->addFlash(
            'warning',
            'Panier vidé'
        );

        return $this->redirectToRoute('app_home');
    }
}

