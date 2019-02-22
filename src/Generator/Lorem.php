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
    private $maxNbChars;

    /**
     * @param FakerProviderLorem $provider
     * @param array $config
     */
    public function __construct(FakerProviderLorem $provider, array $config)
    {
        $this->provider = $provider;
        $this->maxNbChars = $config['max_nb_chars'] ?? 200;
    }

    /**
     * @inheritdoc
     */
    public function generate()
    {
        return $this->provider::text($this->maxNbChars);
    }
}
