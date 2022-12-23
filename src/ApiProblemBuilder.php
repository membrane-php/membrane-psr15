<?php

declare(strict_types=1);

namespace Membrane\Psr15;

use Crell\ApiProblem\ApiProblem;
use Crell\ApiProblem\HttpConverter;
use Membrane\OpenAPI\Exception\CannotProcessRequest;
use Membrane\Renderer\Renderer;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;

class ApiProblemBuilder
{
    /** @param string[] $apiResponseTypes */
    public function __construct(
        private readonly int $errorCode,
        private readonly string $errorType,
        private readonly array $apiResponseTypes
    ) {
    }

    public function buildFromRenderer(Renderer $renderer): ResponseInterface
    {
        $problem = (new ApiProblem('Request payload failed validation'))
            ->setStatus($this->errorCode)
            ->setType($this->errorType);
        $problem['errors'] = $renderer->jsonSerialize();

        return $this->convertToResponse($problem);
    }

    public function buildFromException(CannotProcessRequest $exception): ResponseInterface
    {
        $errorCode = match ($exception->getCode()) {
            0 => 404,
            1 => 405,
            2 => 406,
            default => $this->errorCode
        };

        $errorTitle = match ($errorCode) {
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            default => $this->errorType
        };

        $problem = (new ApiProblem($errorTitle))
            ->setStatus($errorCode)
            ->setType($this->apiResponseTypes[$errorCode] ?? $this->errorType)
            ->setDetail($exception->getMessage());

        return $this->convertToResponse($problem);
    }

    private function convertToResponse(ApiProblem $problem): ResponseInterface
    {
        $converter = new HttpConverter(new Psr17Factory());

        return $converter->toJsonResponse($problem);
    }
}
