<?php

namespace WebnetFr\DatabaseAnonymizer\Event;

use Symfony\Contracts\EventDispatcher\Event;

class AnonymizerEvent extends Event
{
    public $tableName;
    public $values;

    public function __construct(string $tableName, array $values = [])
    {
        $this->tableName = $tableName;
        $this->values = $values;
    }
}
