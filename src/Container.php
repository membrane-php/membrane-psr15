<?php

declare(strict_types=1);

namespace Membrane\Psr15;

class Container implements ContainerInterface
{
    private array $services = [];

    public function add(string $id, mixed $service): void
    {
        $this->services[$id] = $service;
    }

    public function get(string $id)
    {
        return $this->services[$id] ?? null;
    }

    public function has(string $id): bool
    {
        return isset($this->services[$id]);
    }
}
