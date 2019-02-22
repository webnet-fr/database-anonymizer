<?php

namespace WebnetFr\DatabaseAnonymizer\Tests\Unit\Generator;

use Faker\Generator;
use Faker\Provider\Address;
use PHPUnit\Framework\TestCase;
use WebnetFr\DatabaseAnonymizer\Generator\Country as CountryGenerator;

/**
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
class CountryTest extends TestCase
{
    public function testAddress()
    {
        $addressProviderMock = new AddressProviderMock();
        $generator = new CountryGenerator($addressProviderMock);
        $this->assertEquals('France', $generator->generate());
    }
}

class AddressProviderMock extends Address
{
    public function __construct()
    {
        parent::__construct(new Generator());
    }

    /**
     * @return string
     */
    public static function country()
    {
        return 'France';
    }
}
