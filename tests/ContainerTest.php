<?php

declare(strict_types=1);

namespace Membrane\Psr15;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Membrane\Psr15\Container
 */
class ContainerTest extends TestCase
{
    /** @test */
    public function getTest(): void
    {
        $sut = new Container();
        self::assertNull($sut->get('a'));
        $sut->add('a', 1);
        self::assertSame(1, $sut->get('a'));
    }

    /** @test */
    public function hasTest(): void
    {
        $sut = new Container();
        self::assertFalse($sut->has('a'));
        $sut->add('a', 1);
        self::assertTrue($sut->has('a'));
    }
}
