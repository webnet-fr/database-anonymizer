<?php

namespace WebnetFr\DatabaseAnonymizer\Generator;

/**
 * Random datetime.
 *
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
class DateTime implements GeneratorInterface
{
    /**
     * Generated datetime format acceptable by @see date().
     *
     * @var string
     */
    private $format;

    /**
     * In seconds relative to the beginning of unix epoch.
     * Defaults to January 1 1970 00:00:00
     *
     * @var int
     */
    private $min;

    /**
     * In seconds relative to the beginning of unix epoch.
     * Defaults to current time.
     *
     * @var int
     */
    private $max;

    /**
     * @param string $format
     */
    public function __construct(string $format)
    {
        $this->format = $format;
        $this->max = 0;
        $this->max = time();
    }

    /**
     * Set min
     *
     * @param int|\DateTime|string $min
     *
     * @return $this
     */
    public function setMin($min)
    {
        if (is_string($min)) {
            $min = strtotime($min);
        } elseif ($min instanceof \DateTime) {
            $min = $min->getTimestamp();
        } else {
            throw new \InvalidArgumentException('You must pass integer, parceable string or DateTime object to setMin');
        }

        $this->min = $min;

        return $this;
    }

    /**
     * Set max.
     *
     * @param int|\DateTime|string $max
     *
     * @return $this
     */
    public function setMax($max)
    {
        if (is_string($max)) {
            $max = strtotime($max);
        } elseif ($max instanceof \DateTime) {
            $max = $max->getTimestamp();
        } else {
            throw new \InvalidArgumentException('You must pass integer, parceable string or DateTime object to setMax');
        }

        $this->max = $max;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function generate()
    {
        return date($this->format, mt_rand($this->min, $this->max));
    }
}
