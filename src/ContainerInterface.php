<?php

declare(strict_types=1);

namespace Membrane\Psr15;

interface ContainerInterface extends \Psr\Container\ContainerInterface
{
    public function add(string $id, mixed $service): void;
}
