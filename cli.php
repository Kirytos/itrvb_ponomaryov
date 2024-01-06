<?php

use Blog\Repository\ArticlesRepository;
use Blog\Repository\CommentRepository;
use Blog\RepositoryImpl\ArticlesRepositoryImpl;
use Blog\RepositoryImpl\CommentRepositoryImpl;
use Container\DIContainer;

require 'vendor/autoload.php';
require_once 'src/Autoloader.php';

$connection = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');
//
//$container = new DIContainer;
//
//$container->bind(ArticlesRepository::class, ArticlesRepositoryImpl::class);
//$container->bind(CommentRepository::class, CommentRepositoryImpl::class);
//
//$container->bind(PDO::class, $connection);;
//
//return $container;

//$article = $container->get(ArticlesRepository::class);
//
//echo $article->get(1);

$commentRepository = new CommentRepositoryImpl($connection);
$articleRepository = new ArticlesRepositoryImpl($connection);


echo $articleRepository->delete(111);

//$comment = new Comment(authorId: 2, articleId: 2, text: 1, id: 1238);
//$commentRepository->save($comment);

//$comment1 = new Article(2,2, 4, 1202);
//$articleRepository->save($comment1);

//echo $commentRepository->get(1)->getText();
//echo $articleRepository->get(1)->getAuthorUuid();

//$faker = Faker\Factory::create();

//$fakeUuid = $faker->unique()->uuid;
//echo $fakeUuid;