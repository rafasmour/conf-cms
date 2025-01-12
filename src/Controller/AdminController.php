<?php

namespace App\Controller;

use App\Entity\Page;
use App\Entity\Product;
use App\Repository\PageRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AdminController extends AbstractController
{
    #[Route('/admin/login', name: 'app_admin_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->forward(
            'App\Controller\SecurityController::login',
            ['authenticationUtils' => $authenticationUtils]
        );
    }
    #[Route('/admin/logout', name: 'app_admin_logout')]
    public function logout(): Response
    {
        return $this->forward(
            'App\Controller\SecurityController::logout'
        );
    }
    #[Route(path: '/admin/pages/', name: 'app_admin_page_index', methods: ['GET'])]
    public function pageIndex(PageRepository $pageRepository): Response
    {
        return $this->forward(
            'App\Controller\PageController::index',
            ['pageRepository' => $pageRepository]
        );
    }
    #[Route('/admin/pages/new', name: 'app_admin_page_new', methods: ['GET', 'POST'])]
    public function newPage(Request $request, EntityManagerInterface $entityManager): Response
    {
        return $this->forward(
            'App\Controller\PageController::new',
            [
                'request' => $request,
                'entityManager', $entityManager
            ]);
    }
    #[Route('/admin/pages/{id<\d+>}', name: 'app_admin_page_show', methods: ['GET'])]
    public function showPage(Page $page): Response{
        return $this->forward(
            'App\Controller\PageController::show',
            ['page' => $page]);
    }
    #[Route('admin/pages/{id<\d+>}/edit', name: 'app_admin_page_edit', methods: ['GET', 'POST'])]
    public function editPage(Request $request, Page $page, EntityManagerInterface $entityManager): Response
    {
        return $this->forward(
            'App\Controller\PageController::edit',
            [
                'request' => $request,
                'page' => $page,
                'entityManager' => $entityManager
            ]
        );    }
    #[Route('admin/pages/{id<\d+>}/delete', name: 'app_admin_page_delete', methods: ['POST'])]
    public function deletePage(Request $request, Page $page, EntityManagerInterface $entityManager): Response
    {
        return $this->forward(
            'App\Controller\PageController::delete',
            ['request'=> $request, 
             'page' => $page, 
             'entityManager' => $entityManager
        ]);
    }
    #[Route('admin/products/', name: 'app_admin_product_index')]
    public function productIndex(ProductRepository $repository)
    {
        return $this->forward(
            'App\Controller\ProductController::index',
            ['repository' => $repository]
        );
    }
    #[Route('/admin/products/{id<\d+>}', name: 'app_admin_product_show')]
    public function showProduct(Product $product): Response
    {
        return $this->forward(
            'App\Controller\ProductController::show',
            ['product' => $product]
        );
    }
    #[Route('/admin/products/new', name: 'app_admin_product_new')]
    public function newProduct(Request $request, EntityManagerInterface $entityManager): Response
    {
        return $this->forward(
            'App\Controller\ProductController::new',
            [
                'request' => $request,
                'entityManager' => $entityManager
            ]
        );
    }
    #[Route('/admin/products/{id<\d+>}/edit', name: 'app_admin_product_edit')]
    public function editProduct(Product $product, Request $request, EntityManagerInterface $entityManager): Response
    {
        return $this->forward(
            'App\Controller\ProductController::edit',
            [
                'product' => $product,
                'request' => $request,
                'entityManager' => $entityManager 
            ]
        );
    }
    #[Route('/admin/products/{id<\d+>}/delete', name: 'app_admin_product_delete')]
    public function deleteProduct( Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        return $this->forward(
            'App\Controller\ProductController::delete',
            [
                'request' => $request,
                'product' => $product,
                'entityManager' => $entityManager
            ]
        );
    }
}
