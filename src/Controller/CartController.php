<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CartController extends AbstractController
{
    #[Route('/panier', name: 'app_cart')]
    public function index(Cart $cart): Response
    {
        return $this->render('cart/index.html.twig', [
            'cart' => $cart->getCart()
        ]);
    }

    #[Route('/panier/add/{id}', name: 'app_cart_add')]
    public function add(int $id, Cart $cart, ProductRepository $productRepository): Response
    {
        // Recup produit associé au cart
        $product = $productRepository->findOneBy(['id' => $id]);
        // Ajouter le produit
        $cart->add($product);

        $this->addFlash(
            'success',
            'Article ajouté au panier !'
        );

        // Rediriger
        return $this->redirectToRoute('app_product', ['slug' => $product->getSlug()]);
    }
}
