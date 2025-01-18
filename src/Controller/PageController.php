<?php

namespace App\Controller;

use App\Entity\Page;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
final class PageController extends AbstractController
{
    #[Route(path: "/", name: "app_index")]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $homePage = $entityManager->getRepository(Page::class)->findOneBy(['home' => true]);

        if (!$homePage) {
            throw $this->createNotFoundException('Home page not found!');
        }

        return $this->render('page/view.html.twig', [
            'page' => $homePage,
        ]);
    }
    #[Route('/{slug<.*>}' , name:'app_page_view')]
    public function getBySlug(string $slug, Request $request, EntityManagerInterface $manager): Response{
        $page = $manager->getRepository(Page::class)->findOneBySlug($slug);
        if (empty($page)){
            throw $this->createNotFoundException('Could not find Page!');
        }

        return $this->render('page/view.html.twig', [
            'page'=> $page,
        ]);
    }

}
