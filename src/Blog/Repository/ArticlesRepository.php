<?php

    namespace Blog\Repository;

    use Blog\Models\Article;

    interface ArticlesRepository
    {
        public function save($article) : string;
        public function get($uuid) : Article;
        public function delete($uuid) : string;
    }