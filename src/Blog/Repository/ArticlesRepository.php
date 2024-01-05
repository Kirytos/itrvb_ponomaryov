<?php

    namespace Blog\Repository;

    use Blog\Models\Article;

    interface ArticlesRepository
    {
        public function save($article) : void;
        public function get($uuid) : Article;
    }