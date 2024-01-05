<?php

    namespace Blog\Repository;

    interface ArticlesRepository
    {
        public function save($article) : void;
        public function get($uuid);
    }