<?php

namespace Blog;

class User {

    public UUID $UUID;
    public string $firstName;
    public string $lastName;

    public function __construct($id, $firstName, $lastName) {
        $this->UUID = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }
}