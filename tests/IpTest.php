<?php

declare(strict_types=1);

namespace NuthouseCIS\IPLocation\Tests;

use InvalidArgumentException;
use NuthouseCIS\IPLocation\Ip;
use PHPUnit\Framework\TestCase;

class IpTest extends TestCase
{
    public function testIPv4(): void
    {
        $ip = new Ip($value = '8.8.8.8');
        $this->assertSame($value, $ip->getValue());
        $this->assertSame($value, (string)$ip);
        $this->assertTrue($ip->isVersion4());
        $this->assertFalse($ip->isVersion6());
    }

    public function testIPv6(): void
    {
        $ip = new Ip($value = '2001:4860:4860::8888');
        $this->assertSame($value, $ip->getValue());
        $this->assertSame($value, (string)$ip);
        $this->assertTrue($ip->isVersion6());
        $this->assertFalse($ip->isVersion4());
    }

    public function testInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Ip('incorrect');
    }

    public function testEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Ip('');
    }

    public function testInvalidIPv4(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Ip('256.256.256.256');
    }

    public function testInvalidIPv6(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Ip('8:8:8:8:8:8');
    }
}
