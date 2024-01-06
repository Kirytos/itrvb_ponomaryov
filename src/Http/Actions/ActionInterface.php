<?php

namespace Http\Actions;

require 'vendor/autoload.php';
require_once 'src/Autoloader.php';

use Http\Request;
use Http\Response;

interface ActionInterface
{
    public function handle(Request $request): Response;
}