<?php

namespace WebnetFr\DatabaseAnonymizer\ConfigGuesser;

use Symfony\Component\Yaml\Yaml;

/**
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
class ConfigWriter
{
    /**
     * @var array
     *
     * @return string
     */
    public function write(array $hints)
    {
        $config = [];

        foreach ($hints as $tableName => $tableHints) {
            foreach ($tableHints as $columnName => $hint) {
                /** @var ConfigGuesserHint $hint */
                $c = [
                    'generator' => 'faker',
                    'formatter' => $hint->formatter,
                ];

                if (null !== $hint->arguments) {
                    $c['arguments'] = $hint->arguments;
                }

                if (null !== $hint->locale) {
                    $c['locale'] = $hint->locale;
                }

                if (null !== $hint->unique) {
                    $c['unique'] = $hint->unique;
                }

                if (null !== $hint->date) {
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
