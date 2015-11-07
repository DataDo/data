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
        $tableName = strtolower($shortName);
        return $tableName;
    }

    /** {$@inheritdoc} */
    public function propertyToColumnName($property)
    {
        $name = $property->getName();
        $columnName = preg_replace('([A-Z])', '_$0', $name);
        $columnName = strtolower($columnName);
        return $columnName;
    }
}