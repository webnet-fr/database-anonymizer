<?php

namespace WebnetFr\DatabaseAnonymizer\Generator;

use Faker\Provider\Internet;

/**
 * Fake random password.
 *
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
class Password implements GeneratorInterface
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
        return $this->provider->password();
    }
}
