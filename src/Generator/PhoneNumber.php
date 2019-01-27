<?php

namespace WebnetFr\DatabaseAnonymizer\Generator;

use Faker\Provider\PhoneNumber as FakerProviderPhoneNumber;

/**
 * Fake random phone number.
 *
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
class PhoneNumber implements GeneratorInterface
{
    /**
     * @var FakerProviderPhoneNumber
     */
    private $provider;

    /**
     * @param FakerProviderPhoneNumber $provider
     */
    public function __construct(FakerProviderPhoneNumber $provider)
    {
        $this->provider = $provider;
    }

    /**
     * @inheritdoc
     */
    public function generate()
    {
        return $this->provider->phoneNumber();
    }
}
