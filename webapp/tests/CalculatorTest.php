<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use App\Services\Calculator;

class CalculatorTest extends TestCase{
    public function testSomething(){
        $calc = new Calculator();
        $result = $calc->add(3,5);
        $this->assertEquals(8,$result);
    }
}
