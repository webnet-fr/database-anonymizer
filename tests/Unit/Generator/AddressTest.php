<?php

namespace WebnetFr\DatabaseAnonymizer\Tests\Unit\Generator;

use PHPUnit\Framework\TestCase;
use WebnetFr\DatabaseAnonymizer\Generator\Address as AddressGenerator;

/**
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
class AddressTest extends TestCase
{
    public function testAddress()
    {
        $addressProviderMock = $this->createMock(\Faker\Provider\Address::class);
        $addressProviderMock->method('address')
            ->willReturn('1 rue de Cristallerie');
        $generator = new AddressGenerator($addressProviderMock);

        $this->assertEquals('1 rue de Cristallerie', $generator->generate());
    }
}
