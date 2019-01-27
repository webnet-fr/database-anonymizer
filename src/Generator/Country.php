<?php

namespace WebnetFr\DatabaseAnonymizer\Generator;

use Faker\Provider\Address as FakerProviderAddress;

/**
 * Fake random country.
 *
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
class Country implements GeneratorInterface
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
        return $this->provider::country();
    }
}
