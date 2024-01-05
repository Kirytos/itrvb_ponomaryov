<?php

namespace Blog\Models;

class Comment {

    private ?string $UUID;
    private string $authorUUID;
    private string $articleUUID;
    private string $text;

    public function __construct($authorId, $articleId, $text, $id = null) {
        $this->UUID = $id;
        $this->authorUUID = $authorId;
        $this->articleUUID = $articleId;
        $this->text = $text;
    }

    public function getUuid(): ?string
    {
        return $this->UUID;
    }

    public function getAuthorUuid(): string
    {
        return $this->authorUUID;
    }

    public function getArticleUuid(): string
    {
        return $this->articleUUID;
    }

    public function getText() : string
    {
        return $this->text;
    }
}
