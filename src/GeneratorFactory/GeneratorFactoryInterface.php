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
     * The configuration contains field name, field type and any other data.
     *
     * The factory MUST throw @see \WebnetFr\DatabaseAnonymizer\Exception\UnsupportedGeneratorException
     * if it is impossible to create the generator for provided configuration.
     *
     * @param mixed $config
     *
     * @return GeneratorInterface
     *
     * @throws \WebnetFr\DatabaseAnonymizer\Exception\UnsupportedGeneratorException
     */
    public function getGenerator($config): GeneratorInterface;
}
