<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProductRepository;
use App\Entity\Product;
class ProductController extends AbstractController
{
    #[Route('/product', name: 'product_index')]
    public function index(ProductRepository $repository): Response
    {

        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
            'products' => $repository->findAll(),
        ]);
    }
    #[Route('/product/{id<\d+>}', name: 'product_show')]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product
        ]);
    }
    #[Route('/product/new', name: 'product_new')]
    public function new(): Response{
        return $this->render('product/new.html.twig');
    }
}
