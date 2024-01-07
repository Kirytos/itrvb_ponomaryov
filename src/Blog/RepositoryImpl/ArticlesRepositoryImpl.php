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

    public function __construct(private readonly PDO $pdo)
    {
        $this->faker = Factory::create();
    }

    /**
     * @throws ArticleNotFoundException
     * @throws IllegalArgumentException
     */
    public function get($uuid): Article
    {
        $statement = $this->pdo->prepare(
            "SELECT * FROM articles WHERE uuid = :uuid"
        );

        try {
            $statement->execute([
                ":uuid" => $uuid
            ]);
            $result = $statement->fetch(PDO::FETCH_ASSOC);
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
    public function save($article): string
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

        return $article->getUuid();
    }

    /**
     * @throws ArticleNotFoundException
     * @throws IllegalArgumentException
     */
    public function delete($uuid): string
    {
        $statement = $this->pdo->prepare('DELETE FROM articles WHERE uuid = :uuid');

        try {
            $statement->execute([':uuid' => $uuid]);
            if ($statement->rowCount() === 0) {
                throw new ArticleNotFoundException("Article with UUID {$uuid} not found.");
            }
        } catch (PDOException $exception) {
            throw new IllegalArgumentException("Delete article error with message: " . $exception->getMessage());
        }

        return $uuid;
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