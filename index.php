<?php

require_once 'src/Autoloader.php';
require_once 'vendor/autoload.php';

use Blog\Article;
use Faker\Factory;

$fakerFactory = Factory::create();

    echo "Random text".PHP_EOL;
    echo $fakerFactory->text.PHP_EOL;

    echo "Random name".PHP_EOL;
    echo $fakerFactory->name.PHP_EOL;

    echo "Random address".PHP_EOL;
    echo $fakerFactory->address;

    $test = new Article(10000,2,3,"TESTTEST");
    echo PHP_EOL;
    echo $test->UUID;
?>