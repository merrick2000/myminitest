<?php

namespace App\Controller;

use App\Entity\Order;
use App\Service\Cart;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use App\Event\OrderCreatedEvent;

class OrderController extends AbstractController
{
    /**
     * @Route("/make-order", name="app_make_order")
     */
    public function newOrder(Cart $cart, Security $security, EntityManagerInterface $entityManager,EventDispatcherInterface $dispatcher): Response
    {
        $user = $security->getUser();
        if($user){
            $cartData = [];
            $cartItems = $cart->getItems();
            $userCredits = $user->getCredits();
            if($userCredits >= $cart->getTotal()){
                $order = new Order;
                foreach ($cartItems as $cartItem) {
                    $cartData[] = [
                        'productId' => $cartItem['product']->getId(),
                        'variant' => $cartItem['variant'],
                        'quantity' => $cartItem['quantity']
                    ];
                }
                $order->setPurchasedBy($user)
                    ->setCreatedAt(new \DateTime())
                    ->setCartData($cartData)
                    ->setTotal($cart->getTotal())
                ;
                $user->setCredits($userCredits - $cart->getTotal());
                $cart->clear();
                $entityManager->persist($order);
                $entityManager->flush();

                // Dispatch the order.created event
                $event = new OrderCreatedEvent($order);
                $dispatcher->dispatch($event, 'order.created');
                return $this->render('order/success.html.twig');
            }
            else{
                //Not enough credits
                return $this->render('order/nocredits.html.twig');
            }
        }
    }
}
