<?php

namespace WebnetFr\DatabaseAnonymizer\Tests\Unit\Generator;

use PHPUnit\Framework\TestCase;
use WebnetFr\DatabaseAnonymizer\Exception\InvalidConstantException;
use WebnetFr\DatabaseAnonymizer\Generator\Constant;

/**
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
class ConstantTest extends TestCase
{
    public function testReturnsNull()
    {
        $constantGenerator = new Constant(null);
        $this->assertNull($constantGenerator->generate());
        $this->assertNull($constantGenerator->generate());
    }

    public function testReturnsString()
    {
        $constantGenerator = new Constant('test string');
        $this->assertEquals($constantGenerator->generate(), 'test string');
        $this->assertEquals($constantGenerator->generate(), 'test string');
    }

    /**
     * @expectedException InvalidConstantException
     */
    public function throwsExceptionOnInvalidValue()
    {
        new Constant(1);
    }
}
