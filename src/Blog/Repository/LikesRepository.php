<?php

namespace Blog\Repository;

use Blog\Models\Like;

interface LikesRepository
{
    function save(Like $like) : string;
    function getByPostUuid($uuid) : array;
}