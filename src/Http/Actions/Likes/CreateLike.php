<?php

namespace Http\Actions\Likes;

use Blog\Exception\HttpException;
use Blog\Models\Like;
use Blog\Repository\LikesRepository;
use Faker\Factory;
use Http\Actions\ActionInterface;
use Http\ErrorResponse;
use Http\Response;
use Http\SuccessfulResponse;

require 'vendor/autoload.php';
require_once 'src/Autoloader.php';

class CreateLike implements ActionInterface
{
    public function __construct(
        private readonly LikesRepository $rep,
    )
    {
    }

    /**
     */
    public function handle($request): Response
    {
        try {
            $like = new Like(
                authorId: $request->jsonBodyField('author_uuid'),
                articleId: $request->jsonBodyField('article_uuid')
            );
        } catch (HttpException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        $uuid = $this->rep->save($like);

        return new SuccessfulResponse([
            'uuid' => $uuid,
        ]);
    }
}