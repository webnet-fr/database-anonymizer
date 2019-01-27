<?php

namespace WebnetFr\DatabaseAnonymizer\Generator;

use Faker\Provider\Address as FakerProviderAddress;

/**
 * Fake street.
 *
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
class StreetAddress implements GeneratorInterface
{
    /**
     * @var FakerProviderAddress
     */
    private $provider;

    /**
     * @param FakerProviderAddress $provider
     */
    public function __construct(FakerProviderAddress $provider)
    {
        $this->provider = $provider;
    }

    /**
     * @inheritdoc
     */
    public function generate()
    {
        return $this->provider->streetAddress();
    }
}
