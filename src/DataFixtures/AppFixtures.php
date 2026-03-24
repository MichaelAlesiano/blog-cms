<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Tag;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $slugger = new AsciiSlugger();

        // ── User admin ──
        $admin = new User();
        $admin->setEmail('admin@blog-cms.dev');
        $admin->setFullName('Michael Alesiano');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin'));
        $manager->persist($admin);

        // ── Categorie ──
        $categoriesData = [
            ['name' => 'Categoria 1', 'slug' => 'categoria-1', 'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.'],
            ['name' => 'Categoria 2', 'slug' => 'categoria-2', 'description' => 'Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'],
            ['name' => 'Categoria 3', 'slug' => 'categoria-3', 'description' => 'Ut enim ad minim veniam, quis nostrud exercitation ullamco.'],
            ['name' => 'Categoria 4', 'slug' => 'categoria-4', 'description' => 'Duis aute irure dolor in reprehenderit in voluptate velit esse.'],
        ];

        $categories = [];
        foreach ($categoriesData as $catData) {
            $category = new Category();
            $category->setName($catData['name']);
            $category->setSlug($catData['slug']);
            $category->setDescription($catData['description']);
            $manager->persist($category);
            $categories[] = $category;
        }

        // ── Tag ──
        $tagsData = ['PHP', 'Symfony', 'Laravel', 'JavaScript', 'TailwindCSS', 'MySQL', 'Git', 'API REST'];

        $tags = [];
        foreach ($tagsData as $tagName) {
            $tag = new Tag();
            $tag->setName($tagName);
            $tag->setSlug($slugger->slug($tagName)->lower()->toString());
            $manager->persist($tag);
            $tags[] = $tag;
        }

        // ── Articoli ──
        $lorem = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.\n\nDuis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.\n\nCurabitur pretium tincidunt lacus. Nulla gravida orci a odio. Nullam varius, turpis et commodo pharetra, est eros bibendum elit, nec luctus magna felis sollicitudin mauris. Integer in mauris eu nibh euismod gravida.\n\nPraesent dapibus, neque id cursus faucibus, tortor neque egestas augue, eu vulputate magna eros eu erat. Aliquam erat volutpat. Nam dui mi, tincidunt quis, accumsan porttitor, facilisis luctus, metus.";

        $articlesData = [
            ['title' => 'Lorem Ipsum Dolor Sit Amet Consectetur', 'excerpt' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore.', 'category' => 0, 'tags' => [0, 1], 'published' => true, 'daysAgo' => 2, 'views' => 142, 'likes' => 12, 'dislikes' => 1],
            ['title' => 'Sed Do Eiusmod Tempor Incididunt', 'excerpt' => 'Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo.', 'category' => 0, 'tags' => [0], 'published' => true, 'daysAgo' => 5, 'views' => 98, 'likes' => 8, 'dislikes' => 0],
            ['title' => 'Ut Enim Ad Minim Veniam Quis Nostrud', 'excerpt' => 'Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.', 'category' => 1, 'tags' => [0, 1, 7], 'published' => true, 'daysAgo' => 8, 'views' => 215, 'likes' => 24, 'dislikes' => 3],
            ['title' => 'Duis Aute Irure Dolor In Reprehenderit', 'excerpt' => 'Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim.', 'category' => 1, 'tags' => [4, 3], 'published' => true, 'daysAgo' => 12, 'views' => 76, 'likes' => 5, 'dislikes' => 2],
            ['title' => 'Excepteur Sint Occaecat Cupidatat Non Proident', 'excerpt' => 'Curabitur pretium tincidunt lacus. Nulla gravida orci a odio. Nullam varius turpis et commodo.', 'category' => 2, 'tags' => [6], 'published' => true, 'daysAgo' => 15, 'views' => 189, 'likes' => 15, 'dislikes' => 1],
            ['title' => 'Curabitur Pretium Tincidunt Lacus Nulla', 'excerpt' => 'Praesent dapibus, neque id cursus faucibus, tortor neque egestas augue, eu vulputate magna eros.', 'category' => 2, 'tags' => [5, 0], 'published' => true, 'daysAgo' => 20, 'views' => 134, 'likes' => 10, 'dislikes' => 0],
            ['title' => 'Praesent Dapibus Neque Id Cursus Faucibus', 'excerpt' => 'Aliquam erat volutpat. Nam dui mi, tincidunt quis, accumsan porttitor, facilisis luctus metus.', 'category' => 0, 'tags' => [3], 'published' => true, 'daysAgo' => 25, 'views' => 67, 'likes' => 3, 'dislikes' => 1],
            ['title' => 'Aliquam Erat Volutpat Nam Dui Mi', 'excerpt' => 'Morbi in sem quis dui placerat ornare. Pellentesque odio nisi euismod in pharetra a ultricies.', 'category' => 3, 'tags' => [3], 'published' => true, 'daysAgo' => 30, 'views' => 45, 'likes' => 2, 'dislikes' => 0],
            ['title' => 'Morbi In Sem Quis Dui Placerat Ornare', 'excerpt' => 'Fusce aliquet pede non pede. Suspendisse dapibus lorem pellentesque magna integer nulla.', 'category' => 3, 'tags' => [6], 'published' => true, 'daysAgo' => 35, 'views' => 112, 'likes' => 9, 'dislikes' => 2],
            ['title' => 'Fusce Aliquet Pede Non Pede Suspendisse', 'excerpt' => 'Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae.', 'category' => 1, 'tags' => [0, 1, 2], 'published' => true, 'daysAgo' => 40, 'views' => 88, 'likes' => 7, 'dislikes' => 1],
            ['title' => 'Vestibulum Ante Ipsum Primis In Faucibus', 'excerpt' => 'Nullam quis ante. Etiam sit amet orci eget eros faucibus tincidunt. Duis leo sed fringilla.', 'category' => 0, 'tags' => [0, 2], 'published' => false, 'daysAgo' => 1, 'views' => 0, 'likes' => 0, 'dislikes' => 0],
        ];

        $articles = [];
        foreach ($articlesData as $data) {
            $article = new Article();
            $article->setTitle($data['title']);
            $article->setSlug($slugger->slug($data['title'])->lower()->toString());
            $article->setExcerpt($data['excerpt']);
            $article->setContent($lorem);
            $article->setPublished($data['published']);
            $article->setCategory($categories[$data['category']]);
            $article->setAuthor($admin);
            $article->setViews($data['views']);
            $article->setLikes($data['likes']);
            $article->setDislikes($data['dislikes']);
            $article->setCreatedAt(new \DateTimeImmutable("-{$data['daysAgo']} days"));
            $article->setUpdatedAt(new \DateTimeImmutable("-{$data['daysAgo']} days"));

            foreach ($data['tags'] as $tagIndex) {
                $article->addTag($tags[$tagIndex]);
            }

            $manager->persist($article);
            $articles[] = $article;
        }

        // ── Commenti ──
        $commentNames = ['Marco Bianchi', 'Laura Verdi', 'Paolo Neri', 'Giulia Romano', 'Alessandro Russo'];
        $commentTexts = [
            'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Molto interessante.',
            'Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ottimo articolo!',
            'Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris.',
            'Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore.',
            'Excepteur sint occaecat cupidatat non proident. Complimenti per la chiarezza.',
            'Curabitur pretium tincidunt lacus. Nulla gravida orci a odio.',
            'Praesent dapibus, neque id cursus faucibus. Aspettavo un articolo così.',
            'Aliquam erat volutpat. Nam dui mi, tincidunt quis, accumsan porttitor.',
        ];

        $commentIndex = 0;
        foreach ($articles as $i => $article) {
            if (!$article->isPublished()) {
                continue;
            }

            $numComments = ($i % 3) + 1; // 1 a 3 commenti per articolo
            for ($j = 0; $j < $numComments; $j++) {
                $comment = new Comment();
                $comment->setAuthorName($commentNames[$commentIndex % count($commentNames)]);
                $comment->setAuthorEmail(strtolower(str_replace(' ', '.', $commentNames[$commentIndex % count($commentNames)])) . '@esempio.it');
                $comment->setContent($commentTexts[$commentIndex % count($commentTexts)]);
                $comment->setArticle($article);
                $comment->setCreatedAt(new \DateTimeImmutable("-" . ($article->getCreatedAt()->diff(new \DateTimeImmutable())->days - 1) . " days"));
                $manager->persist($comment);
                $commentIndex++;
            }
        }

        $manager->flush();
    }
}
