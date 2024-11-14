<?php

namespace WebnetFr\DatabaseAnonymizer\ConfigGuesser;

use Symfony\Component\Yaml\Yaml;

/**
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
class ConfigWriter
{
    /**
     * @return string
     */
    public function write(array $hints)
    {
        $config = [];

        foreach ($hints as $tableName => $tableHints) {
            foreach ($tableHints as $columnName => $hint) {
                /** @var ConfigGuesserHint $hint */
                $config[$tableName]['fields'][$columnName] = $hint->getConfigArray();
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
