<?php

    namespace Blog\Repository;

    use Blog\Comment;

    interface CommentRepository
    {
        public function save($article) : void;
        public function get($uuid) : Comment;
    }

?>