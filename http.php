<?php

require 'vendor/autoload.php';
require_once 'src/Autoloader.php';

use Http\Actions\Articles\CreateArticle;
use Http\Actions\Articles\DeleteArticle;
use Http\Actions\Comments\CreateComment;
use Http\Actions\Likes\CreateLike;
use Http\ErrorResponse;
use Http\Request;
use Blog\Exception\RouteException;
use Psr\Log\LoggerInterface;

$container = require __DIR__ . '/bootstrap.php';

$request = new Request($_GET, $_SERVER, file_get_contents('php://input'));

$logger = $container->get(LoggerInterface::class);

try {
    $path = $request->path();
} catch (RouteException $e) {
    $logger->warning($e->getMessage());
    (new ErrorResponse)->send();
    return;
}

try {
    $method = $request->method();
} catch (RouteException $e) {
    $logger->warning($e->getMessage());
    (new ErrorResponse)->send();
    return;
}

$routes = [
    'DELETE' => [
        '/posts' => DeleteArticle::class
    ],
    'POST' => [
        '/posts' => CreateArticle::class,
        '/posts/comment' => CreateComment::class,
        '/posts/likes' => CreateLike::class
    ]
];

if (!array_key_exists($method, $routes)) {
    $logger->notice("Error route: $method $path");
    (new ErrorResponse('Not found'))->send();
    return;
}

if (!array_key_exists($path, $routes[$method])) {
    $logger->notice("Error route: $method $path");
    (new ErrorResponse('Not found'))->send();
    return;
}

try {
    $response = $container
        ->get($routes[$method][$path])
        ->handle($request);

    $response->send();
} catch (Exception $error) {
    $logger->error($error->getMessage(), ['exception' => $error]);
    (new ErrorResponse($error->getMessage()))->send();
}
