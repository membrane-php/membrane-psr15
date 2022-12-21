<?php

declare(strict_types=1);

namespace Membrane\Psr15;

use Crell\ApiProblem\ApiProblem;
use Crell\ApiProblem\HttpConverter;
use Membrane\Renderer\Renderer;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;

class ApiProblemBuilder
{
    public function __construct(
        private readonly int $errorCode,
        private readonly string $errorType
    ) {
    }

    public function build(Renderer $renderer): ResponseInterface
    {
        $problem = (new ApiProblem('Request payload failed validation'))
            ->setStatus($this->errorCode)
            ->setType($this->errorType);
        $problem['errors'] = $renderer->jsonSerialize();

        $converter = new HttpConverter(new Psr17Factory());


        return $converter->toJsonResponse($problem);
    }
}
