<?php

namespace WebnetFr\DatabaseAnonymizer\Tests\Unit\Generator;

use PHPUnit\Framework\TestCase;
use WebnetFr\DatabaseAnonymizer\Generator\City as CityGenerator;

/**
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
class CityTest extends TestCase
{
    public function testAddress()
    {
        $addressProviderMock = $this->createMock(\Faker\Provider\Address::class);
        $addressProviderMock->method('city')
            ->willReturn('Sèvres');
        $generator = new CityGenerator($addressProviderMock);

        $this->assertEquals('Sèvres', $generator->generate());
    }
}
