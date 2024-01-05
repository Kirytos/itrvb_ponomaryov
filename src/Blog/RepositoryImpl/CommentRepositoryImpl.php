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

class CommentRepositoryImpl implements CommentRepository
{

    private Generator $faker;

    public function __construct(private PDO $pdo)
    {
        $this->faker = Factory::create();
    }

    /**
     * @throws IllegalArgumentException
     */
    public function save($comment): void
    {
        $statement = $this->pdo->prepare(
            "INSERT INTO comments (uuid, author_uuid, article_uuid, text) VALUES (:uuid, :author_uuid, :article_uuid, :text)"
        );
        if ($comment->getUuid() === null || $comment->getUuid() === '') {
            $comment = new Comment(
                authorId: $comment->getAuthorUuid(),
                articleId: $comment->getArticleUuid(),
                text: $comment->getText(),
                id: $this->faker->unique()->uuid
            );
        }
        try {
            $statement->execute([
                ':uuid' => (string)$comment->getUuid(),
                ':author_uuid' => $comment->getAuthorUuid(),
                ':article_uuid' => $comment->getArticleUuid(),
                ':text' => $comment->getText()
            ]);
        } catch (PDOException $exception) {
            throw new IllegalArgumentException("Save comment error with message: " . $exception->getMessage());
        }
    }

    /**
     * @throws CommentNotFoundException
     * @throws IllegalArgumentException
     */
    public function get($uuid): Comment
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM comments WHERE uuid = :uuid"
        );

        try {
            $stmt->execute([
                ":uuid" => $uuid
            ]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$result) {
                throw new CommentNotFoundException("Comment with UUID $uuid not found!");
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