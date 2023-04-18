<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ProductController extends AbstractController
{
    /**
     * @Route("/dashboard/product/add-new", name="app_dashboard_add_product")
     */
    public function addProductAdmin(Request $request): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('app_dashboard_product_show', ['id' => $product->getId()]);
        }

        return $this->render('product/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/dashboard/product/{id}", name="app_dashboard_product_show")
     */
    public function show(Product $product = null): Response
    {
        if(!$product){
            return $this->redirectToRoute('app_dashboard');
        }
        return $this->render('product/show.html.twig', [
            'product' => $product
        ]);
    }

    /**
     * @Route("/dashboard/product/{id}/edit", name="app_dashboard_product_edit")
     */
    public function edit(Request $request, Product $product = null): Response
    {
        if(!$product){
            return $this->redirectToRoute('app_dashboard');
        }
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('app_dashboard_product_show', ['id' => $product->getId()]);
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/dashboard/product/delete/{id}", name="app_dashboard_product_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Product $product): Response
    {
        if(!$product){
            return $this->redirectToRoute('app_dashboard');
        }
        $submittedToken = $request->request->get('form')['_token'];
        if ($this->isCsrfTokenValid('delete_product_' . $product->getId(), $submittedToken)) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($product);
            $entityManager->flush();

            $this->addFlash('success', 'Product deleted successfully.');
        } else {
            $this->addFlash('error', 'Invalid CSRF token.');
        }

        return $this->redirectToRoute('app_dashboard');
    }

    /**
     * @Route("/products", name="app_home")
     */
    public function index(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $products = $entityManager->getRepository(Product::class)->findBy([], ['createdAt' => 'ASC']);
        $prodcutsArray = [];
        foreach ($products as $product) {
            $prodcutsArray[] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'price' => self::getProductPrice($product)
            ];
        }
        return $this->render('home/index.html.twig', [
            'products' => $products,
        ]);
    }

    /**
     * @Route("/products/{id}", name="product_details", requirements={"id"="\d+"})
     */
    public function getProductDetails(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $product = $entityManager->getRepository(Product::class)->find($id);

        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }
        $product = [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'description' => $product->getDescription(),
            'price' => $product->getPrice(),
            'createdAt' => $product->getCreatedAt(),
            'variants' => self::getProductVariantInfo($product)
        ];

        return $this->render('home/product-detail.html.twig', [
            'product' => $product,
        ]);
    }

    public static function getProductPrice(Product $product)
    {
        $variants = $product->getProductVariants()->toArray();
        if($variants){
            foreach ($variants as $variant) {
                $prices[] = $variant->getPrice();
            }
            return (string)min($prices) . ' - ' . (string)max($prices);
        }
        return $product->getPrice();
    }

    public static function getProductVariantInfo(Product $product)
    {
        $variants = $product->getProductVariants()->toArray();
        $data = [];
        if($variants){
            foreach ($variants as $variant) {
                $data[] = [
                    'id' => $variant->getId(),
                    'variantPrice' => $variant->getPrice(),
                    'size' => $variant->getSizeVariant()->getSize()
                ];
            }   
            return $data;
        }
        return false;
    }
}
