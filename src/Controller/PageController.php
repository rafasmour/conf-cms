<?php
namespace App\Controller;


use App\Entity\Page;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;

class PageController extends AbstractController {
    #[Route(path: '/', name: 'app_index')]
    public function index(EntityManagerInterface $entityManager): Response{
        $homePage = $entityManager->getRepository(Page::class)->findOneBy(['home' => true]);
        return $this->render('page/view.html.twig', [
            'page' => $homePage,
        ]);
    }

    #[Route('/{slug<.*>}', name: 'app_page_view')]
    public function showBySlug (string $slug, EntityManagerInterface $entityManager): Response
    {
        $page = $entityManager->getRepository(Page::class)->findOneBySlug($slug);
        if (empty($page) || $page->isPublished() === false){
            throw $this->createNotFoundException('Could not find page!');
        }
        if($page->isHome()){
            return $this->redirectToRoute('app_index');
        }
        return $this->render('page/view.html.twig', [
            'page' => $page,
        ]);
    }
}