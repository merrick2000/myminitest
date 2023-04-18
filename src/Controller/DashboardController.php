<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\SizeVariant;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
* @Route("/dashboard")
*/
class DashboardController extends AbstractController
{
    /**
     * @Route("/", name="app_dashboard")
     */
    public function index(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $products = $entityManager->getRepository(Product::class)->findBy([], ['createdAt' => 'DESC']);
        // $prodcutsArray = [];
        // foreach ($products as $product) {
        //     $prodcutsArray[] = [
        //         'id' => $product->getId(),
        //         'name' => $product->getName(),
        //         'price' => $product->getPrice()
        //     ];
        // }
        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }

    /**
     * @Route("/users", name="app_dashboard_users")
     */
    public function getUsers(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $users = $entityManager->getRepository(User::class)->findBy([], ['id' => 'DESC']);
        // $prodcutsArray = [];
        // foreach ($products as $product) {
        //     $prodcutsArray[] = [
        //         'id' => $product->getId(),
        //         'name' => $product->getName(),
        //         'price' => $product->getPrice()
        //     ];
        // }
        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/size-variants", name="app_dashboard_sizes")
     */
    public function getSizes(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $sizes = $entityManager->getRepository(SizeVariant::class)->findBy([], ['id' => 'ASC']);
        return $this->render('size-variant/index.html.twig', [
            'sizes' => $sizes,
        ]);
    }
}
