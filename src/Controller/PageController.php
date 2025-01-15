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
use Symfony\Component\Security\Core\User\UserInterface;
final class PageController extends AbstractController
{
    #[Route(path: "/", name: "app_index")]
    public function index(): Response
    {
        return $this->render('page/index.html.twig');
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
