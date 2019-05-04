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
     * The configuration contains:
     * - generator: the type of generator to create.
     * - any other data to configure generator.
     *
     * The factory MUST throw @see \WebnetFr\DatabaseAnonymizer\Exception\UnsupportedGeneratorException
     * if it is impossible to create the generator for provided configuration.
     *
     * @param array $config
     *
     * @throws \WebnetFr\DatabaseAnonymizer\Exception\UnsupportedGeneratorException
     *
     * @return GeneratorInterface
     */
    public function getGenerator(array $config): GeneratorInterface;
}
