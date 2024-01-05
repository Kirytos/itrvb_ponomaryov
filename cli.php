<?php

use Blog\Models\Article;
use Blog\Models\Comment;
use Blog\RepositoryImpl\ArticlesRepositoryImpl;
use Blog\RepositoryImpl\CommentRepositoryImpl;

require 'vendor/autoload.php';
require_once 'src/Autoloader.php';

$connection = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');

$commentRepository = new CommentRepositoryImpl($connection);
$articleRepository = new ArticlesRepositoryImpl($connection);

//$comment = new Comment(authorId: 2, articleId: 2, text: 1, id: 1238);
//$commentRepository->save($comment);

//$comment1 = new Article(2,2, 4, 1202);
//$articleRepository->save($comment1);

//echo $commentRepository->get(1)->getText();
//echo $articleRepository->get(1)->getAuthorUuid();

$faker = Faker\Factory::create();

$fakeUuid = $faker->unique()->uuid;
echo $fakeUuid;