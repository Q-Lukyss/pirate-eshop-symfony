<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\OrderDetail;
use App\Form\OrderType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityManagerInterfacer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OrderController extends AbstractController
{
    #[Route('/commande/livraison', name: 'app_order')]
    public function index(): Response
    {
        $addresses = $this->getUser()->getAdresses();

        if(count($addresses) === 0) {
            return $this->redirectToRoute('app_address_add');
        }

        $form = $this->createForm(OrderType::class, null, [
            'addresses' => $addresses,
            // apres submit rediriger vers recap
            'action' => $this->generateUrl('app_order_summary')
        ]);

        return $this->render('order/index.html.twig', [
            'delivery_form' => $form->createView()
        ]);
    }

    #[Route('/commande/recapitulatif', name: 'app_order_summary')]
    public function summary(Request $request, Cart $cart, EntityManagerInterface $entityManager): Response
    {
        if ($request->getMethod() !== 'POST') {
            return $this->redirectToRoute('app_order');
        }

        $products = $cart->getCart();

        $form = $this->createForm(OrderType::class, null, [
            'addresses' => $this->getUser()->getAdresses(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $addressObj = $form->get("addresses")->getData();
            $address = $addressObj->getFirstName() . ' ' . $addressObj->getLastName().'<br/>';
            $address .= $addressObj->getAddress() .'<br/>';
            $address .= $addressObj->getCity() .'<br/>';
            $address .= $addressObj->getCountry() .'<br/>';

            $order = new Order();
            $order->setCreatedAt(new \DateTime());
            $order->setState(1);
            $order->setCarrierName($form->get("carriers")->getData()->getName());
            $order->setCarrierPrice($form->get("carriers")->getData()->getPrice());
            $order->setDelivery($address);
            $order->setUser($this->getUser());

            foreach ($products as $product) {
                $orderDetail = new OrderDetail();
                $orderDetail->setProduct($product['object']->getName());
                $orderDetail->setProductIllustration($product['object']->getIllustration());
                $orderDetail->setProductPrice($product['object']->getPrice());
                $orderDetail->setProductTva($product['object']->getTva());
                $orderDetail->setProductQuantity($product['qty']);

                $order->addOrderDetail($orderDetail);
            }
            $entityManager->persist($order);
            $entityManager->flush();

        }

        return $this->render('order/summary.html.twig', [
            'choices' => $form->getData(),
            'cart' => $cart->getCart(),
            'totalWt' =>$cart->getTotalPriceWt(),
            // Pour envoyer a stripe
            'order'=>$order,
        ]);
    }
}
