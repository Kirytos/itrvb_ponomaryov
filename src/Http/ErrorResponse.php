<?php

namespace Http;

/**
 * @method send()
 */
class ErrorResponse extends Response
{
    protected const SUCCESS = false;

    public function __construct(
        private string $reason = 'Some goes wrong'
    )
    {

    }

    protected function payload(): array
    {
        return ['reason' => $this->reason];
    }
}