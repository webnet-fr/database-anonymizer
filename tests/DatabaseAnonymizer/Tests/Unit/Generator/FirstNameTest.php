<?php

namespace WebnetFr\DatabaseAnonymizer\Tests\Unit\Generator;

use PHPUnit\Framework\TestCase;
use WebnetFr\DatabaseAnonymizer\Generator\FirstName;

/**
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
class FirstNameTest extends TestCase
{
    public function testAddress()
    {
        $addressProviderMock = $this->createMock(\Faker\Provider\Person::class);
        $addressProviderMock->method('firstName')
            ->willReturn('Vladyslav');
        $generator = new FirstName($addressProviderMock);

        $this->assertEquals('Vladyslav', $generator->generate());
    }
}
