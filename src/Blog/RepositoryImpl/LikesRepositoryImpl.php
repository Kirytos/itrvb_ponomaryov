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
use Psr\Log\LoggerInterface;

require 'vendor/autoload.php';
require_once 'src/Autoloader.php';

/**
 * @property $logger
 */
class LikesRepositoryImpl implements LikesRepository
{

    private Generator $faker;

    public function __construct(private readonly PDO $pdo, private readonly LoggerInterface $loggerInterface)
    {
        $this->faker = Factory::create();
    }

    /**
     * @throws IllegalArgumentException
     * @throws MoreThanOneLikeArticleException
     */
    function save($like): string
    {
        if ($like->getUuid() === null || $like->getUuid() === '') {
            $uuid = $this->faker->unique()->uuid;
            $like = new Like(
                authorId: $like->getAuthorUuid(),
                articleId: $like->getArticleUuid(),
                id: $uuid
            );
        }

        $this->loggerInterface->info("start save like with UUID: ". $like->getUuid());

        $this->loggerInterface->info("start checking count like with UUID: " . $like->getUuid());
        if ($this->checkHasLikeByArticleFromAuthor($like->getAuthorUuid(), $like->getArticleUuid())) {
            $this->loggerInterface->warning("Only one like on article! Like UUID: ". $like->getUuid());
            throw new MoreThanOneLikeArticleException("Only one like on article!");
        }

        $statement = $this->pdo->prepare(
            "insert into likes (uuid, author_uuid, article_uuid) values (:uuid, :author_uuid, :article_uuid)"
        );


        try {
            $statement->execute([
                ':uuid' => $like->getUuid(),
                ':author_uuid' => $like->getAuthorUuid(),
                ':article_uuid' => $like->getArticleUuid()
            ]);
        } catch (PDOException $e) {
            $errorMessage = $e->getMessage();
            $this->loggerInterface->warning("Save like error with UUID: " . $like->getUuid() . " with error: " . $errorMessage);
            throw new IllegalArgumentException("Save like error with message: " . $errorMessage);
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
                $message = "No likes found for article with UUID: $uuid";
                $this->loggerInterface->warning($message);
                throw new LikeNotFoundException($message);
            }
            $articles = [];

            foreach ($result as $currentArticle) {
                $articles[] = $this->getLike($currentArticle);
            }

            return $articles;
        } catch (PDOException $e) {
            throw new IllegalArgumentException("Error with getting likes with next message: " . $e->getMessage());
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