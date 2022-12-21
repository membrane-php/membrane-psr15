<?php

declare(strict_types=1);

namespace Membrane\Psr15\Middleware;

use Closure;
use Membrane\Psr15\ApiProblemBuilder;
use Membrane\Psr15\ContainerInterface;
use Membrane\Renderer\JsonNested;
use Membrane\Result\Result;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ResponseJsonNested
{
    public function __construct(
        private ContainerInterface $container,
        private ApiProblemBuilder $apiProblemBuilder
    ) {
    }

    public function handle(RequestInterface $request, Closure $next): ResponseInterface
    {
        $result = $this->container->get(Result::class);

        assert($result instanceof Result);
        if (!$result->isValid()) {
            return $this->apiProblemBuilder->build(new JsonNested($result));
        }

        return $next($request);
    }
}
