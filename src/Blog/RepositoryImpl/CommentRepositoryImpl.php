<?php

namespace Blog\RepositoryImpl;

require_once 'src/Autoloader.php';


use Blog\Exception\CommentNotFoundException;
use Blog\Exception\IllegalArgumentException;
use Blog\Models\Comment;
use Blog\Repository\CommentRepository;
use PDO;
use PDOException;

class CommentRepositoryImpl implements CommentRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    /**
     * @throws IllegalArgumentException
     */
    public function save($comment): void
    {
        if (!($comment->getUuid() !== null && $comment->getUuid() !== '')) {
            $statement = $this->pdo->prepare(
                "INSERT INTO comments (author_uuid, article_uuid, text) VALUES (:author_uuid, :article_uuid, :text)"
            );
            try {
                $statement->execute([
                    ':author_uuid' => $comment->getAuthorUuid(),
                    ':article_uuid' => $comment->getArticleUuid(),
                    ':text' => $comment->getText()
                ]);
            } catch (PDOException $exception) {
                throw new IllegalArgumentException("Save comment error with message: " . $exception->getMessage());
            }
        } else {
            $statement = $this->pdo->prepare(
                "INSERT INTO comments (uuid, author_uuid, article_uuid, text) VALUES (:uuid, :author_uuid, :article_uuid, :text)"
            );
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
    }

    /**
     * @throws CommentNotFoundException
     * @throws IllegalArgumentException
     */
    public function get($uuid): Comment {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM comments WHERE uuid = :uuid"
        );

        try {
            $stmt->execute([
                ":uuid" => $uuid
            ]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$result) {
                throw new CommentNotFoundException("Комментарий с UUID $uuid не найден");
            }
        } catch (PDOException $e) {
            throw new IllegalArgumentException("Ошибка при получении комментария: " . $e->getMessage());
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