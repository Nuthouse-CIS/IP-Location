<?php

namespace NuthouseCIS\IPLocation\Tests;

use InvalidArgumentException;
use NuthouseCIS\IPLocation\Ip;
use PHPUnit\Framework\TestCase;

class IpTest extends TestCase
{
    public function testIPv4(): void
    {
        $ip = new Ip($value = '8.8.8.8');
        $this->assertEquals($value, $ip->getValue());
        $this->assertEquals($value, (string)$ip);
    }

    public function testIPv6(): void
    {
        $ip = new Ip($value = '2001:4860:4860::8888');
        $this->assertEquals($value, $ip->getValue());
        $this->assertEquals($value, (string)$ip);
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
