<?php

namespace WebnetFr\DatabaseAnonymizer;

use WebnetFr\DatabaseAnonymizer\Generator\GeneratorInterface;

/**
 * Encapsulates the name of the field within @see TargetTable.
 * Contains generator for getting random anonymous values.
 *
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
class TargetField
{
    /**
     * Name of the field.
     *
     * @var string
     */
    private $name;

    /**
     * Random value generator.
     *
     * @var GeneratorInterface
     */
    private $generator;

    /**
     * @param string $name
     * @param GeneratorInterface $generator
     */
    public function __construct(string $name, GeneratorInterface $generator)
    {
        $this->name = $name;
        $this->generator = $generator;
    }

    /**
     * Get the name of this field.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Generate next random value for this field.
     *
     * @return string|null
     */
    public function generate()
    {
        return $this->generator->generate();
    }
}
