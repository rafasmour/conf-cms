<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProductRepository;
use App\Entity\Product;
use App\Form\ProductType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;    
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
    public function new(Request $request, EntityManagerInterface $manager): Response{

        $product = new Product;

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) {

            $manager->persist($product);

            $manager->flush();

            $this->addFlash('notice','Product has been added');

            return $this->redirectToRoute('product_show', [
                'id'=> $product->getId(),
            ]);
        }

        return $this->render('product/new.html.twig', [
            'form'=> $form->createView(),
        ]);
    }
}
