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
    public function index(ProductRepository $repository): Response
    {

        return $this->render('/admin/product/index.html.twig', [
            'controller_name' => 'ProductController',
            'products' => $repository->findAll(),
        ]);
    }
    public function show(Product $product): Response
    {
        return $this->render('/admin/product/show.html.twig', [
            'product' => $product
        ]);
    }
    public function new(Request $request, EntityManagerInterface $entityManager): Response{

        $product = new Product;

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($product);

            $entityManager->flush();

            $this->addFlash('notice','Product has been added');

            return $this->redirectToRoute('app_admin_product_show', [
                'id'=> $product->getId(),
            ]);
        }

        return $this->render('admin/product/new.html.twig', [
            'form'=> $form->createView(),
        ]);

    }
    public function edit(Product $product, Request $request, EntityManagerInterface $entityManager): Response{
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) {

            $entityManager->flush();

            $this->addFlash('notice','Product has been updated');

            return $this->redirectToRoute('app_admin_product_show', [
                'id'=> $product->getId(),
            ]);
        }

        return $this->render('admin/product/edit.html.twig', [
            'form'=> $form->createView(),
        ]);
    }

    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response{
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_product_index', [], Response::HTTP_SEE_OTHER);
    }
}
