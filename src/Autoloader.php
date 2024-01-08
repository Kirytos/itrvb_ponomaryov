<?php

spl_autoload_register(function ($class) {
    $classPath = str_replace(['\\', '_'], [DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR], $class);

    $classFile = $classPath . '.php';

    $filePath = __DIR__ . DIRECTORY_SEPARATOR . $classFile;

    if (file_exists($filePath)) {
        require $filePath;
    }

});