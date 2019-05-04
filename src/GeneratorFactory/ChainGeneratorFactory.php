<?php

namespace WebnetFr\DatabaseAnonymizer\GeneratorFactory;

use WebnetFr\DatabaseAnonymizer\Exception\UnsupportedGeneratorException;
use WebnetFr\DatabaseAnonymizer\Generator\GeneratorInterface;

/**
 * Delegates the creation of the generator to one of the provided factories.
 *
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
class ChainGeneratorFactory implements GeneratorFactoryInterface
{
    /**
     * Array of generator factories.
     *
     * @var GeneratorFactoryInterface[]
     */
    private $factories = [];

    /**
     * Add factory.
     *
     * @param GeneratorFactoryInterface $factory
     *
     * @return $this
     */
    public function addFactory(GeneratorFactoryInterface $factory): self
    {
        $this->factories[] = $factory;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getGenerator(array $config): GeneratorInterface
    {
        foreach ($this->factories as $factory) {
            try {
                return $factory->getGenerator($config);
            } catch (UnsupportedGeneratorException $e) {
            }
        }

        throw new UnsupportedGeneratorException($config['generator'].' generator is not known');
    }
}
