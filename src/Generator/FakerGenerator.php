<?php

namespace WebnetFr\DatabaseAnonymizer\Generator;

use Faker\DefaultGenerator;
use Faker\Generator;

/**
 * Fake random value using Faker.
 *
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
class FakerGenerator implements GeneratorInterface
{
    /**
     * @var Generator|DefaultGenerator
     */
    private $generator;

    /**
     * @var string
     */
    private $formatter;

    /**
     * @var array
     */
    private $arguments;

    /**
     * @var array
     */
    private $config;

    /**
     * @param Generator|DefaultGenerator $generator
     * @param string $formatter
     * @param array $arguments
     * @param array $config
     */
    public function __construct($generator, string $formatter, array $arguments = [], array $config = [])
    {
        $this->generator = $generator;
        $this->formatter = $formatter;
        $this->arguments = $arguments;
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function generate()
    {
        $value = $this->generator->__call($this->formatter, $this->arguments);
        if ($value instanceof \DateTime) {
            $format = $this->config['date_format'] ?? null;

            return $value->format($format);
        }

        return $value;
    }
}
