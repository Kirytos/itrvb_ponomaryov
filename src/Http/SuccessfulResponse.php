<?php
namespace Http;

require 'vendor/autoload.php';
require_once 'src/Autoloader.php';

class SuccessfulResponse extends Response
{
    protected const SUCCESS = true;

    public function __construct(
        private readonly array $data = []
    )
    {

    }

    protected function payload(): array
    {
        return ['data' => $this->data];
    }
}