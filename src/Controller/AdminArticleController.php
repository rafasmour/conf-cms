<?php
namespace App\Controller;
use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminArticleController extends AbstractController{
    #[Route(path: '/admin/articles/', name: 'app_admin_article_index', methods: ['GET'])]
    public function articleIndex(ArticleRepository $articleRepository): Response
    {
        return $this->render('admin/article/index.html.twig', [
            'articles' => $articleRepository->findAll(),
        ]);
    }
    #[Route('/admin/articles/new', name: 'app_admin_article_new', methods: ['GET', 'POST'])]
    public function newArticle(Request $request, EntityManagerInterface $entityManager): Response
    {

        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try{
                $entityManager->persist($article);
                $entityManager->flush();
                $this->addFlash('notice','Product has been created');
                return $this->redirectToRoute('app_admin_article_index', [], Response::HTTP_SEE_OTHER);
            } catch(\Exception $e){
                $this->addFlash('error','Error: '.$e->getMessage());
            }
        }

        return $this->render('admin/article/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }
    #[Route('/admin/articles/{id<\d+>}', name: 'app_admin_article_show', methods: ['GET'])]
    public function showArticle(Article $article): Response{
        return $this->render('admin/article/show.html.twig', [
            'article' => $article,
        ]);
    }
    #[Route('admin/articles/{id<\d+>}/edit', name: 'app_admin_article_edit', methods: ['GET', 'POST'])]
    public function editArticle(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('notice','article has been updated');
            return $this->redirectToRoute('app_admin_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/article/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }
    #[Route('admin/articles/{id<\d+>}/delete', name: 'app_admin_article_delete', methods: ['POST'])]
    public function deletearticle(Request $request, article $article, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($article);
            $this->addFlash('notice','article has been Deleted');
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_article_index', [], Response::HTTP_SEE_OTHER);
    }
    /*#[Route('/admin/articles/home/{id<\d+>}', name: 'app_admin_article_change_home', methods: ['POST', 'GET'])]
    public function changeHomeArticle(Article $article, EntityManagerInterface $entityManager): Response
    {
        $articles = $entityManager->getRepository(Article::class)->findAll();
        foreach ($articles as $p) {
            $p->setHome(false);
        }
        $article->setHome(true);
        $entityManager->flush();
        $this->addFlash('notice', 'Home article has been changed');
        return $this->redirectToRoute('app_admin_article_index', [], Response::HTTP_SEE_OTHER);
    }*/

}