<?php
$connection = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');

$connection->exec(
    "INSERT INTO users (username, first_name, last_name) VALUES ('test', 'Ivan', 'Ivanov')"
);