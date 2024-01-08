<?php

namespace Blog\RepositoryImpl;

require 'vendor/autoload.php';
require_once 'src/Autoloader.php';

use Blog\Exception\CommentNotFoundException;
use Blog\Exception\IllegalArgumentException;
use Blog\Models\Comment;
use Blog\Repository\CommentRepository;
use Faker\Factory;
use Faker\Generator;
use PDO;
use PDOException;
use Psr\Log\LoggerInterface;

class CommentRepositoryImpl implements CommentRepository
{

    private Generator $faker;

    public function __construct(private readonly PDO $pdo, private LoggerInterface $loggerInterface)
    {
        $this->faker = Factory::create();
    }

    /**
     * @throws IllegalArgumentException
     */
    public function save($comment): string
    {
        if ($comment->getUuid() === null || $comment->getUuid() === '') {
            $comment = new Comment(
                authorId: $comment->getAuthorUuid(),
                articleId: $comment->getArticleUuid(),
                text: $comment->getText(),
                id: $this->faker->unique()->uuid
            );
        }

        $this->loggerInterface->info("start save comment with UUID: " . $comment->getUuid());

        $statement = $this->pdo->prepare(
            "INSERT INTO comments (uuid, author_uuid, article_uuid, text) VALUES (:uuid, :author_uuid, :article_uuid, :text)"
        );

        try {
            $statement->execute([
                ':uuid' => $comment->getUuid(),
                ':author_uuid' => $comment->getAuthorUuid(),
                ':article_uuid' => $comment->getArticleUuid(),
                ':text' => $comment->getText()
            ]);
        } catch (PDOException $exception) {
            $message = "Save comment error with message: " . $exception->getMessage();
            $this->loggerInterface->warning($message);
            throw new IllegalArgumentException($message);
        }

        return $comment->getUuid();
    }

    /**
     * @throws CommentNotFoundException
     * @throws IllegalArgumentException
     */
    public function get($uuid): Comment
    {
        $statement = $this->pdo->prepare(
            "SELECT * FROM comments WHERE uuid = :uuid"
        );

        try {
            $statement->execute([
                ":uuid" => $uuid
            ]);
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            if (!$result) {
                $message = "Comment with UUID $uuid not found!";
                $this->loggerInterface->warning($message);
                throw new CommentNotFoundException($message);
            }
        } catch (PDOException $e) {
            throw new IllegalArgumentException("Error with getting comment: " . $e->getMessage());
        }

        return $this->getComment($result);
    }

    private function getComment($result): Comment
    {
        return new Comment(
            $result['author_uuid'],
            $result['article_uuid'],
            $result['text'],
            $result['uuid']);
    }
}