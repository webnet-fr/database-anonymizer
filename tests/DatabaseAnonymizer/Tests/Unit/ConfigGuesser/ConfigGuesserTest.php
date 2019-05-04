<?php

namespace DatabaseAnonymizer\Tests\Unit\ConfigGuesser;

use PHPUnit\Framework\TestCase;
use WebnetFr\DatabaseAnonymizer\ConfigGuesser\ConfigGuesser;
use WebnetFr\DatabaseAnonymizer\ConfigGuesser\ConfigGuesserHint;

/**
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
class ConfigGuesserTest extends TestCase
{
    const TO_SNAKE_CASE_TESTS = [
        'snake_case'          => 'snake_case',
        'camelCase'           => 'camel_case',
        'lowercase'           => 'lowercase',
        'UPPERCASE'           => 'uppercase',
        'StartsWithCapital'   => 'starts_with_capital',
        'abbrLAST'            => 'abbr_last',
        'ABBRFirst'           => 'abbr_first',
        'abbrINTHEMiddle'     => 'abbr_inthe_middle',
        'SCREAMING_CASE_HERE' => 'screaming_case_here',
    ];

    public function testToSnakeCase()
    {
        $guesser = new ConfigGuesser();

        foreach (self::TO_SNAKE_CASE_TESTS as $test => $expected) {
            $this->assertEquals($expected, $this->callToSnakeCase($test));
        }
    }

    public function testGuessZipCode()
    {
        $hint = $this->callGuessColumn('zip_code');
        $this->assertEquals('postcode', $hint->formatter);

        $hint = $this->callGuessColumn('postCode');
        $this->assertEquals('postcode', $hint->formatter);
    }

    public function testGuessCodePostal()
    {
        $hint = $this->callGuessColumn('code_postal');
        $this->assertEquals('postcode', $hint->formatter);
        $this->assertEquals('fr_FR', $hint->locale);

        $hint = $this->callGuessColumn('CP');
        $this->assertEquals('postcode', $hint->formatter);
        $this->assertEquals('fr_FR', $hint->locale);
    }

    private function callToSnakeCase(string $str)
    {
        return $this->callGuesserPrivateMethod('toSnakeCase', $str);
    }

    /**
     * @param string $name
     *
     * @return ConfigGuesserHint
     */
    private function callGuessColumn(string $name)
    {
        return $this->callGuesserPrivateMethod('guessColumn', $name);
    }

    /**
     * @param string $method
     * @param mixed $arg
     *
     * @return mixed
     */
    private function callGuesserPrivateMethod(string $method, $arg)
    {
        $guesser = new ConfigGuesser();

        $reflConfigGuesser = new \ReflectionObject($guesser);
        $reflMethod = $reflConfigGuesser->getMethod($method);
        $reflMethod->setAccessible(true);

        return $reflMethod->invoke($guesser, $arg);
    }
}
