<?php
/**
 * //TODO PHPDoc
 * @author Thomas Biesaart
 */

namespace DataDo\Data;
use ReflectionClass;
use ReflectionProperty;

/**
 * This interface represents an object that can translate entity names into tables and columns.
 * @package DataDo\Data
 */
interface NamingConvention
{
    /**
     * Transform a class to a table name.
     * @param $class ReflectionClass the class to build a table name for
     * @return string the table name
     */
    public function classToTableName($class);

    /**
     * Clean up a table name as a final step.
     * @param $tableName string the raw table name
     * @return string the physical table name
     */
    public function tableName($tableName);

    /**
     * Transform a property to a column name.
     * @param $property ReflectionProperty the property to build a table column for
     * @return string the column name
     */
    public function propertyToColumnName($property);

    /**
     * Clean up a column name as a final step.
     * @param $columnName string the raw column name
     * @return string the physical column name
     */
    public function columnName($columnName);
}