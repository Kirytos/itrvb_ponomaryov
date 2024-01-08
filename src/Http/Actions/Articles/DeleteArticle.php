<?php

namespace Http\Actions\Articles;

use Blog\Repository\ArticlesRepository;
use Http\Actions\ActionInterface;
use Http\ErrorResponse;
use Http\SuccessfulResponse;
use Blog\Exception\RouteException;
use Http\Response;

class DeleteArticle implements ActionInterface
{
    public function __construct(private readonly ArticlesRepository $rep) { }

    public function handle($request): Response
    {
        try {
            $articleId = $request->query(
                'uuid'
            );
        } catch (RouteException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $this->rep->delete($articleId);

        return new SuccessfulResponse();
    }
}