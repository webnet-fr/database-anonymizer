<?php

namespace WebnetFr\DatabaseAnonymizer\Tests\Unit\Generator;

use PHPUnit\Framework\TestCase;
use WebnetFr\DatabaseAnonymizer\Generator\DateTime as DateTimeGenerator;

/**
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
class DateTimeTest extends TestCase
{
    public function testDate()
    {
        $value = (new DateTimeGenerator('Y-m-d H:i:s'))->generate();
        $this->assertTrue(is_string($value));
        $this->assertRegExp('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $value);
        $this->assertLessThanOrEqual(new \DateTime(), new \DateTime($value));
    }

    public function testDateWithMin()
    {
        $yesterday = (new \DateTime('yesterday'))->setTime(0, 0, 0);

        $generator = (new DateTimeGenerator('Y-m-d H:i'))
            ->setMin($yesterday);

        $value = $generator->generate();

        $this->assertRegExp('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}/', $value);
        $this->assertGreaterThanOrEqual($yesterday, new \DateTime($value));
        $this->assertLessThanOrEqual(new \DateTime(), new \DateTime($value));
    }

    public function testDateWithMinMax()
    {
        $tomorrow = (new \DateTime('tomorrow'))->setTime(23, 59, 59);
        $inFiveMinutes = new \DateTime('+5 minutes');

        $generator = (new DateTimeGenerator('Y-m-d H:i:s'))
            ->setMin($inFiveMinutes)
            ->setMax($tomorrow);

        $value = $generator->generate();

        $this->assertRegExp('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $value);
        $this->assertGreaterThanOrEqual($inFiveMinutes, new \DateTime($value));
        $this->assertLessThanOrEqual($tomorrow, new \DateTime($value));
    }
}
