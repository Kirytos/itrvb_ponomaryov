<?php

namespace Blog\RepositoryImpl;

require_once 'src/Autoloader.php';

use Blog\Exception\ArticleNotFoundException;
use Blog\Exception\IllegalArgumentException;
use Blog\Models\Article;
use Blog\Repository\ArticlesRepository;
use PDO;
use PDOException;

class ArticlesRepositoryImpl implements ArticlesRepository
{

    public function __construct(private PDO $pdo)
    {
    }

    /**
     * @throws ArticleNotFoundException
     * @throws IllegalArgumentException
     */
    public function get($uuid): Article {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM articles WHERE uuid = :uuid"
        );

        try {
            $stmt->execute([
                ":uuid" => $uuid
            ]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$result) {
                throw new ArticleNotFoundException("Комментарий с UUID $uuid не найден");
            }
        } catch (PDOException $e) {
            throw new IllegalArgumentException("Ошибка при получении комментария: " . $e->getMessage());
        }

        return $this->getArticle($result);
    }

    /**
     * @throws IllegalArgumentException
     */
    public function save($article): void
    {
        if (!($article->getUuid() !== null && $article->getUuid() !== '')) {
            $statement = $this->pdo->prepare(
                'INSERT INTO articles (author_uuid, title, text) VALUES (:author_uuid, :title, :text)'
            );

            try {
                $statement->execute([
                    ':author_uuid' => $article->getAuthorUuid(),
                    ':title' => $article->getTitle(),
                    ':text' => $article->getText(),
                ]);
            } catch (PDOException $exception) {
                throw new IllegalArgumentException("Save articles error with message: " . $exception->getMessage());
            }
        } else {
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
            } catch (PDOException $exception) {
                throw new IllegalArgumentException("Save articles error with message: " . $exception->getMessage());
            }

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