<?php

namespace WebnetFr\DatabaseAnonymizer\Generator;

use Faker\Provider\Lorem as FakerProviderLorem;

/**
 * Fake random "lorem ipsum" text.
 *
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
class Lorem implements GeneratorInterface
{
    /**
     * @var FakerProviderLorem
     */
    private $provider;

    /**
     * @var int
     */
    private $maxLength;

    /**
     * @param FakerProviderLorem $provider
     * @param int $maxLength
     */
    public function __construct(FakerProviderLorem $provider, int $maxLength = 200)
    {
        $this->provider = $provider;
        $this->maxLength = $maxLength;
    }

    /**
     * @inheritdoc
     */
    public function generate()
    {
        return $this->provider::text();
    }
}
