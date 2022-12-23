<?php

declare(strict_types=1);

namespace Membrane\Psr15\Middleware;

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
    /** @test */
    public function catchesCannotProcessRequest(): void
    {
        $request = new ServerRequest('get', '/hats');
        $apiProblemBuilder = self::createMock(ApiProblemBuilder::class);
        $sut = new RequestValidation(
            __DIR__ . '/../fixtures/petstore-expanded.json',
            $apiProblemBuilder,
            self::createStub(Container::class)
        );

        $apiProblemBuilder
            ->expects($this->once())
            ->method('buildFromException');

        $sut->process($request, self::createStub(RequestHandlerInterface::class));
    }
}
