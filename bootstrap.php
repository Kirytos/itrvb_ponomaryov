<?php

use Blog\Repository\ArticlesRepository;
use Blog\Repository\CommentRepository;
use Blog\Repository\LikesRepository;
use Blog\RepositoryImpl\ArticlesRepositoryImpl;
use Blog\RepositoryImpl\CommentRepositoryImpl;
use Blog\RepositoryImpl\LikesRepositoryImpl;
use Container\DIContainer;
use Dotenv\Dotenv;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

require 'vendor/autoload.php';
require_once 'src/Autoloader.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$container = new DIContainer;

$connection = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');
$container->bind(PDO::class, $connection);

$container->bind(ArticlesRepository::class, ArticlesRepositoryImpl::class);
$container->bind(CommentRepository::class, CommentRepositoryImpl::class);
$container->bind(LikesRepository::class, LikesRepositoryImpl::class);

$logger = new Logger('blog');

if ($_SERVER['LOG_TO_FILES'] === 'yes') {
    $logger->pushHandler(new StreamHandler(__DIR__ . '/logs/blog.log'))
        ->pushHandler(new StreamHandler(
            __DIR__ . '/logs/blog.error.log',
            level: Level::Error,
            bubble: false
        ));
}

if ($_SERVER['LOG_TO_CONSOLE'] === 'yes') {
    $logger->pushHandler(new StreamHandler("php://stdout"));
}

$container->bind(LoggerInterface::class, $logger);

return $container;