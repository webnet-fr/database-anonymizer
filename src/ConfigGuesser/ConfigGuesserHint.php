<?php

namespace WebnetFr\DatabaseAnonymizer\ConfigGuesser;

/**
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
class ConfigGuesserHint
{
    /**
     * @var string
     */
    public $formatter;

    /**
     * @var string[]
     */
    public $words;

    /**
     * @var array
     */
    public $arguments;

    /**
     * @var bool
     */
    public $date;

    /**
     * @var bool
     */
    public $unique;

    /**
     * @var string
     */
    public $locale;

    /**
     * @param string $formatter
     */
    public function __construct(string $formatter)
    {
        $this->formatter = $formatter;
    }

    /**
     * Set words.
     *
     * @param string[] $words
     *
     * @return $this
     */
    public function words(array $words)
    {
        $this->words = $words;

        return $this;
    }

    /**
     * Set arguments.
     *
     * @param array $arguments
     *
     * @return $this
     */
    public function arguments(array $arguments)
    {
        $this->arguments = $arguments;

        return $this;
    }

    /**
     * Set date.
     *
     * @param bool $date
     *
     * @return $this
     */
    public function date(bool $date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Set unique.
     *
     * @param bool $unique
     *
     * @return $this
     */
    public function unique(bool $unique)
    {
        $this->unique = $unique;

        return $this;
    }

    /**
     * Set locale.
     *
     * @param string $locale
     *
     * @return $this
     */
    public function locale(string $locale)
    {
        $this->locale = $locale;

        return $this;
    }
}
