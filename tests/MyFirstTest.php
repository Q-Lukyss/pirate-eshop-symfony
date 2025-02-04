<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;

class MyFirstTest extends TestCase
{
    public function testSomething(): void
    {
        $param = true;
        $this->assertTrue($param);
    }
}
