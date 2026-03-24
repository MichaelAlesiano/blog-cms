<?php

namespace App\Controller\Admin;

use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Repository\TagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin', name: 'admin_')]
#[IsGranted('ROLE_ADMIN')]
class DashboardController extends AbstractController
{
    #[Route('', name: 'dashboard')]
    public function index(
        ArticleRepository $articleRepository,
        CategoryRepository $categoryRepository,
        TagRepository $tagRepository,
        CommentRepository $commentRepository,
    ): Response {
        return $this->render('admin/dashboard/index.html.twig', [
            'totalArticles' => $articleRepository->count([]),
            'publishedArticles' => $articleRepository->count(['published' => true]),
            'draftArticles' => $articleRepository->count(['published' => false]),
            'totalCategories' => $categoryRepository->count([]),
            'totalTags' => $tagRepository->count([]),
            'totalViews' => $articleRepository->getTotalViews(),
            'totalLikes' => $articleRepository->getTotalLikes(),
            'totalComments' => $commentRepository->countAll(),
            'latestArticles' => $articleRepository->findBy([], ['createdAt' => 'DESC'], 5),
            'latestComments' => $commentRepository->findLatest(5),
        ]);
    }
}
