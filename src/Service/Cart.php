<?php

// src/Service/Cart.php

namespace App\Service;

use App\Entity\Product;
use App\Entity\ProductVariant;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Cart
{
    private $session;
    private $entityManager;

    public function __construct(SessionInterface $session, EntityManagerInterface $entityManager)
    {
        $this->session = $session;
        $this->session->start();
        if (!$this->session->has('cart')) {
            $this->session->set('cart', []);
        }
        $this->entityManager = $entityManager;
    }

    public function add($product, $quantity = 1,$variant = 0, $hasVariant = false)
    {
        $cart = $this->session->get('cart');
        if (!isset($cart[$product->getId()])) {
            $cart[$product->getId()] = [
                'product' => $product,
                'variant' => $variant,
                'quantity' => $quantity,
                'hasVariant' => $hasVariant
            ];
        } else {
            $variants = array_column($cart[$product->getId()], 'variant');
            if(!in_array($variant, $variants)){
                $cart[$product->getId()] = [
                    'product' => $product,
                    'variant' => $variant,
                    'quantity' => $quantity,
                    'hasVariant' => $hasVariant
                ];
            }
            // $cart[$product->getId()]['quantity']++;
        }
        $this->session->set('cart', $cart);
    }

    public function remove(Product $product, $variant = 0)
    {
        $cart = $this->session->get('cart');
        if (isset($cart[$product->getId()])){
            unset($cart[$product->getId()]);
            $this->session->set('cart', $cart);
        }
    }

    public function getItems()
    {
        $items = [];
        $cart = $this->session->get('cart');
        foreach ($cart as $item) {
            $items[] = $item;
        }
        return $items;
    }

    public function getTotal()
    {
        $total = 0;
        $cart = $this->session->get('cart');
        foreach ($cart as $item) {
            if(is_array($item['variant'])){
                $total += $item['variant']['price'] * $item['quantity'];
            }
            else{
                $total += $item['product']->getPrice() * $item['quantity'];
            }
        }
        return $total;
    }

    public function clear()
    {
        $this->session->remove('cart');
    }
}
