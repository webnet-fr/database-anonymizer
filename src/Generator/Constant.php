<?php

namespace WebnetFr\DatabaseAnonymizer\Generator;

use WebnetFr\DatabaseAnonymizer\Exception\InvalidConstantException;

/**
 * Generator that always return constant.
 *
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
class Constant implements GeneratorInterface
{
    /**
     * Arbitrary constant value.
     *
     * @var mixed
     */
    private $constant;

    /**
     * @param $constant
     */
    public function __construct($constant)
    {
        if (!is_null($constant) && !is_string($constant)) {
            throw new InvalidConstantException("Constant value must be null or string");
        }

        $this->constant = $constant;
    }

    /**
     * @inheritdoc
     */
    public function generate()
    {
        return $this->constant;
    }
}
