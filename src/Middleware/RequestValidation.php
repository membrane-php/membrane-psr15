<?php

declare(strict_types=1);

namespace Membrane\Psr15\Middleware;

use Membrane\Membrane;
use Membrane\OpenAPI\Specification\Request as MembraneRequestSpec;
use Membrane\Psr15\ContainerInterface;
use Membrane\Result\Result;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RequestValidation implements MiddlewareInterface
{
    private readonly Membrane $membrane;

    public function __construct(
        private readonly string $apiSpecPath,
        private ContainerInterface $container
    ) {
        $this->membrane = new Membrane();
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $specification = MembraneRequestSpec::fromPsr7($this->apiSpecPath, $request);
        $result = $this->membrane->process($request, $specification);

        $this->container->add(Result::class, $result);

        return $handler->handle($request);
    }
}
