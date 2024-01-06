<?php

namespace Container;

require 'vendor/autoload.php';
require_once 'src/Autoloader.php';

use Blog\Exception\NotFoundException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionClass;

class DIContainer implements ContainerInterface
{
    private array $resolves = [];

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function has(string $id): bool
    {
        try {
            $this->get($id);
        } catch (NotFoundException) {
            return false;
        }

        return true;
    }

    public function bind(string $type, $class): void
    {
        $this->resolves[$type] = $class;
    }

    public function get($type): object
    {
        if (array_key_exists($type, $this->resolves)) {
            $typeToCreate = $this->resolves[$type];

            if (is_object($typeToCreate)) {
                return $typeToCreate;
            }

            return $this->get($typeToCreate);
        }

        if (!class_exists($type)) {
            throw new NotFoundException("Cannot resolve type: $type");
        }

        $reflectionClass = new ReflectionClass($type);

        $constructor = $reflectionClass->getConstructor();

        if ($constructor === null) {
            return new $type();
        }

        $parameters = [];

        foreach ($constructor->getParameters() as $parameter) {
            $parameterType = $parameter->getType()->getName();

            $parameters[] = $this->get($parameterType);
        }

        return new $type(...$parameters);
    }
}