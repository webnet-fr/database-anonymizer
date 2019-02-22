<?php

namespace WebnetFr\DatabaseAnonymizer\Generator;

use Faker\Provider\DateTime as FakerProviderDateTime;

/**
 * Random datetime.
 *
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
class DateTime implements GeneratorInterface
{
    /**
     * @var FakerProviderDateTime
     */
    private $provider;

    /**
     * Generated datetime format acceptable by @see date().
     *
     * @var string
     */
    private $format;

    /**
     * In seconds relative to the beginning of unix epoch.
     * Defaults to -30 years.
     *
     * @var int
     */
    private $start;

    /**
     * In seconds relative to the beginning of unix epoch.
     * Defaults to current time.
     *
     * @var int
     */
    private $end;

    /**
     * @var string
     */
    private $timezone = null;

    /**
     * @param FakerProviderDateTime $provider
     * @param array $config
     */
    public function __construct(FakerProviderDateTime $provider, array $config)
    {
        $this->provider = $provider;

        $this->format = $config['format'] ?? false;
        if (!$this->format) {
            throw new \InvalidArgumentException("You must define 'format' for date generator.");
        }

        $this->start = $config['start'] ?? '-30 years';
        $this->end = $config['end'] ?? 'now';
        $this->timezone = $config['timezone'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function generate()
    {
        return $this->provider->dateTimeBetween($this->start, $this->end, $this->timezone)->format($this->format);
    }
}
