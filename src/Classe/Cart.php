<?php

namespace App\Classe;

use Symfony\Component\HttpFoundation\RequestStack;
use function PHPUnit\Framework\isEmpty;

class Cart
{
    public function __construct(
        private RequestStack $requestStack,
    )
    {

    }

    public function getCart(){
        return $this->requestStack->getSession()->get('cart');
    }

    public function add($product): mixed
    {
        $cart = $this->requestStack->getSession()->get('cart');

//        dd($cart);
        if (isset($cart[$product->getId()])) {
            $this->increment_quantity($product);
            // Rechargez le panier mis à jour depuis la session
            $cart = $this->requestStack->getSession()->get('cart');
        } else {
            $cart[$product->getId()] = [
                'object' => $product,
                'qty' => 1
            ];
        }

        // Enregistrez le panier (pour le cas où le produit n'était pas déjà présent)
        $this->requestStack->getSession()->set('cart', $cart);

        return $this->requestStack->getSession()->get('cart');
    }


    public function increment_quantity($product): void
    {
        $cart = $this->requestStack->getSession()->get('cart');

        $cart[$product->getId()] = [
            'object' => $product,
            'qty' =>$cart[$product->getId()]['qty'] + 1
        ];

        $this->requestStack->getSession()->set('cart', $cart);
    }

    public function decrement_quantity($product): void
    {
        $cart = $this->getCart();

        // Vérifie d'abord si le produit est dans le panier
        if (isset($cart[$product->getId()])) {
            // Si la quantité actuelle est <= 1, on supprime carrément l'article
            if ($cart[$product->getId()]['qty'] <= 1) {
                $this->remove($product);
            } else {
                // Sinon on décrémente simplement la quantité
                $cart[$product->getId()] = [
                    'object' => $product,
                    'qty'    => $cart[$product->getId()]['qty'] - 1
                ];

                $this->requestStack->getSession()->set('cart', $cart);
            }
        }
    }

    public function remove($product): void
    {
        $cart = $this->getCart();

        // Si le produit existe dans le panier, on le supprime
        if (isset($cart[$product->getId()])) {
            unset($cart[$product->getId()]);
        }

        $this->requestStack->getSession()->set('cart', $cart);
    }

    public function clear(): void
    {
        $this->requestStack->getSession()->remove('cart');
    }

    public function getTotalQty(): float
    {
        $cart = $this->getCart();
        $quantity = 0;
        if(!isset($cart) || empty($cart)){
            return 0;
        }
        foreach ($cart as $product) {
            $quantity += $product['qty'];
        }
        return $quantity;

    }
    public function getTotalPriceRaw(): float
    {
        $cart = $this->getCart();
        $price = 0;
        if(!isset($cart) || empty($cart)){
            return 0;
        }
        foreach ($cart as $product) {
            $price += $product['object']->getPrice() * $product['qty'];
        }
        return $price;
    }
    public function getTotalPriceWt(): float
    {
        $cart = $this->getCart();
        $price = 0;
        if(!isset($cart) || empty($cart)){
            return 0;
        }
        foreach ($cart as $product) {
            $price += $product['object']->getPriceWt() * $product['qty'];
        }
        return $price;
    }

    public function checkIfCartExists():bool
    {
        if(!isset($cart) || empty($cart)){
            return false;
        }
        return true;
    }
}