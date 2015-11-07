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
        $tableName = $this->removeCamelCasing($shortName);
        return $tableName;
    }

    /** {$@inheritdoc} */
    public function propertyToColumnName($property)
    {
        $name = $property->getName();
        $columnName = $this->removeCamelCasing($name);
        return $columnName;
    }

    private function removeCamelCasing($input)
    {
        $output = lcfirst($input);
        $output = preg_replace('([A-Z])', '_$0', $output);
        $output = strtolower($output);
        return $output;
    }
}