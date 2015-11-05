<?php

namespace DataDo\Data\Query;


use DataDo\Data\Exceptions\DslSyntaxException;
use DataDo\Data\MethodNameToken;
use DataDo\Data\NamingConvention;
use ReflectionClass;

interface QueryBuilder
{
    /**
     * Build a QueryBuilderResult from a MethodNameToken.
     *
     * @param MethodNameToken $tokens
     * @param string $tableName
     * @param NamingConvention $namingConvention the naming convention
     * @param ReflectionClass $class the class to build this query for
     * @return QueryBuilderResult
     * @throws DslSyntaxException if the token could not be parsed
     */
    public function build($tokens, $tableName, $namingConvention, $class);
}