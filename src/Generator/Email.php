<?php

namespace WebnetFr\DatabaseAnonymizer\Generator;

use Faker\Provider\Internet;

/**
 * Fake random email.
 *
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
class Email implements GeneratorInterface
{
    /**
     * @var Internet
     */
    private $provider;

    /**
     * @param Internet $provider
     */
    public function __construct(Internet $provider)
    {
        $this->provider = $provider;
    }

    /**
     * @inheritdoc
     */
    public function generate()
    {
        return $this->provider->email();
    }
}
