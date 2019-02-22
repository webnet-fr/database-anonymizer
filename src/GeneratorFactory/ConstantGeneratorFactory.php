<?php

namespace WebnetFr\DatabaseAnonymizer\GeneratorFactory;

use WebnetFr\DatabaseAnonymizer\Exception\UnsupportedGeneratorException;
use WebnetFr\DatabaseAnonymizer\Generator\Constant;
use WebnetFr\DatabaseAnonymizer\Generator\GeneratorInterface;

/**
 * Creates the instance of @see Constant generator.
 *
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
class ConstantGeneratorFactory implements GeneratorFactoryInterface
{
    /**
     * @inheritdoc
     */
    public function getGenerator(array $config): GeneratorInterface
    {
        if ('constant' !== $config['generator']) {
            throw new UnsupportedGeneratorException($config['generator'].' generator is not known');
        };

        return new Constant($config['value']);
    }
}
