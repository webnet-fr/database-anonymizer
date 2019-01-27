<?php

namespace WebnetFr\DatabaseAnonymizer\GeneratorFactory;

use WebnetFr\DatabaseAnonymizer\Exception\UnsupportedGeneratorException;
use WebnetFr\DatabaseAnonymizer\Generator\DateTime;
use WebnetFr\DatabaseAnonymizer\Generator\GeneratorInterface;

/**
 * Creates the instance of @see DateTime generator.
 *
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
class DatetimeGeneratorFactory implements GeneratorFactoryInterface
{
    /**
     * @inheritdoc
     */
    public function getGenerator($config): GeneratorInterface
    {
        if ('datetime' !== $config['generator']) {
            throw new UnsupportedGeneratorException($config['generator'].' generator is not known');
        };

        $dateTimeGenerator = new DateTime($config['format']);

        if (array_key_exists('min', $config)) {
            $dateTimeGenerator->setMin($config['min']);
        }

        if (array_key_exists('max', $config)) {
            $dateTimeGenerator->setMax($config['max']);
        }

        return $dateTimeGenerator;
    }
}
