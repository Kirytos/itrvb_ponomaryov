<?php

namespace Blog\RepositoryImpl;

require 'vendor/autoload.php';
require_once 'src/Autoloader.php';

use Blog\Exception\ArticleNotFoundException;
use Blog\Exception\IllegalArgumentException;
use Blog\Models\Article;
use Blog\Repository\ArticlesRepository;
use Faker\Factory;
use Faker\Generator;
use PDO;
use PDOException;

class ArticlesRepositoryImpl implements ArticlesRepository
{

    private Generator $faker;

    public function __construct(private PDO $pdo)
    {
        $this->faker = Factory::create();
    }

    /**
     * @throws ArticleNotFoundException
     * @throws IllegalArgumentException
     */
    public function get($uuid): Article
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM articles WHERE uuid = :uuid"
        );

        try {
            $stmt->execute([
                ":uuid" => $uuid
            ]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$result) {
                throw new ArticleNotFoundException("Article with UUID: $uuid not found!");
            }
        } catch (PDOException $e) {
            throw new IllegalArgumentException("Error with getting article: " . $e->getMessage());
        }

        return $this->getArticle($result);
    }

    /**
     * @throws IllegalArgumentException
     */
    public function save($article): void
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO articles (uuid, author_uuid, title, text) VALUES (:uuid, :author_uuid, :title, :text)'
        );
        if ($article->getUuid() === null || $article->getUuid() === '') {
            $article = new Article(
                authorId: $article->getAuthorUuid(),
                title: $article->getTitle(),
                text: $article->getText(),
                id: $this->faker->unique()->uuid
            );
        }
        try {
            $statement->execute([
                ':uuid' => (string)$article->getUuid(),
                ':author_uuid' => $article->getAuthorUuid(),
                ':title' => $article->getTitle(),
                ':text' => $article->getText(),
            ]);
        } catch (PDOException $exception) {
            throw new IllegalArgumentException("Save articles error with message: " . $exception->getMessage());
        }
    }

    private function getArticle($result): Article
    {
        return new Article(
            $result['author_uuid'],
            $result['title'],
            $result['text'],
            $result['uuid']
        );
    }
}