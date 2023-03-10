<?php

declare(strict_types=1);

namespace Membrane\Psr15;

use Membrane\OpenAPI\Exception\CannotProcessRequest;
use Membrane\Renderer\Renderer;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Membrane\Psr15\ApiProblemBuilder
 */
class ApiProblemBuilderTest extends TestCase
{
    /** @test */
    public function buildFromRendererTest(): void
    {
        $expected = [
            'errors' => [
                'id' => ['must be an integer'],
            ],
            'title' => 'Request payload failed validation',
            'status' => 400,
            'type' => 'about:blank',

        ];

        $sut = new ApiProblemBuilder(400, 'about:blank', []);
        $renderer = self::createMock(Renderer::class);
        $renderer->expects(self::once())
            ->method('jsonSerialize')
            ->willReturn(['id' => ['must be an integer']]);

        $actual = $sut->buildFromRenderer($renderer);

        self::assertEquals($expected, json_decode($actual->getBody()->getContents(), true));
    }

    public function dataSetsToBuildFromException(): array
    {
        return [
            'path not found, no apiResponseTypes' => [
                CannotProcessRequest::pathNotFound('api.json', '/pets'),
                [
                    'title' => 'Not Found',
                    'type' => 'about:blank',
                    'status' => 404,
                    'detail' => '/pets does not match any specified paths in api.json'
                ],
                [],
            ],
            'path not found, no applicable apiResponseType' => [
                CannotProcessRequest::pathNotFound('api.json', '/pets'),
                [
                    'title' => 'Not Found',
                    'type' => 'about:blank',
                    'status' => 404,
                    'detail' => '/pets does not match any specified paths in api.json',
                ],
                [418 => 'I\'m a teapot'],
            ],
            'path not found, applicable apiResponseType' => [
                CannotProcessRequest::pathNotFound('api.json', '/pets'),
                [
                    'title' => 'Not Found',
                    'type' => 'Path Not Found',
                    'status' => 404,
                    'detail' => '/pets does not match any specified paths in api.json',
                ],
                [404 => 'Path Not Found', 418 => 'I\'m a teapot'],
            ],
            'method not found, applicable apiResponseType' => [
                CannotProcessRequest::methodNotFound('get'),
                [
                    'title' => 'Method Not Allowed',
                    'type' => 'Method Not Found',
                    'status' => 405,
                    'detail' => 'get operation not specified on path',
                ],
                [404 => 'Path Not Found', 405 => 'Method Not Found', 418 => 'I\'m a teapot'],
            ],
            'content type not supported, applicable apiResponseType' => [
                CannotProcessRequest::unsupportedContent(),
                [
                    'title' => 'Not Acceptable',
                    'type' => 'Not Accepted',
                    'status' => 406,
                    'detail' => 'APISpec expects application/json content',
                ],
                [404 => 'Path Not Found', 405 => 'Method Not Found', 406 => 'Not Accepted', 418 => 'I\'m a teapot'],
            ],
        ];
    }

    /**
     * @test
     * @dataProvider dataSetsToBuildFromException
     */
    public function buildFromExceptionTest(
        CannotProcessRequest $exception,
        array $expected,
        array $apiResponseTypes
    ): void {
        $sut = new ApiProblemBuilder(400, 'about:blank', $apiResponseTypes);

        $actual = $sut->buildFromException($exception);

        self::assertSame($expected, json_decode($actual->getBody()->getContents(), true));
    }
}
