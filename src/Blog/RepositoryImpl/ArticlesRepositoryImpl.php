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
use Psr\Log\LoggerInterface;

class ArticlesRepositoryImpl implements ArticlesRepository
{

    private Generator $faker;

    public function __construct(private readonly PDO $pdo, private readonly LoggerInterface $loggerInterface)
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
                $message = "Article with UUID: $uuid not found!";
                $this->loggerInterface->warning("During save" . $message);
                throw new ArticleNotFoundException($message);
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
        if ($article->getUuid() === null || $article->getUuid() === '') {
            $article = new Article(
                authorId: $article->getAuthorUuid(),
                title: $article->getTitle(),
                text: $article->getText(),
                id: $this->faker->unique()->uuid
            );
        }

        $this->loggerInterface->info("start save article with UUID: ". $article->getUuid());

        $statement = $this->pdo->prepare(
            'INSERT INTO articles (uuid, author_uuid, title, text) VALUES (:uuid, :author_uuid, :title, :text)'
        );

        try {
            $statement->execute([
                ':uuid' => (string)$article->getUuid(),
                ':author_uuid' => $article->getAuthorUuid(),
                ':title' => $article->getTitle(),
                ':text' => $article->getText(),
            ]);
        } catch (PDOException $e) {
            $errorMessage = $e->getMessage();
            $this->loggerInterface->warning("Save article error with UUID: " . $article->getUuid() . " with error: " . $errorMessage);
            throw new IllegalArgumentException("Save article error with message: " . $e->getMessage());
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
                $message = "Article with UUID {$uuid} not found.";
                $this->loggerInterface->warning($message);
                throw new ArticleNotFoundException($message);
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