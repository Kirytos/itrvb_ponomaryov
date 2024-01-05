<?php

require_once 'src/Autoloader.php';
require 'vendor/autoload.php';

use Blog\Models\Article;
use Faker\Factory;

$fakerFactory = Factory::create();

    echo "Random text".PHP_EOL;
    echo $fakerFactory->text.PHP_EOL;

    echo "Random name".PHP_EOL;
    echo $fakerFactory->name.PHP_EOL;

    echo "Random address".PHP_EOL;
    echo $fakerFactory->address;

    $test = new Article(10001,2,3,"TESTTEST");
    echo PHP_EOL;
    echo $test->getUuid();
    echo PHP_EOL;


//    $rep = new RepositoryImpl\ArticlesRepositoryImpl();
//
//    echo $rep->get(1)->text;
?>