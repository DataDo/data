<?php
/**
 * //TODO PHPDoc
 * @author Thomas Biesaart
 */

namespace DataDo\Data;


use DataDo\Data\Exceptions\DslSyntaxException;

interface QueryBuilder
{
    /**
     * Build a QueryBuilderResult from a MethodNameToken.
     *
     * @param MethodNameToken $tokens
     * @param string $tableName
     * @return QueryBuilderResult
     * @throws DslSyntaxException if the token could not be parsed
     */
    public function build($tokens, $tableName);
}