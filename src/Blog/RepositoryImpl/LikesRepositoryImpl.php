<?php

namespace Blog\RepositoryImpl;

use Blog\Exception\IllegalArgumentException;
use Blog\Exception\LikeNotFoundException;
use Blog\Exception\MoreThanOneLikeArticleException;
use Blog\Models\Like;
use Blog\Repository\LikesRepository;
use Faker\Factory;
use Faker\Generator;
use PDO;
use PDOException;

require 'vendor/autoload.php';
require_once 'src/Autoloader.php';

class LikesRepositoryImpl implements LikesRepository
{

    private Generator $faker;

    public function __construct(private readonly PDO $pdo)
    {
        $this->faker = Factory::create();
    }

    /**
     * @throws IllegalArgumentException
     * @throws MoreThanOneLikeArticleException
     */
    function save($like): string
    {
        if ($this->checkHasLikeByArticleFromAuthor($like->getAuthorUuid(), $like->getArticleUuid())) {
            throw new MoreThanOneLikeArticleException("Only one like on article!");
        }

        $statement = $this->pdo->prepare(
            "insert into likes (uuid, author_uuid, article_uuid) values (:uuid, :author_uuid, :article_uuid)"
        );

        if ($like->getUuid() === null || $like->getUuid() === '') {
            $uuid = $this->faker->unique()->uuid;
            $like = new Like(
                authorId: $like->getAuthorUuid(),
                articleId: $like->getArticleUuid(),
                id: $uuid
            );
        }

        try {
            $statement->execute([
                ':uuid' => $like->getUuid(),
                ':author_uuid' => $like->getAuthorUuid(),
                ':article_uuid' => $like->getArticleUuid()
            ]);
        } catch (PDOException $exception) {
            throw new IllegalArgumentException("Save like error with message: " . $exception->getMessage());
        }

        return $like->getUuid();
    }

    /**
     * @throws LikeNotFoundException
     * @throws IllegalArgumentException
     */
    function getByPostUuid($uuid): array
    {
        $statement = $this->pdo->prepare(
            'select * from likes where article_uuid=:uuid'
        );

        try {
            $statement->execute([
                ":uuid" => $uuid
            ]);
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);

            if (empty($result)) {
                throw new LikeNotFoundException("No likes found for article with UUID $uuid");
            }
            $articles = [];

            foreach ($result as $currentArticle) {
                $articles[] = $this->getLike($currentArticle);
            }

            return $articles;
        } catch (PDOException $e) {
            throw new IllegalArgumentException("Error with getting likes: " . $e->getMessage());
        }
    }

    /**
     * @throws IllegalArgumentException
     */
    private function checkHasLikeByArticleFromAuthor($authorUuid, $articleUuid): bool
    {
        $statement = $this->pdo->prepare(
            'SELECT * from likes 
            where article_uuid=:articleUuid 
            and author_uuid=:authorUuid'
        );

        try {
            $statement->execute([
                ":articleUuid" => $articleUuid,
                ":authorUuid" => $authorUuid
            ]);

            $result = $statement->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                return true;
            }
            return false;
        } catch (PDOException $e) {
            throw new IllegalArgumentException("Error with checking comment: " . $e->getMessage());
        }
    }

    private function getLike($result): Like
    {
        return new Like(
            authorId: $result["author_uuid"],
            articleId: $result["article_uuid"],
            id: $result["uuid"],
        );
    }
}