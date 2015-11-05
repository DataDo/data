<?php
/**
 * //TODO PHPDoc
 * @author Thomas Biesaart
 */

namespace DataDo\Data;

/**
 * This is the default implementation of the NamingConvention
 * @package DataDo\Data
 */
class DefaultNamingConvention implements NamingConvention
{

    /** {$@inheritdoc} */
    public function classToTableName($class)
    {
        $shortName = $class->getShortName();
        return $shortName;
    }

    /** {$@inheritdoc} */
    public function tableName($tableName)
    {
        return $tableName;
    }

    /** {$@inheritdoc} */
    public function propertyToColumnName($property)
    {
        $name = $property->getName();
        return $name;
    }

    /** {$@inheritdoc} */
    public function columnName($columnName)
    {
        return $columnName;
    }
}