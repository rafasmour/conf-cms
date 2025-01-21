<?php

namespace App\Controller;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
final class ArticleController extends AbstractController
{
    /*#[Route(path: "posts/", name: "app_index")]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $homeArticle = $entityManager->getRepository(Article::class)->findOneBy(['home' => true]);

        if (!$homeArticle) {
            throw $this->createNotFoundException('Home article not found!');
        }

        return $this->render('article/view.html.twig', [
            'article' => $homeArticle,
        ]);
    }*/
    #[Route('posts/{slug<.*>}' , name:'app_article_view')]
    public function getBySlug(string $slug, Request $request, EntityManagerInterface $manager): Response{
        $article = $manager->getRepository(Article::class)->findOneBySlug($slug);
        if (empty($article)){
            throw $this->createNotFoundException('Could not find article!');
        }

        return $this->render('article/view.html.twig', [
            'article'=> $article,
        ]);
    }

}
