<?php

namespace WebnetFr\DatabaseAnonymizer\ConfigGuesser;

use Doctrine\DBAL\Connection;
use WebnetFr\DatabaseAnonymizer\Exception\GuesserMissingHintException;

/**
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
class ConfigGuesser
{
    /**
     * @var ConfigGuesserHint[]
     */
    private static $hints;

    public function __construct()
    {
        self::$hints = [
            (new ConfigGuesserHint('firstName'))->words([['first', 'name'], 'firstname'])->arguments([null]),
            (new ConfigGuesserHint('firstName'))->words(['prenom'])->locale('fr_FR')->arguments([null]),
            (new ConfigGuesserHint('lastName'))->words([['last', 'name'], 'lastname']),
            (new ConfigGuesserHint('lastName'))->words(['nom'])->locale('fr_FR'),
            (new ConfigGuesserHint('city'))->words(['city', 'town', 'ville']),
            (new ConfigGuesserHint('streetAddress'))->words(['address', 'adresse']),
            (new ConfigGuesserHint('postcode'))->words([['post', 'code'], 'zip']),
            (new ConfigGuesserHint('postcode'))->words([['code', 'postal'], 'cp'])->locale('fr_FR'),
            (new ConfigGuesserHint('country'))->words(['country', 'pays']),
            (new ConfigGuesserHint('phoneNumber'))->words(['phone']),
            (new ConfigGuesserHint('realText'))->words(['comment'])->arguments([200, 2]),
            (new ConfigGuesserHint('realText'))->words(['commentaire'])->arguments([200, 2])->locale('fr_FR'),
            (new ConfigGuesserHint('dateTimeBetween'))->words(['birthdate', 'birthday'])->arguments(['-30 years', 'now', null])->date(true),
            (new ConfigGuesserHint('safeEmail'))->words(['email', 'mail']),
            (new ConfigGuesserHint('userName'))->words([['user', 'name'], 'username'])->unique(true),
            (new ConfigGuesserHint('password'))->words(['password']),
            (new ConfigGuesserHint('creditCardNumber'))->words([['credit', 'card'], ['credit', 'carte'], 'cb']),
            (new ConfigGuesserHint('siren'))->words(['siren'])->locale('fr_FR')->unique(true),
            (new ConfigGuesserHint('siret'))->words(['siret'])->locale('fr_FR')->unique(true),
            (new ConfigGuesserHint('vat'))->words(['vat'])->locale('fr_FR')->unique(true),
            (new ConfigGuesserHint('nir'))->words(['nir', ['securite', 'sociale']])->locale('fr_FR')->unique(true),
        ];
    }

    /**
     * @param Connection $connection
     *
     * @return array
     */
    public static function guess(Connection $connection)
    {
        $hints = [];
        $sm = $connection->createSchemaManager();

        foreach ($sm->listTables() as $table) {
            $tableName = $table->getName();

            foreach ($table->getColumns() as $column) {
                $columnName = $column->getName();

                try {
                    $hints[$tableName][$columnName] = self::guessColumn($columnName);
                } catch (GuesserMissingHintException $e) {
                    // Column cannot be guessed and it does not seem to be
                    // personal information. Skip it.
                }
            }
        }
        ksort($hints);
        return $hints;
    }

    /**
     * @param string $name
     *
     * @throws GuesserMissingHintException
     *
     * @return ConfigGuesserHint
     */
    public static function guessColumn(string $name)
    {
        $columnWords = self::toWords($name);

        foreach (self::$hints as $hint) {
            foreach ($hint->words as $word) {
                if (\is_string($word) && \in_array($word, $columnWords, true)) {
                    return $hint;
                }

                if (\is_array($word) && \count(array_intersect($word, $columnWords)) == \count($word)) {
                    return $hint;
                }
            }
        }

        throw new GuesserMissingHintException();
    }

    /**
     * @param string $str
     *
     * @return array
     *
     * @author Vlad Riabchenko <vriabchenko@webnet.fr>
     */
    private static function toWords(string $str)
    {
        $snake = self::toSnakeCase(preg_replace('/\d/', '', $str));

        return explode('_', $snake);
    }

    /**
     * @param string $str
     *
     * @return string
     */
    private static function toSnakeCase(string $str)
    {
        $pieces = preg_split('/((?<=.)(?=[[:upper:]][[:lower:]])|(?<=[[:lower:]])(?=[[:upper:]]))/', $str);

        return strtolower(implode('_', $pieces));
    }
}
