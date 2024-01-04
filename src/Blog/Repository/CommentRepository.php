<?php

    namespace Blog\Repository;

    interface CommentRepository
    {
        public function save($article);
        public function get($uuid);
    }

?>