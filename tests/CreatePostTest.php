<?php

use Blog\Models\User;
use Blog\Repository\ArticlesRepository;
use Http\Actions\Articles\CreateArticle;
use Http\ErrorResponse;
use Http\Request;
use Http\SuccessfulResponse;
use PHPUnit\Framework\TestCase;
use Blog\Models\Article;

require 'vendor/autoload.php';
require_once 'src/Autoloader.php';

class CreatePostTest extends TestCase
{
    private string $correctUuid = "d8bdb073-2104-3e93-8520-bf02e3a71104";
    private string $incorrectUuid = "IncorrectUuid";

    private string $title = "title";
    private string $text = "text";
    private string $name = "name";
    private string $surname = "surname";
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disable
     * @throws JsonException
     */

    public function testThrowErrorAfterIncorrectUuid(): void
    {
        $data = [
            "author_uuid" => $this->incorrectUuid,
            "title" => $this->title,
            "text" => $this->text
        ];

        $jsonData = json_encode($data);

        $request = new Request([], [], $jsonData);

        $articleRepository = $this->articlesRepository([
            new User(
                $this->incorrectUuid,
                $this->name,
                $this->surname
            )]);

        $action = new CreateArticle($articleRepository);

        $response = $action->handle($request);
        $response->send();

        $this->expectOutputString(
            '{"success":false,"reason":"author uuid incorrect"}'
        );
        $this->assertInstanceOf(
            ErrorResponse::class, $response
        );

    }
    private function articlesRepository(array $users): ArticlesRepository
    {
        $articles = [];
        return new class($articles, $users) implements ArticlesRepository
        {
            public function __construct(
                private array $articles,
                private readonly array $users
            ) {
            }

            public function save($article): string
            {
                $flag = false;

                foreach ($this->users as $user) {
                    if ($user->getUuid() == $article->getAuthorUuid()) {
                        $flag = true;
                        break;
                    }
                }

                if (!$flag) {
                    throw new Exception;
                }

                $this->articles[] = $article;

                return "";
            }

            public function delete($uuid): string
            {
                throw new Exception;
            }

            public function get($uuid): Article
            {
                throw new Exception;
            }
        };
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disable
     * @throws JsonException
     */
    public function testGetSuccessfulResponse(): void
    {
        $data = [
            "author_uuid" => $this->correctUuid,
            "title" => $this->title,
            "text" => $this->text
        ];

        $jsonData = json_encode($data);

        $request = new Request([], [], $jsonData);

        $articleRepository = $this->articlesRepository([
            new User(
                $this->correctUuid,
                $this->name,
                $this->surname
            )]);

        $action = new CreateArticle($articleRepository);

        $response = $action->handle($request);
        $response->send();

        $this->assertInstanceOf(
            SuccessfulResponse::class, $response
        );
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disable
     * @throws JsonException
     */
    public function testThrowErrorResponseWithoutUserUuid(): void
    {
        $data = [
            "author_uuid" => $this->correctUuid,
            "title" => $this->title,
            "text" => $this->text
        ];

        $jsonData = json_encode($data);

        $request = new Request([], [], $jsonData);

        $articleRepository = $this->articlesRepository([
            new User(
                $this->incorrectUuid,
                $this->name,
                $this->surname
            )]);

        $action = new CreateArticle($articleRepository);

        $response = $action->handle($request);
        $response->send();

        $this->assertInstanceOf(
            ErrorResponse::class, $response
        );
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disable
     * @throws JsonException
     */
    public function testThrowErrorResponseWithoutFullJSONData(): void
    {
        $data = [
            'title' => $this->title,
            'text' => $this->text
        ];

        $jsonData = json_encode($data);

        $request = new Request([], [], $jsonData);

        $articleRepository = $this->articlesRepository([
            new User(
                $this->correctUuid,
                $this->name,
                $this->surname
            )]);

        $action = new CreateArticle($articleRepository);

        $response = $action->handle($request);
        $response->send();

        $this->assertInstanceOf(
            ErrorResponse::class, $response
        );
    }
}
