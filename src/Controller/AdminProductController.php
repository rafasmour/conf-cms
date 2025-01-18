<?php

namespace App\Controller;


use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminProductController extends AbstractController
{
    #[Route('admin/products/', name: 'app_admin_product_index')]
    public function index(ProductRepository $repository): Response
    {

        return $this->render('/admin/product/index.html.twig', [
            'controller_name' => 'ProductController',
            'products' => $repository->findAll(),
        ]);
    }
    #[Route('/admin/products/{id<\d+>}', name: 'app_admin_product_show')]
    public function show(Product $product): Response
    {
        return $this->render('/admin/product/show.html.twig', [
            'product' => $product
        ]);
    }
    #[Route('/admin/products/new', name: 'app_admin_product_new')]
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
    #[Route('/admin/products/{id<\d+>}/edit', name: 'app_admin_product_edit')]
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
    #[Route('/admin/products/{id<\d+>}/delete', name: 'app_admin_product_delete')]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response{
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($product);
            $this->addFlash('notice','Product has been deleted');
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_product_index', [], Response::HTTP_SEE_OTHER);
    }
}