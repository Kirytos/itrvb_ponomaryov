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

    private string $authorId = "1";
    private string $text = "testText";
    private string $id = "10";
    private string $articleId = "11";

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     * @throws IllegalArgumentException
     */
    public function testShouldThrowsAnExceptionWhenCommentNotFound(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementStub = $this->createStub(PDOStatement::class);

        $statementStub->method('fetch')->willReturn(false);
        $connectionStub->method('prepare')->willReturn($statementStub);

        $repository = new CommentRepositoryImpl($connectionStub);

        $this->expectException(CommentNotFoundException::class);
        $this->expectExceptionMessage("Comment with UUID $this->id not found!");

        $repository->get($this->id);
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
            authorId: $this->authorId,
            articleId: $this->articleId,
            text: $this->text,
            id: $this->id
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
    public function testShouldSaveCommentToDatabase(): void
    {
        $statementMock = $this->createMock(PDOStatement::class);
        $connectionStub = $this->createStub(PDO::class);
        $waitingComment = new Comment(
            authorId: $this->authorId,
            articleId: $this->articleId,
            text: $this->text,
            id: $this->id
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
}