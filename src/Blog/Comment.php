<?php

namespace Blog;

class Comment {

    public UUID $UUID;
    public UUID $authorUUID;
    public UUID $articleUUID;
    public string $text;

    public function __construct($id, $authorId, $articleId, $text) {
        $this->UUID = $id;
        $this->authorUUID = $authorId;
        $this->articleUUID = $articleId;
        $this->text = $text;
    }
}
