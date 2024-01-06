<?php

namespace Blog\Exception;

require 'vendor/autoload.php';
require_once 'src/Autoloader.php';

use Exception;
use Psr\Container\NotFoundExceptionInterface;

class NotFoundException extends Exception implements NotFoundExceptionInterface
{

}