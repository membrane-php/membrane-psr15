<?php

declare(strict_types=1);

namespace Membrane\Psr15\Middleware;

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
    public function handleRegistersResultInstanceInContainer(): void
    {
        $expected = Result::valid([
                'path' => [],
                'query' => ['limit' => 5, 'tags' => ['cat', 'tabby']],
                'header' => [],
                'cookie' => [],
                'body' => '',
            ]
        );
        $request = new ServerRequest('get', '/pets?limit=5&tags[]=cat&tags[]=tabby');
        $container = self::createMock(Container::class);
        $sut = new RequestValidation(__DIR__ . '/../fixtures/petstore-expanded.json', $container);

        $container->expects(self::once())
            ->method('add')
            ->with(Result::class, $expected);

        $sut->process($request, self::createStub(RequestHandlerInterface::class));
    }
}
