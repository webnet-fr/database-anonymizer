<?php

namespace WebnetFr\DatabaseAnonymizer\Tests\Unit\Generator;

use Faker\Generator;
use PHPUnit\Framework\TestCase;
use Faker\Provider\DateTime as FakerProviderDateTime;
use WebnetFr\DatabaseAnonymizer\Generator\DateTime as DateTimeGenerator;

/**
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
class DateTimeTest extends TestCase
{
    public function testDate()
    {
        $dateTimeProviderMock = new DateTimeProviderMock();
        $value = (new DateTimeGenerator($dateTimeProviderMock, ['format' => 'Y-m-d H:i:s']))->generate();
        $this->assertTrue(is_string($value));
        $this->assertRegExp('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $value);
    }
}

class DateTimeProviderMock extends FakerProviderDateTime
{
    public function __construct()
    {
        parent::__construct(new Generator());
    }

    public static function dateTimeBetween($startDate = '-30 years', $endDate = 'now', $timezone = null)
    {
        return new \DateTime();
    }
}
