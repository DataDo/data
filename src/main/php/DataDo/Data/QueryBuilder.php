<?php
/**
 * //TODO PHPDoc
 * @author Thomas Biesaart
 */

namespace DataDo\Data;


interface QueryBuilder
{
    public function build($tokens, $tableName);
}