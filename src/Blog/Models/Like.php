<?php

namespace Blog\Models;

class Like
{

    private ?string $UUID;
    private string $authorUUID;
    private string $articleUUID;

    public function __construct($authorId, $articleId, $id = null)
    {
        $this->UUID = $id;
        $this->authorUUID = $authorId;
        $this->articleUUID=$articleId;
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
}