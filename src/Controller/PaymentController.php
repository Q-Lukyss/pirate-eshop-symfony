<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PaymentController extends AbstractController
{
    #[Route('/commande/paiement/{id_order}', name: 'app_payment')]
    public function index(int $id_order, OrderRepository $orderRepository): Response
    {
        $stripeSecretKey='sk_test_51PixFI01RuS50BaHBK1OBRSoNPhXNMuDb6l5PA2YwQGlPiKvtoXyLhUaDsc6m9nKEj0UDCBVQ8ZTEzZwLwXD8owM00ATYZ1dny';
        \Stripe\Stripe::setApiKey($stripeSecretKey);
        header('Content-Type: application/json');

        $YOUR_DOMAIN = 'http:/127.0.0.1:8000';

        $order = $orderRepository->findOneBy([
            'id' => $id_order,
            'user' => $this->getUser()
        ]);

        if (!$order) {
            return $this->redirectToRoute('app_home');
        }

        $productForStripe = [];

        foreach ($order->getOrderDetails() as $product) {
            $productForStripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => number_format($product->getProductPrice()*(1+$product->getProductTva()/100)*100, 0, '', ''),
                    'product_data' => [
                        'name' => $product->getProduct(),

                    ],
                ],
                'quantity' => $product->getProductQuantity(),
            ];
        }

        // Ajout transporteur
        $productForStripe[] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => number_format($order->getCarrierPrice()*100, 0, '', ''),
                'product_data' => [
                    'name' => $order->getCarrierName(),

                ],
            ],
            'quantity' => $product->getProductQuantity(),
        ];

        $checkout_session = \Stripe\Checkout\Session::create([
            'line_items' => [[
                $productForStripe
            ]],
            'customer_email' => $this->getUser()->getEmail(),

            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/success.html',
            'cancel_url' => $YOUR_DOMAIN . '/cancel.html',
        ]);

        header("HTTP/1.1 303 See Other");
        header("Location: " . $checkout_session->url);

        return $this->redirect($checkout_session);
    }
}
