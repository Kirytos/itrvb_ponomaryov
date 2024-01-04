<?php

    namespace Blog\Repository;

    interface ArticlesRepository
    {
        public function save($article);
        public function get($uuid);
    }

?>