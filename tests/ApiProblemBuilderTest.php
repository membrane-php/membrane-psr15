<?php

declare(strict_types=1);

namespace Membrane\Psr15;

use Membrane\Renderer\Renderer;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Membrane\Psr15\ApiProblemBuilder
 */
class ApiProblemBuilderTest extends TestCase
{
    /** @test */
    public function buildsApiProblemImplementingServerResponseInterface(): void
    {
        $expected = [
            'errors' => [
                'id' => ['must be an integer'],
            ],
            'title' => 'Request payload failed validation',
            'status' => 400,
            'type' => 'about:blank',

        ];

        $sut = new ApiProblemBuilder(400, 'about:blank');
        $renderer = self::createMock(Renderer::class);
        $renderer->expects(self::once())
            ->method('jsonSerialize')
            ->willReturn(['id' => ['must be an integer']]);

        $actual = $sut->build($renderer);

        self::assertEquals($expected, json_decode($actual->getBody()->getContents(), true));
    }
}
