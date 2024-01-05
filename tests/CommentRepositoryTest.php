<?php

use Blog\Exception\CommentNotFoundException;
use Blog\Exception\IllegalArgumentException;
use Blog\Models\Comment;
use Blog\RepositoryImpl\CommentRepositoryImpl;
use PHPUnit\Framework\TestCase;

require 'vendor/autoload.php';
require_once 'src/Autoloader.php';

class CommentRepositoryTest extends TestCase
{

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     * @throws IllegalArgumentException
     */
    public function testShouldSaveCommentToDatabase(): void
    {
        $statementMock = $this->createMock(PDOStatement::class);
        $connectionStub = $this->createStub(PDO::class);
        $waitingComment = new Comment(
            authorId: 10,
            articleId: 100,
            text: "testText",
            id: 1
        );

        $statementMock->expects($this->once())->method('execute')->with([
            ':uuid' => $waitingComment->getUuid(),
            ':author_uuid' => $waitingComment->getAuthorUuid(),
            ':article_uuid' => $waitingComment->getArticleUuid(),
            ':text' => $waitingComment->getText(),
        ]);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $repository = new CommentRepositoryImpl($connectionStub);
        $repository->save($waitingComment);
    }

    /**
     * @throws CommentNotFoundException
     * @throws \PHPUnit\Framework\MockObject\Exception
     * @throws IllegalArgumentException
     */
    public function testShouldRetrieveCommentFromDatabaseByUuid(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);
        $waitingComment = new Comment(
            authorId: 10,
            articleId: 100,
            text: "testText",
            id: 1
        );

        $statementMock->method('fetch')->willReturn([
            'author_uuid' => $waitingComment->getAuthorUuid(),
            'article_uuid' => $waitingComment->getArticleUuid(),
            'text' => $waitingComment->getText(),
            'uuid' => $waitingComment->getUuid(),
        ]);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $repository = new CommentRepositoryImpl($connectionStub);

        $uuidByWaitingComment = $waitingComment->getUuid();
        $actualComment = $repository->get($uuidByWaitingComment);

        $this->assertEquals($waitingComment->getUuid(), $actualComment->getUuid());
        $this->assertEquals($waitingComment->getAuthorUuid(), $actualComment->getAuthorUuid());
        $this->assertEquals($waitingComment->getArticleUuid(), $actualComment->getArticleUuid());
        $this->assertEquals($waitingComment->getText(), $actualComment->getText());
    }

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     * @throws IllegalArgumentException
     */
    public function testItThrowsAnExceptionWhenUserNotFound(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementStub = $this->createStub(PDOStatement::class);

        $statementStub->method('fetch')->willReturn(false);
        $connectionStub->method('prepare')->willReturn($statementStub);

        $repository = new CommentRepositoryImpl($connectionStub);

        $assumedUuid = 1;

        $this->expectException(CommentNotFoundException::class);
        $this->expectExceptionMessage("Комментарий с UUID $assumedUuid не найден");

        $repository->get($assumedUuid);
    }
}