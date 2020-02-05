<?php

namespace WebnetFr\DatabaseAnonymizer;

/**
 * Target table to anonymize.
 * Contains the primary key's name and an array of target fields to anonymize.
 *
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
class TargetTable
{
    /**
     * Name of the table.
     *
     * @var string
     */
    private $name;

    /**
     * Primary key field names.
     *
     * @var array
     */
    private $primaryKey;

    /**
     * Array of target fields to anonymize.
     *
     * @var TargetField[]
     */
    private $targetFields;

    /**
     * Truncate a table.
     *
     * @var bool
     */
    private $truncate;

    /**
     * @param string $name
     * @param array $primaryKey
     * @param TargetField[] $targetFields
     * @param bool $truncate
     */
    public function __construct(string $name, array $primaryKey, array $targetFields, bool $truncate)
    {
        if ($truncate && $targetFields) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid configuration of %s. Table can be either anonymized or truncated.', TargetTable::class
            ));
        }

        $this->name = $name;
        $this->primaryKey = $primaryKey;
        $this->targetFields = $targetFields;
        $this->truncate = $truncate;
    }

    /**
     * Get the name of this table.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the name of the primary key.
     *
     * @return array
     */
    public function getPrimaryKey(): array
    {
        return $this->primaryKey;
    }

    /**
     * Get the list target fields to anonymize.
     *
     * @return TargetField[]
     */
    public function getTargetFields(): array
    {
        return $this->targetFields;
    }

    /**
     * Get the names of all known fields.
     *
     * @return string[]
     */
    public function getAllFieldNames(): array
    {
        $fields = $this->primaryKey;

        foreach ($this->targetFields as $targetField) {
            $fields[] = $targetField->getName();
        }

        return $fields;
    }

    /**
     * Table must be truncated.
     *
     * @return bool
     */
    public function isTruncate(): bool
    {
        return $this->truncate;
    }
}
