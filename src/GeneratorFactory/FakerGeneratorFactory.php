<?php

namespace WebnetFr\DatabaseAnonymizer\GeneratorFactory;

use Faker\Factory;
use WebnetFr\DatabaseAnonymizer\Exception\UnsupportedGeneratorException;
use WebnetFr\DatabaseAnonymizer\Generator\Address;
use WebnetFr\DatabaseAnonymizer\Generator\City;
use WebnetFr\DatabaseAnonymizer\Generator\Country;
use WebnetFr\DatabaseAnonymizer\Generator\DateTime;
use WebnetFr\DatabaseAnonymizer\Generator\Email;
use WebnetFr\DatabaseAnonymizer\Generator\FirstName;
use WebnetFr\DatabaseAnonymizer\Generator\LastName;
use WebnetFr\DatabaseAnonymizer\Generator\Lorem;
use WebnetFr\DatabaseAnonymizer\Generator\Password;
use WebnetFr\DatabaseAnonymizer\Generator\PhoneNumber;
use WebnetFr\DatabaseAnonymizer\Generator\PostCode;
use WebnetFr\DatabaseAnonymizer\Generator\StreetAddress;
use WebnetFr\DatabaseAnonymizer\Generator\GeneratorInterface;

/**
 * Creates various generators based on faker providers.
 *
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
class FakerGeneratorFactory extends Factory implements GeneratorFactoryInterface
{
    /**
     * <generator key> => [
     *     'faker_provider' => <class name of faker provider>,
     *     'generator_class' => <class name of generator that will be instantiated>,
     * ]
     */
    const GENERATOR_MAP = [
        'address' => [
            'faker_provider' => 'Address',
            'generator_class' => Address::class,
        ],
        'city' => [
            'faker_provider' => 'Address',
            'generator_class' => City::class,
        ],
        'country' => [
            'faker_provider' => 'Address',
            'generator_class' => Country::class,
        ],
        'post_code' => [
            'faker_provider' => 'Address',
            'generator_class' => PostCode::class,
        ],
        'street_address' => [
            'faker_provider' => 'Address',
            'generator_class' => StreetAddress::class,
        ],
        'email' => [
            'faker_provider' => 'Internet',
            'generator_class' => Email::class,
        ],
        'datetime' => [
            'faker_provider' => 'DateTime',
            'generator_class' => DateTime::class,
        ],
        'first_name' => [
            'faker_provider' => 'Person',
            'generator_class' => FirstName::class,
        ],
        'last_name' => [
            'faker_provider' => 'Person',
            'generator_class' => LastName::class,
        ],
        'lorem' => [
            'faker_provider' => 'Lorem',
            'generator_class' => Lorem::class,
        ],
        'password' => [
            'faker_provider' => 'Internet',
            'generator_class' => Password::class,
        ],
        'phone_number' => [
            'faker_provider' => 'PhoneNumber',
            'generator_class' => PhoneNumber::class,
        ],
    ];

    const DEFAULT_LOCALE = 'en_US';

    /**
     * @inheritdoc
     */
    public function getGenerator(array $config): GeneratorInterface
    {
        $generatorKey = $config['generator'];
        if (!array_key_exists($generatorKey, self::GENERATOR_MAP)) {
            throw new UnsupportedGeneratorException($generatorKey.' generator is not known');
        }

        $fakerProviderName = self::GENERATOR_MAP[$generatorKey]['faker_provider'];
        $providerFQCN = self::GENERATOR_MAP[$generatorKey]['generator_class'];

        $locale = $config['locale'] ?? self::DEFAULT_LOCALE;

        $fakerProviderFQCN = $this->getProviderClassname($fakerProviderName, $locale);
        $fakerProvider = new $fakerProviderFQCN(Factory::create($locale));

        if ($config['unique'] ?? false) {
            $fakerProvider = $fakerProvider->unique();
        }

        return new $providerFQCN($fakerProvider, $config);
    }
}
