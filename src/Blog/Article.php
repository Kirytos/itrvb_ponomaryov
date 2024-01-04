<?php

namespace Blog;

class Article {

    public $UUID;
    public $authorUUID;
    public $title;
    public $text;

    public function __construct($id, $authorId, $title, $text) {
        $this->UUID = $id;
        $this->authorUUID = $authorId;
        $this->title = $title;
        $this->text = $text;
    }
}
