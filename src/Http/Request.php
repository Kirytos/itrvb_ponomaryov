<?php

namespace Http;

require 'vendor/autoload.php';
require_once 'src/Autoloader.php';

use Blog\Exception\RouteException;
use JsonException;

class Request
{
    public function __construct(
        private readonly array  $get,
        private readonly array  $server,
        private readonly string $body,
    )
    {

    }

    /**
     * @throws RouteException
     */
    public function method(): string
    {
        if (!array_key_exists('REQUEST_METHOD', $this->server)) {
            throw new RouteException('Cannot get method from request');
        }

        return $this->server['REQUEST_METHOD'];
    }

    /**
     * @throws RouteException
     */
    public function jsonBody(): array
    {
        try {
            $data = json_decode(
                $this->body,
                associative: true,
                flags: JSON_THROW_ON_ERROR
            );
        } catch (JsonException) {
            throw new RouteException('Cannot decode json body');
        }

        if (!is_array($data)) {
            throw new RouteException('Not an array/object in json body');
        }

        return $data;
    }

    /**
     * @throws RouteException
     */
    public function jsonBodyField(string $field): mixed
    {
        $data = $this->jsonBody();

        if (!array_key_exists($field, $data)) {
            throw new RouteException("No such field: $field");
        }

        if (empty($data[$field])) {
            throw new RouteException("Empty field: $field");
        }

        return $data[$field];
    }

    /**
     * @throws RouteException
     */
    public function path(): string
    {
        if (!array_key_exists('REQUEST_URI', $this->server)) {
            throw new RouteException('Cannot get path from the request');
        }

        $components = parse_url($this->server['REQUEST_URI']);

        if (!is_array($components) || !array_key_exists('path', $components)) {
            throw new RouteException('Cannot get path from the request');
        }

        return $components['path'];
    }

    /**
     * @throws RouteException
     */
    public function query(string $param): string
    {
        if (!array_key_exists($param, $this->get)) {
            throw new RouteException("No such query param in the request: $param");
        }

        $value = trim($this->get[$param]);

        if (empty($value)) {
            throw new RouteException("Empty query param in the request: $param");
        }

        return $value;
    }

    /**
     * @throws RouteException
     */
    public function header(string $header): string
    {
        $headerName = mb_strtoupper("http_" . str_replace('-', '_', $header));

        if (!array_key_exists($headerName, $this->server)) {
            throw new RouteException("No such header in the request: $header");
        }

        $value = trim($this->server[$headerName]);

        if (empty($value)) {
            throw new RouteException("Empty header in the request: $header");
        }

        return $value;
    }
}