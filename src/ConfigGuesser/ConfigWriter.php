<?php

namespace WebnetFr\DatabaseAnonymizer\ConfigGuesser;

use Symfony\Component\Yaml\Yaml;

/**
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
class ConfigWriter
{
    /**
     * @var array $hints
     *
     * @return string
     */
    public function write(array $hints)
    {
        $config = [];

        foreach ($hints as $tableName => $tableHints) {
            foreach ($tableHints as $columnName => $hint) {
                /** @var $hint ConfigGuesserHint */

                $c = [
                    'generator' => 'faker',
                    'formatter' => $hint->formatter,
                ];

                if (!is_null($hint->arguments)) {
                    $c['arguments'] = $hint->arguments;
                }

                if (!is_null($hint->locale)) {
                    $c['locale'] = $hint->locale;
                }

                if (!is_null($hint->unique)) {
                    $c['unique'] = $hint->unique;
                }

                if (!is_null($hint->date)) {
                    $c['date_format'] = 'Y-m-d H:i:s';
                }

                $config[$tableName]['fields'][$columnName] = $c;
            }
        }

        $config = [
            'webnet_fr_database_anonymizer' => [
                'tables' => $config,
            ],
        ];

        return Yaml::dump($config, 6);
    }
}
