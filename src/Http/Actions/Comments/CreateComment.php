<?php

namespace Http\Actions\Comments;

use Blog\Exception\HttpException;
use Faker\Factory;
use Http\Actions\ActionInterface;
use Http\ErrorResponse;
use Http\Response;
use Http\SuccessfulResponse;
use Blog\Models\Comment;
use Blog\Repository\CommentRepository;

require 'vendor/autoload.php';
require_once 'src/Autoloader.php';

class CreateComment implements ActionInterface
{
    public function __construct(private readonly CommentRepository $rep){ }

    /**
     */
    public function handle($request): Response
    {
        try {
            $newComment = new Comment(
                authorId: $request->jsonBodyField('author_uuid'),
                articleId: $request->jsonBodyField('article_uuid'),
                text: $request->jsonBodyField('text')
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $uuid = $this->rep->save($newComment);

        return new SuccessfulResponse([
            'uuid' => $uuid,
        ]);
    }
}