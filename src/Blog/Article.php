<?php

namespace Blog;

class Article {

    private ?string $UUID;
    private string $authorUUID;
    private string $title;
    private string $text;

    public function __construct($authorId, $title, $text, $id = null) {
        $this->UUID = $id;
        $this->authorUUID = $authorId;
        $this->title = $title;
        $this->text = $text;
    }

    public function getUuid() {
        return $this->UUID;
    }

    public function getAuthorUuid(): string
    {
        return $this->authorUUID;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getText(): string
    {
        return $this->text;
    }
}
