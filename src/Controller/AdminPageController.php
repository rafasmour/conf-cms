<?php
namespace App\Controller;
use App\Entity\Page;
use App\Form\PageType;
use App\Repository\PageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminPageController extends AbstractController{
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
            try{
                $entityManager->persist($page);
                $entityManager->flush();
                $this->addFlash('notice','Product has been created');
                return $this->redirectToRoute('app_admin_page_index', [], Response::HTTP_SEE_OTHER);
            } catch(\Exception $e){
                $this->addFlash('error','Error: '.$e->getMessage());
            }
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
    #[Route('/admin/pages/home/{id<\d+>}', name: 'app_admin_page_change_home', methods: ['POST', 'GET'])]
    public function changeHomePage(Page $page, EntityManagerInterface $entityManager): Response
    {
        $pages = $entityManager->getRepository(Page::class)->findAll();
        foreach ($pages as $p) {
            $p->setHome(false);
        }
        $page->setHome(true);
        $entityManager->flush();
        $this->addFlash('notice', 'Home Page has been changed');
        return $this->redirectToRoute('app_admin_page_index', [], Response::HTTP_SEE_OTHER);
    }

}