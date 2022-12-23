<?php

declare(strict_types=1);

namespace Membrane\Psr15\Middleware;

use Membrane\Psr15\ApiProblemBuilder;
use Membrane\Psr15\Container;
use Membrane\Result\FieldName;
use Membrane\Result\Message;
use Membrane\Result\MessageSet;
use Membrane\Result\Result;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

/**
 * @covers \Membrane\Psr15\Middleware\ResponseJsonFlat
 * @uses \Membrane\Psr15\ApiProblemBuilder
 */
class ResponseJsonFlatTest extends TestCase
{
    public function dataSetsToHandle(): array
    {
        return [
            'valid results return valid responses' => [
                Result::valid(1),
                200,
                ''
            ],
            'invalid result returns response with ApiProblem' => [
                Result::invalid(
                    1,
                    new MessageSet(new FieldName('id', 'pet'), new Message('must be an integer', [])),
                    new MessageSet(new FieldName('pet'), new Message('%s is a required field', ['name']))
                ),
                400,
                '{"errors":{' .
                '"pet->id":["must be an integer"],' .
                '"pet":["name is a required field"]},' .
                '"title":"Request payload failed validation","type":"about:blank","status":400}'
            ],
        ];
    }

    /**
     * @test
     * @dataProvider dataSetsToHandle
     */
    public function handleTest(Result $result, int $expectedStatus, string $expectedContent): void
    {
        $request = self::createStub(RequestInterface::class);
        $container = self::createMock(Container::class);
        $apiProblemBuilder = new ApiProblemBuilder(400, 'about:blank', []);
        $sut = new ResponseJsonFlat($container, $apiProblemBuilder);

        $container->expects(self::once())
            ->method('get')
            ->with(Result::class)
            ->willReturn($result);

        $actual = $sut->handle($request, fn($var) => new Response());

        self::assertSame($expectedStatus, $actual->getStatusCode());
        self::assertSame($expectedContent, $actual->getBody()->getContents());
    }
}
