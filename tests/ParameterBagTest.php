<?php

declare(strict_types=1);

namespace Benedya\Url\Tests;

use Benedya\Url\ParameterBag;
use PHPUnit\Framework\TestCase;

class ParameterBagTest extends TestCase
{
    /** @var ParameterBag */
    private $parameterBag;

    public function setUp(): void
    {
        $this->parameterBag = new ParameterBag(['foo' => 'bar', 'bar' => 'foo', 'baz' => 10]);
    }

    public function testCount(): void
    {
        $this->assertCount(3, $this->parameterBag);
    }

    public function testHas(): void
    {
        $this->assertTrue($this->parameterBag->has('foo'));
    }

    public function testGet(): void
    {
        $this->assertSame('bar', $this->parameterBag->get('foo'));
        $this->assertSame('10', $this->parameterBag->get('baz'));
    }

    public function testGetDefault(): void
    {
        $this->assertSame('111', $this->parameterBag->get('undefined', '111'));
    }

    public function testSet(): void
    {
        $this->parameterBag->set('foo', 'new bar');

        $this->assertSame('new bar', $this->parameterBag->get('foo'));
    }

    public function testAll(): void
    {
        $this->assertSame(['foo' => 'bar', 'bar' => 'foo', 'baz' => 10], $this->parameterBag->all());
    }

    public function testToString(): void
    {
        $this->assertEquals('foo=bar&bar=foo&baz=10', $this->parameterBag . '');
    }
}
