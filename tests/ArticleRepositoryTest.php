<?php

use Blog\Exception\ArticleNotFoundException;
use Blog\Exception\IllegalArgumentException;
use Blog\Models\Article;
use Blog\RepositoryImpl\ArticlesRepositoryImpl;
use PHPUnit\Framework\TestCase;

require 'vendor/autoload.php';
require_once 'src/Autoloader.php';

class ArticleRepositoryTest extends TestCase
{

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     * @throws IllegalArgumentException
     */
    public function testShouldSaveArticleToDatabase(): void
    {
        $statementMock = $this->createMock(PDOStatement::class);
        $connectionStub = $this->createStub(PDO::class);
        $waitingArticle = new Article(
            authorId: 10,
            title: "testTitle",
            text: "testText",
            id: 1
        );

        $statementMock->expects($this->once())->method('execute')->with([
            ':uuid' => $waitingArticle->getUuid(),
            ':author_uuid' => $waitingArticle->getAuthorUuid(),
            ':title' => $waitingArticle->getTitle(),
            ':text' => $waitingArticle->getText(),
        ]);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $repository = new ArticlesRepositoryImpl($connectionStub);
        $repository->save($waitingArticle);
    }

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     * @throws IllegalArgumentException
     * @throws ArticleNotFoundException
     */
    public function testShouldRetrieveArticleFromDatabaseByUuid(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);
        $waitingArticle = new Article(
            authorId: 10,
            title: "testTitle",
            text: "testText",
            id: 1
        );

        $statementMock->method('fetch')->willReturn([
            'title' => $waitingArticle->getTitle(),
            'author_uuid' => $waitingArticle->getAuthorUuid(),
            'text' => $waitingArticle->getText(),
            'uuid' => $waitingArticle->getUuid(),
        ]);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $repository = new ArticlesRepositoryImpl($connectionStub);

        $uuidByWaitingArticle = $waitingArticle->getUuid();
        $actualArticle = $repository->get($uuidByWaitingArticle);

        $this->assertEquals($waitingArticle->getUuid(), $actualArticle->getUuid());
        $this->assertEquals($waitingArticle->getAuthorUuid(), $actualArticle->getAuthorUuid());
        $this->assertEquals($waitingArticle->getTitle(), $actualArticle->getTitle());
        $this->assertEquals($waitingArticle->getText(), $actualArticle->getText());
    }

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     * @throws IllegalArgumentException
     */
    public function testShouldThrowsAnExceptionWhenArticleNotFound(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementStub = $this->createStub(PDOStatement::class);

        $statementStub->method('fetch')->willReturn(false);
        $connectionStub->method('prepare')->willReturn($statementStub);

        $repository = new ArticlesRepositoryImpl($connectionStub);

        $assumedUuid = 1;

        $this->expectException(ArticleNotFoundException::class);
        $this->expectExceptionMessage("Article with UUID: $assumedUuid not found!");

        $repository->get($assumedUuid);
    }

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     * @throws IllegalArgumentException
     */
    public function testShouldDeleteArticleFromDatabase(): void
    {
        $uuid = 'testUuid';

        $statementMock = $this->createMock(PDOStatement::class);
        $statementMock->expects($this->once())->method('execute')->with([':uuid' => $uuid]);

        $connectionStub = $this->createStub(PDO::class);
        $connectionStub->method('prepare')->willReturn($statementMock);

        $repository = new ArticlesRepositoryImpl($connectionStub);

        $this->expectException(ArticleNotFoundException::class);
        $this->expectExceptionMessage("Article with UUID {$uuid} not found.");

        $repository->delete($uuid);
    }


}