<?php

namespace App\Controller;

use App\Entity\Page;
use App\Entity\Product;
use App\Form\PageType;
use App\Form\ProductType;
use App\Repository\PageRepository;
use App\Service\PageAdmin;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AdminController extends AbstractController
{
    private PageAdmin $pageAdmin;

    public function __construct(PageAdmin $pageAdmin)
    {
        $this->pageAdmin = $pageAdmin;
    }

    #[Route(path:"/admin/" , name:"app_admin_index")]
    public function index(){
        return $this->render("/admin/adminPanel/index.html.twig");
    }
    #[Route('/admin/login', name: 'app_admin_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $user = $this->getUser();
        if(!empty($user)){
            return $this->redirectToRoute('app_admin_index');
        }    
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
        return $this->render('admin/page/index.html.twig', [
            'pages' => $pageRepository->findAll(),
        ]);
    }
    #[Route('/admin/pages/new', name: 'app_admin_page_new', methods: ['GET', 'POST'])]
    public function newPage(Request $request, EntityManagerInterface $entityManager): Response
    {
        
        $page = new Page();
        $form = $this->createForm(PageType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($page);
            $entityManager->flush();
            $this->addFlash('notice','Product has been created');
            return $this->redirectToRoute('app_admin_page_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/page/new.html.twig', [
            'page' => $page,
            'form' => $form,
        ]);
    }
    #[Route('/admin/pages/{id<\d+>}', name: 'app_admin_page_show', methods: ['GET'])]
    public function showPage(Page $page): Response{
        return $this->render('admin/page/show.html.twig', [
            'page' => $page,
        ]);
    }
    #[Route('admin/pages/{id<\d+>}/edit', name: 'app_admin_page_edit', methods: ['GET', 'POST'])]
    public function editPage(Request $request, Page $page, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PageType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('notice','Page has been updated');
            return $this->redirectToRoute('app_admin_page_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/page/edit.html.twig', [
            'page' => $page,
            'form' => $form,
        ]);
    }
    #[Route('admin/pages/{id<\d+>}/delete', name: 'app_admin_page_delete', methods: ['POST'])]
    public function deletePage(Request $request, Page $page, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$page->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($page);
            $this->addFlash('notice','Page has been Deleted');
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_page_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('admin/products/', name: 'app_admin_product_index')]
    public function productIndex(ProductRepository $repository)
    {
        return $this->render('/admin/product/index.html.twig', [
            'controller_name' => 'ProductController',
            'products' => $repository->findAll(),
        ]);
    }
    #[Route('/admin/products/{id<\d+>}', name: 'app_admin_product_show')]
    public function showProduct(Product $product): Response
    {
        return $this->render('/admin/product/show.html.twig', [
            'product' => $product
        ]);
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
    #[Route('/admin/products/{id<\d+>}/delete', name: 'app_admin_product_delete')]
    public function deleteProduct( Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($product);
            $this->addFlash('notice','Product has been deleted');
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_product_index', [], Response::HTTP_SEE_OTHER);
    }
}
