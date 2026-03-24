<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BlogController extends AbstractController
{
    #[Route('/', name: 'blog_index')]
    public function index(
        Request $request,
        ArticleRepository $articleRepository,
        CategoryRepository $categoryRepository,
        PaginatorInterface $paginator,
    ): Response {
        $categorySlug = $request->query->get('category');
        $category = null;

        if ($categorySlug) {
            $category = $categoryRepository->findOneBy(['slug' => $categorySlug]);
        }

        $queryBuilder = $articleRepository->findPublishedQueryBuilder($category);

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            6
        );

        return $this->render('blog/index.html.twig', [
            'pagination' => $pagination,
            'categories' => $categoryRepository->findAllOrderedByName(),
            'currentCategory' => $category,
        ]);
    }

    #[Route('/article/{slug}', name: 'blog_show', methods: ['GET', 'POST'])]
    public function show(
        string $slug,
        Request $request,
        ArticleRepository $articleRepository,
        CategoryRepository $categoryRepository,
        EntityManagerInterface $em,
    ): Response {
        $article = $articleRepository->findOnePublishedBySlug($slug);

        if (!$article) {
            throw $this->createNotFoundException('Articolo non trovato.');
        }

        // Incrementa views solo una volta per sessione
        $session = $request->getSession();
        $viewedArticles = $session->get('viewed_articles', []);

        if (!in_array($article->getId(), $viewedArticles)) {
            $article->incrementViews();
            $viewedArticles[] = $article->getId();
            $session->set('viewed_articles', $viewedArticles);
            $em->flush();
        }

        // Controlla se ha già votato
        $reactedArticles = $session->get('reacted_articles', []);
        $hasReacted = in_array($article->getId(), $reactedArticles);

        // Form commento
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setArticle($article);
            $em->persist($comment);
            $em->flush();

            $this->addFlash('success', 'Commento pubblicato!');

            return $this->redirectToRoute('blog_show', ['slug' => $slug]);
        }

        return $this->render('blog/show.html.twig', [
            'article' => $article,
            'categories' => $categoryRepository->findAllOrderedByName(),
            'commentForm' => $form,
            'hasReacted' => $hasReacted,
        ]);
    }

    #[Route('/article/{id}/like', name: 'blog_like', methods: ['POST'])]
    public function like(Article $article, Request $request, EntityManagerInterface $em): JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse(['error' => 'Richiesta non valida'], 400);
        }

        $session = $request->getSession();
        $reactedArticles = $session->get('reacted_articles', []);

        if (in_array($article->getId(), $reactedArticles)) {
            return new JsonResponse(['error' => 'Hai già votato questo articolo'], 403);
        }

        $article->incrementLikes();
        $reactedArticles[] = $article->getId();
        $session->set('reacted_articles', $reactedArticles);
        $em->flush();

        return new JsonResponse([
            'likes' => $article->getLikes(),
            'dislikes' => $article->getDislikes(),
        ]);
    }

    #[Route('/article/{id}/dislike', name: 'blog_dislike', methods: ['POST'])]
    public function dislike(Article $article, Request $request, EntityManagerInterface $em): JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse(['error' => 'Richiesta non valida'], 400);
        }

        $session = $request->getSession();
        $reactedArticles = $session->get('reacted_articles', []);

        if (in_array($article->getId(), $reactedArticles)) {
            return new JsonResponse(['error' => 'Hai già votato questo articolo'], 403);
        }

        $article->incrementDislikes();
        $reactedArticles[] = $article->getId();
        $session->set('reacted_articles', $reactedArticles);
        $em->flush();

        return new JsonResponse([
            'likes' => $article->getLikes(),
            'dislikes' => $article->getDislikes(),
        ]);
    }
}
