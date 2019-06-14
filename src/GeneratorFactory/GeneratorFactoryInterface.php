<?php

namespace WebnetFr\DatabaseAnonymizer\GeneratorFactory;

use WebnetFr\DatabaseAnonymizer\Generator\GeneratorInterface;

/**
 * The factory that creates the instance of @see GeneratorInterface given the configuration.
 *
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
interface GeneratorFactoryInterface
{
    /**
     * Create the generator out of provided configuration.
     *
     * @param array $config
     *        An array of the configuration for field to anonymize. It contains
     *        all specified entries, like "generator", "unique", "date_format",
     *        "my_custom_entry", etc.
     *
     * @throws \WebnetFr\DatabaseAnonymizer\Exception\UnsupportedGeneratorException
     *          The factory MUST throw "UnsupportedGeneratorException" if it is
     *          impossible to create the generator for provided configuration.
     *
     * @return GeneratorInterface
     */
    public function getGenerator(array $config): GeneratorInterface;
}
