<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/article', name: 'admin_article_')]
#[IsGranted('ROLE_ADMIN')]
class ArticleController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private SluggerInterface $slugger,
        private ArticleRepository $articleRepository,
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('admin/article/index.html.twig', [
            'articles' => $this->articleRepository->findAllOrderedByDate(),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setSlug($this->generateUniqueSlug($article->getTitle()));
            $article->setAuthor($this->getUser());

            $this->handleCoverUpload($form, $article);

            $this->em->persist($article);
            $this->em->flush();

            $this->addFlash('success', 'Articolo creato con successo.');

            return $this->redirectToRoute('admin_article_index');
        }

        return $this->render('admin/article/new.html.twig', [
            'form' => $form,
            'article' => $article,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Article $article): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setSlug($this->generateUniqueSlug($article->getTitle(), $article->getId()));

            $this->handleCoverUpload($form, $article);

            $this->em->flush();

            $this->addFlash('success', 'Articolo aggiornato con successo.');

            return $this->redirectToRoute('admin_article_index');
        }

        return $this->render('admin/article/edit.html.twig', [
            'form' => $form,
            'article' => $article,
        ]);
    }

    #[Route('/{id}/preview', name: 'preview', methods: ['GET'])]
    public function preview(Article $article, CategoryRepository $categoryRepository): Response
    {
        return $this->render('admin/article/preview.html.twig', [
            'article' => $article,
            'categories' => $categoryRepository->findAllOrderedByName(),
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Article $article): Response
    {
        if ($this->isCsrfTokenValid('delete' . $article->getId(), $request->request->get('_token'))) {
            if ($article->getCoverImage()) {
                $coverPath = $this->getParameter('app.uploads_directory') . '/' . $article->getCoverImage();
                if (file_exists($coverPath)) {
                    unlink($coverPath);
                }
            }

            $this->em->remove($article);
            $this->em->flush();

            $this->addFlash('success', 'Articolo eliminato.');
        }

        return $this->redirectToRoute('admin_article_index');
    }

    private function generateUniqueSlug(string $title, ?int $excludeId = null): string
    {
        $baseSlug = $this->slugger->slug($title)->lower()->toString();
        $slug = $baseSlug;
        $counter = 1;

        while ($this->articleRepository->slugExists($slug, $excludeId)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function handleCoverUpload($form, Article $article): void
    {
        $coverFile = $form->get('coverImageFile')->getData();

        if ($coverFile) {
            $originalFilename = pathinfo($coverFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $this->slugger->slug($originalFilename)->lower();
            $newFilename = $safeFilename . '-' . bin2hex(random_bytes(8)) . '.' . $coverFile->guessExtension();

            try {
                $coverFile->move(
                    $this->getParameter('app.uploads_directory'),
                    $newFilename
                );

                if ($article->getCoverImage()) {
                    $oldPath = $this->getParameter('app.uploads_directory') . '/' . $article->getCoverImage();
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }

                $article->setCoverImage($newFilename);
            } catch (FileException $e) {
                $this->addFlash('error', 'Errore durante il caricamento dell\'immagine.');
            }
        }
    }
}
