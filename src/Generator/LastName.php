<?php

namespace WebnetFr\DatabaseAnonymizer\Generator;

use Faker\Provider\Person;

/**
 * Fake random last name.
 *
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
class LastName implements GeneratorInterface
{
    /**
     * @var Person
     */
    private $provider;

    /**
     * @param Person $provider
     */
    public function __construct(Person $provider)
    {
        $this->provider = $provider;
    }

    /**
     * @inheritdoc
     */
    public function generate()
    {
        return $this->provider->lastName();
    }
}
