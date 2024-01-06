<?php

use Blog\Repository\ArticlesRepository;
use Blog\Repository\CommentRepository;
use Blog\RepositoryImpl\ArticlesRepositoryImpl;
use Blog\RepositoryImpl\CommentRepositoryImpl;
use Container\DIContainer;

require 'vendor/autoload.php';
require_once 'src/Autoloader.php';

$container = new DIContainer;

$container->bind(ArticlesRepository::class, ArticlesRepositoryImpl::class);
$container->bind(CommentRepository::class, CommentRepositoryImpl::class);


$connection = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');
$container->bind(PDO::class, $connection);;

return $container;