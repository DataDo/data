<?php

namespace DataDo\Data\Query;


use DataDo\Data\Exceptions\DslSyntaxException;
use DataDo\Data\MethodNameToken;
use DataDo\Data\NamingConvention;
use DataDo\Data\Tokens\AllToken;
use DataDo\Data\Tokens\AndToken;
use DataDo\Data\Tokens\ByToken;
use DataDo\Data\Tokens\LikeToken;
use DataDo\Data\Tokens\OrToken;
use DataDo\Data\Tokens\ValueToken;
use ReflectionClass;

/**
 * This is the default implementation of QueryBuilder.
 * @package DataDo\Data
 */
class DefaultQueryBuilder extends AbstractQueryBuilder
{

    /** {@inheritdoc} */
    public function build($tokens, $tableName, $namingConvention, $class)
    {
        $resultMode = $this->getMode($tokens);
        $fields = $this->getFields($tokens, $namingConvention, $class);
        $constraints = $this->getConstraints($tokens, $namingConvention, $class);

        switch ($resultMode) {
            case QueryBuilderResult::RESULT_SELECT_SINGLE:
            case QueryBuilderResult::RESULT_SELECT_MULTIPLE:
                $sql = "SELECT $fields FROM $tableName $constraints";
                break;
            case QueryBuilderResult::RESULT_DELETE:
                $sql = "DELETE FROM $tableName $constraints";
                break;
            default:
                throw new DslSyntaxException("Unknown result mode: $resultMode", DATADO_ILLEGAL_RESULT_MODE);
        }

        return new QueryBuilderResult($sql, $resultMode);
    }


    /**
     * Get the result type for a MethodNameToken.
     *
     * @param MethodNameToken $tokens
     * @return int The result type
     * @throws DslSyntaxException if no supported mode was found
     */
    private function getMode($tokens)
    {
        switch ($tokens->getQueryMode()) {
            case 'find':
                return QueryBuilderResult::RESULT_SELECT_MULTIPLE;
            case 'get':
                return QueryBuilderResult::RESULT_SELECT_SINGLE;
            case 'delete':
                return QueryBuilderResult::RESULT_DELETE;
        }

        throw new DslSyntaxException('No query could be built for ' . $tokens->getMethodName() . ': Unknown query mode [' . $tokens->getQueryMode() . ']', DATADO_ILLEGAL_RESULT_MODE);
    }

    /**
     * Build the SQL string for the fields from a MethodNameToken
     * @param MethodNameToken $tokens
     * @param NamingConvention $namingConvention
     * @param ReflectionClass $class
     * @return string
     * @throws DslSyntaxException if an unexpected token is found
     */
    private function getFields($tokens, $namingConvention, $class)
    {
        $fields = [];

        foreach ($tokens->getTokens() as $token) {
            if ($token instanceof ByToken) {
                return $this->fieldsToSQL($fields);
            }

            if ($token instanceof AllToken) {
                return $this->fieldsToSQL([]);
            }

            if ($token instanceof AndToken) {
                // Ignore and tokens as they do nothing
                continue;
            }

            if (!($token instanceof ValueToken)) {
                throw new DslSyntaxException('Unexpected token ' . $token->getName() . ' in field selector', DATADO_UNEXPECTED_TOKEN);
            }

            $fields[] = $this->tokenToColumn($token, $namingConvention, $class);
        }

        return $this->fieldsToSQL($fields);
    }

    /**
     * @param MethodNameToken $tokens
     * @param NamingConvention $namingConvention
     * @param ReflectionClass $class
     * @return string
     * @throws DslSyntaxException if parsing this token failed
     */
    private function getConstraints($tokens, $namingConvention, $class)
    {
        $hasSeenBy = false;
        $expectingValue = true;
        $result = 'WHERE ';
        $lastToken = null;
        foreach ($tokens->getTokens() as $token) {
            // Check if we are processing yet
            if (!$hasSeenBy) {
                if ($token instanceof ByToken) {
                    $hasSeenBy = true;
                    continue;
                } else {
                    continue;
                }
            }

            if ($expectingValue) {
                if (!($token instanceof ValueToken)) {
                    throw new DslSyntaxException('Expected value token in constraint query at ' . $token->getSource(), DATADO_UNEXPECTED_TOKEN);
                }
                $result .= $this->tokenToColumn($token, $namingConvention, $class);
                $expectingValue = false;
            } else {
                if ($token instanceof LikeToken) {
                    $result .= ' LIKE ? ';
                    $lastToken = $token;
                    continue;
                } else if($lastToken instanceof ValueToken) {
                    $result .= ' = ? ';
                }
                $expectingValue = true;
                if ($token instanceof AndToken) {
                    $result .= ' AND ';
                } else if ($token instanceof OrToken) {
                    $result .= ' OR ';
                } else if (!($token instanceof LikeToken)) {
                    throw new DslSyntaxException('Expected And or Or token at ' . $token->getSource(), DATADO_UNEXPECTED_TOKEN);
                }
            }

            $lastToken = $token;


        }
        
        if($lastToken instanceof ValueToken) {
            $result .= ' = ?';
        }

        if (!$hasSeenBy) {
            return '';
        }
        return $result;
    }

    /**
     * Get the column name for a token.
     * @param ValueToken $token
     * @param NamingConvention $namingConvention
     * @param ReflectionClass $class
     * @return string
     * @throws DslSyntaxException when no matching property could be found
     */
    private function tokenToColumn($token, $namingConvention, $class)
    {
        // Find a matching property
        $property = null;
        foreach ($class->getProperties() as $prop) {
            if (strcasecmp($token->getSource(), $prop->getName()) === 0) {
                $property = $prop;
                break;
            }
        }

        if ($property === null) {
            throw new DslSyntaxException('No matching property found for ' . $token->getSource());
        }

        $columnName = $namingConvention->propertyToColumnName($property);
        return $namingConvention->columnName($columnName);
    }

    private function fieldsToSQL($fields)
    {
        if (0 === count($fields)) {
            return '*';
        }

        return implode($fields, ', ');
    }
}