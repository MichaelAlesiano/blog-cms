<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function findPublishedQueryBuilder(?Category $category = null): QueryBuilder
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.category', 'c')
            ->addSelect('c')
            ->andWhere('a.published = :published')
            ->setParameter('published', true)
            ->orderBy('a.createdAt', 'DESC');

        if ($category) {
            $qb->andWhere('a.category = :category')
               ->setParameter('category', $category);
        }

        return $qb;
    }

    public function findLatestPublished(int $limit = 5): array
    {
        return $this->findPublishedQueryBuilder()
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findOnePublishedBySlug(string $slug): ?Article
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.category', 'c')
            ->addSelect('c')
            ->leftJoin('a.tags', 't')
            ->addSelect('t')
            ->leftJoin('a.author', 'u')
            ->addSelect('u')
            ->leftJoin('a.comments', 'co')
            ->addSelect('co')
            ->andWhere('a.slug = :slug')
            ->andWhere('a.published = :published')
            ->setParameter('slug', $slug)
            ->setParameter('published', true)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllOrderedByDate(): array
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.category', 'c')
            ->addSelect('c')
            ->leftJoin('a.comments', 'co')
            ->addSelect('co')
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $qb = $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->andWhere('a.slug = :slug')
            ->setParameter('slug', $slug);

        if ($excludeId) {
            $qb->andWhere('a.id != :id')
               ->setParameter('id', $excludeId);
        }

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }

    public function getTotalViews(): int
    {
        return (int) $this->createQueryBuilder('a')
            ->select('SUM(a.views)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getTotalLikes(): int
    {
        return (int) $this->createQueryBuilder('a')
            ->select('SUM(a.likes)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
