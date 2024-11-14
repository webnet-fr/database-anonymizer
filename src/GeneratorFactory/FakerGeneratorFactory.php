<?php

namespace WebnetFr\DatabaseAnonymizer\GeneratorFactory;

use Faker\Factory;
use WebnetFr\DatabaseAnonymizer\Exception\MissingFormatterException;
use WebnetFr\DatabaseAnonymizer\Exception\UnsupportedGeneratorException;
use WebnetFr\DatabaseAnonymizer\Generator\FakerGenerator;
use WebnetFr\DatabaseAnonymizer\Generator\GeneratorInterface;

/**
 * Creates various generators based on faker providers.
 *
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
class FakerGeneratorFactory extends Factory implements GeneratorFactoryInterface
{
    public const DEFAULT_LOCALE = 'en_US';

    /**
     * {@inheritdoc}
     */
    public function getGenerator(array $config): GeneratorInterface
    {
        $generatorKey = $config['generator'];
        if ('faker' !== $generatorKey) {
            throw new UnsupportedGeneratorException($generatorKey . ' generator is not known');
        }

        $formatter = $config['formatter'] ?? null;
        if (!$formatter) {
            throw new MissingFormatterException('You need to chose a "formatter" for "faker" generator');
        }

        $locale = $config['locale'] ?? self::DEFAULT_LOCALE;
        $generator = Factory::create($locale);

        $seed = $config['seed'] ?? false;
        if ($seed) {
            $generator->seed($seed);
        }

        if ($config['unique'] ?? false) {
            $generator = $generator->unique();
        }

        $optional = $config['optional'] ?? false;
        if ($optional) {
            $generator = $generator->optional($optional);
        }

        $arguments = $config['arguments'] ?? [];

        return new FakerGenerator($generator, $formatter, $arguments, $config);
    }
}
