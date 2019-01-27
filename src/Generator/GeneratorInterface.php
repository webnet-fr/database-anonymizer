<?php

namespace WebnetFr\DatabaseAnonymizer\Generator;

/**
 * Common interface that all generators must implement.
 * Each generator operates in scope of the @see \WebnetFr\DatabaseAnonymizer\TargetField.
 *
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
interface GeneratorInterface
{
    /**
     * Generate new random value.
     *
     * @return string|null
     */
    public function generate();
}
