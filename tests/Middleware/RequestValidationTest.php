<?php

declare(strict_types=1);

namespace Membrane\Psr15\Middleware;

use Illuminate\Http\Response;
use Membrane\OpenAPI\Exception\CannotProcessRequest;
use Membrane\OpenAPI\Method;
use Membrane\Psr15\ApiProblemBuilder;
use Membrane\Psr15\Container;
use Membrane\Result\Result;
use Nyholm\Psr7\Request;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @covers \Membrane\Psr15\Middleware\RequestValidation
 */
class RequestValidationTest extends TestCase
{
    /** @test */
    public function addsResultInstanceToContainer(): void
    {
        $request = new ServerRequest('get', '/pets?limit=5&tags[]=cat&tags[]=tabby');
        $expected = Result::valid([
                'path' => [],
                'query' => ['limit' => 5, 'tags' => ['cat', 'tabby']],
                'header' => [],
                'cookie' => [],
                'body' => '',
            ]);
        $container = self::createMock(Container::class);
        $sut = new RequestValidation(
            __DIR__ . '/../fixtures/petstore-expanded.json',
            self::createStub(ApiProblemBuilder::class),
            $container
        );

        $container->expects(self::once())
            ->method('add')
            ->with(Result::class, $expected);

        $sut->process($request, self::createStub(RequestHandlerInterface::class));
    }

    public function dataSetsThatThrowCannotProcessRequest(): array
    {
        return [
            'path not found' => [
                '/hats',
                Method::GET,
                CannotProcessRequest::pathNotFound('petstore-expanded.json', '/hats'),
            ],
            'method not found' => [
                '/pets',
                Method::DELETE,
                CannotProcessRequest::methodNotFound(Method::DELETE->value),
            ],
            // TODO test 406 from unsupported content-types once Membrane is reading content-types from requests
        ];
    }


    /**
     * @test
     * @dataProvider dataSetsThatThrowCannotProcessRequest
     */
    public function catchesCannotProcessRequest(string $path, Method $method, CannotProcessRequest $expected): void
    {
        $apiProblemBuilder = self::createMock(ApiProblemBuilder::class);
        $sut = new RequestValidation(
            __DIR__ . '/../fixtures/petstore-expanded.json',
            $apiProblemBuilder,
            self::createStub(Container::class)
        );

        $apiProblemBuilder
            ->expects($this->once())
            ->method('buildFromException')
            ->with($expected);

        $sut->process(new ServerRequest($method->value, $path), self::createStub(RequestHandlerInterface::class));
    }
}
