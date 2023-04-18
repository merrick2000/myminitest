<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\ProductVariant;
use App\Entity\SizeVariant;
use App\Service\Cart;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * @Route("/cart", name="app_cart")
     */
    public function index(Cart $cart)
    {
        $cartItems = $cart->getItems();
        
        return $this->render('cart/index.html.twig', [
            'cartItems' => $cartItems,
            'total' => $cart->getTotal()
        ]);
    }

    /**
     * @Route("/add-to-cart", name="app_add_to_cart", methods={"POST"})
     */
    public function addToCart(Request $request, Cart $cart, EntityManagerInterface $entityManager): Response
    {
        $postData = $request->request->all();
        $variantId = !empty($postData['variant']) ? (int)$postData['variant'] : null;
        $quantity = !empty($postData['quantity']) ? (int)$postData['quantity'] : null;
        $product = !empty($postData['productId']) ? $entityManager->getRepository(Product::class)->find((int)$postData['productId']) : null;
        if($variantId !== null || $quantity !== null || $quantity > 0){
            $variant = $entityManager->getRepository(ProductVariant::class)->findOneById($variantId);
            if($variant){
                $size = $entityManager->getRepository(SizeVariant::class)->findOneById($variant->getSizeVariant()->getId());
                $cart->add($product, $quantity, ['price' => $variant->getPrice(), 'size' => $size->getSize()], true);
            }
            else{
                $cart->add($product, $quantity);
            }
        }
        
        return $this->redirectToRoute('app_cart');
    }

    /**
    * @Route("/remove-cart-item/{id}", name="app_remove_cart")
    */
    public function removeFromCart(Request $request, Cart $cart, Product $product = null): Response
    {
        
        if($product){
            $cart->remove($product);
        } 
        return $this->redirectToRoute('app_cart');
    }

    /**
    * @Route("/show-cart", name="app_show_cart")
    */
    public function showCart(Cart $cart): Response
    {
        $items = $cart->getItems();

        return $this->render('cart/cart.html.twig', [
            'items' => $items,
            'total' => $cart->getTotal(),
        ]);
    }
}
