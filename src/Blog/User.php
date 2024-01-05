<?php

namespace Blog;

class User {

    private string $UUID;
    private string $firstName;
    private string $lastName;

    public function __construct($id, $firstName, $lastName) {
        $this->UUID = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    public function getUuid(): string
    {
        return $this->UUID;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }
}