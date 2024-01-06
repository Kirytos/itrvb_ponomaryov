<?php

namespace Http\Actions\Articles;

use Blog\Exception\HttpException;
use Http\Actions\ActionInterface;
use Http\ErrorResponse;
use Http\SuccessfulResponse;
use Blog\Models\Article;
use Blog\Repository\ArticlesRepository;
use Http\Response;
use Exception;
use Faker\Factory;

require 'vendor/autoload.php';
require_once 'src/Autoloader.php';

class CreateArticle implements ActionInterface
{
    public function __construct(private readonly ArticlesRepository $rep){ }

    public function handle($request): Response
    {
        $uuid = Factory::create()->uuid();

        try {
            $newArticle = new Article(
                $request->jsonBodyField('author_uuid'),
                $request->jsonBodyField('title'),
                $request->jsonBodyField('text'),
                $uuid,
            );
        } catch (HttpException) {
            return new ErrorResponse('error with getting JSON');
        }

        try {
            if (!($this->isValidUuid($newArticle->getAuthorUuid()))){
                return new ErrorResponse('author uuid incorrect');
            }

            $this->rep->save($newArticle);
        } catch (Exception){
            return new ErrorResponse('error with save');
        }

        return new SuccessfulResponse([
            'uuid' => $uuid
        ]);
    }

    private function isValidUuid(string $uuid): bool {
        $pattern = '/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[1-5][0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}$/';
        return (bool)preg_match($pattern, $uuid);
    }
}