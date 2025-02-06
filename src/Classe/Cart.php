<?php

namespace App\Classe;

use Symfony\Component\HttpFoundation\RequestStack;

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
        $cart = $this->requestStack->getSession()->get('cart');

        if ($cart[$product->getId()['qty']] <= 1) {

            $this->remove($product);
        }
        else {
            $cart[$product->getId()] = [
                'object' => $product,
                'qty' =>$cart[$product->getId()]['qty'] - 1
            ];
        }
          $this->requestStack->getSession()->set('cart', $cart);

    }

    public function remove($product): void
    {

    }
}