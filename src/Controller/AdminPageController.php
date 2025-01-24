<?php

namespace App\Controller;

use App\Entity\Page;
use App\Form\PageType;
use App\Repository\PageRepository;
use App\Service\EnvService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/page')]
final class AdminPageController extends AbstractController
{
    private EnvService $envService;

    public function __construct(EnvService $envService)
    {
        $this->envService = $envService;
    }

    #[Route(name: 'app_admin_page_index', methods: ['GET'])]
    public function index(PageRepository $pageRepository): Response
    {
        return $this->render('admin/page/index.html.twig', [
            'pages' => $pageRepository->findAll(),
        ]);
    }

    #[Route('/admin/pages/new', name: 'app_admin_page_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $page = new Page();
        $form = $this->createForm(PageType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($page);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_page_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/page/new.html.twig', [
            'page' => $page,
            'form' => $form,
            'tinymce_api_key' => $this->envService->get('TINYMCE_API_KEY'),
        ]);
    }

    #[Route('/admin/pages/{id}', name: 'app_admin_page_show', methods: ['GET'])]
    public function show(Page $page): Response
    {
        return $this->render('admin/page/show.html.twig', [
            'page' => $page,
        ]);
    }

    #[Route('/admin/pages/{id}/edit', name: 'app_admin_page_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Page $page, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PageType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_page_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('admin/page/edit.html.twig', [
            'page' => $page,
            'form' => $form,
            'tinymce_api_key' => $this->envService->get('TINYMCE_API_KEY'),
        ]);
    }

    #[Route('/admin/pages/{id}/delete', name: 'app_admin_page_delete', methods: ['POST', 'GET'])]
    public function delete(Request $request, Page $page, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$page->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($page);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_page_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/admin/pages/{id}/home', name: 'app_admin_page_set_home', methods: ['POST', 'GET'])]
    public function setHome(Request $request, Page $page, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('set_home' . $page->getId(), $request->request->get('_token'))) {
            // Set all other pages' home field to false
            $entityManager->createQuery('UPDATE App\Entity\Page p SET p.home = false WHERE p.home = true')
                ->execute();

            // Set the specified page's home field to true
            $page->setHome(true);
            $entityManager->flush();
        } else {
            $this->addFlash('error', 'Invalid CSRF token.');
        }

        return $this->redirectToRoute('app_admin_page_index', [], Response::HTTP_SEE_OTHER);
    }
}