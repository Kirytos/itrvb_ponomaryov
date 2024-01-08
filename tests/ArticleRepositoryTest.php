<?php

namespace tests;

use Blog\Exception\ArticleNotFoundException;
use Blog\Exception\IllegalArgumentException;
use Blog\Models\Article;
use Blog\RepositoryImpl\ArticlesRepositoryImpl;
use PDO;
use PDOStatement;
use Mock\TestRealizationLogger;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

require 'vendor/autoload.php';
require_once 'src/Autoloader.php';
require 'Mock/TestRealizationLogger.php';

class ArticleRepositoryTest extends TestCase
{
    private string $authorId = "1";
    private string $title = "testTitle";
    private string $text = "testText";
    private string $id = "10";

    /**
     * @throws Exception
     * @throws IllegalArgumentException
     */
    public function testShouldDeleteArticleFromDatabase(): void
    {
        $statementMock = $this->createMock(PDOStatement::class);
        $statementMock->expects($this->once())->method('execute')->with([':uuid' => $this->id]);

        $connectionStub = $this->createStub(PDO::class);
        $connectionStub->method('prepare')->willReturn($statementMock);

        $repository = new ArticlesRepositoryImpl($connectionStub, new TestRealizationLogger());

        $this->expectException(ArticleNotFoundException::class);
        $this->expectExceptionMessage("Article with UUID $this->id not found.");

        $repository->delete($this->id);
    }

    /**
     * @throws Exception
     * @throws IllegalArgumentException
     * @throws ArticleNotFoundException
     */
    public function testShouldRetrieveArticleFromDatabaseByUuid(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);
        $waitingArticle = new Article(
            authorId: $this->authorId,
            title: $this->title,
            text: $this->text,
            id: $this->id
        );

        $statementMock->method('fetch')->willReturn([
            'title' => $waitingArticle->getTitle(),
            'author_uuid' => $waitingArticle->getAuthorUuid(),
            'text' => $waitingArticle->getText(),
            'uuid' => $waitingArticle->getUuid(),
        ]);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $repository = new ArticlesRepositoryImpl($connectionStub, new TestRealizationLogger());

        $uuidByWaitingArticle = $waitingArticle->getUuid();
        $actualArticle = $repository->get($uuidByWaitingArticle);

        $this->assertEquals($waitingArticle->getUuid(), $actualArticle->getUuid());
        $this->assertEquals($waitingArticle->getAuthorUuid(), $actualArticle->getAuthorUuid());
        $this->assertEquals($waitingArticle->getTitle(), $actualArticle->getTitle());
        $this->assertEquals($waitingArticle->getText(), $actualArticle->getText());
    }

    /**
     * @throws Exception
     * @throws IllegalArgumentException
     */
    public function testShouldSaveArticleToDatabase(): void
    {
        $statementMock = $this->createMock(PDOStatement::class);
        $connectionStub = $this->createStub(PDO::class);
        $waitingArticle = new Article(
            authorId: $this->authorId,
            title: $this->title,
            text: $this->text,
            id: $this->id
        );

        $statementMock->expects($this->once())->method('execute')->with([
            ':uuid' => $waitingArticle->getUuid(),
            ':author_uuid' => $waitingArticle->getAuthorUuid(),
            ':title' => $waitingArticle->getTitle(),
            ':text' => $waitingArticle->getText(),
        ]);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $repository = new ArticlesRepositoryImpl($connectionStub, new TestRealizationLogger());
        $repository->save($waitingArticle);
    }

    /**
     * @throws Exception
     * @throws IllegalArgumentException
     */
    public function testShouldThrowsAnExceptionWhenArticleNotFound(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementStub = $this->createStub(PDOStatement::class);

        $statementStub->method('fetch')->willReturn(false);
        $connectionStub->method('prepare')->willReturn($statementStub);

        $repository = new ArticlesRepositoryImpl($connectionStub, new TestRealizationLogger());

        $this->expectException(ArticleNotFoundException::class);
        $this->expectExceptionMessage("Article with UUID: $this->id not found!");

        $repository->get($this->id);
    }
}