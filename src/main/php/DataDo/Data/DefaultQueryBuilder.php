<?php
/**
 * //TODO PHPDoc
 * @author Thomas Biesaart
 */

namespace DataDo\Data;


use DataDo\Data\Exceptions\DslSyntaxException;
use DataDo\Data\Tokens\AllToken;
use DataDo\Data\Tokens\AndToken;
use DataDo\Data\Tokens\ByToken;
use DataDo\Data\Tokens\OrToken;
use DataDo\Data\Tokens\ValueToken;

class DefaultQueryBuilder implements QueryBuilder
{
    /** {@inheritdoc} */
    public function build($tokens, $tableName)
    {
        $resultMode = $this->getMode($tokens);
        $fields = $this->getFields($tokens);
        $constraints = $this->getConstraints($tokens);

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
     * @return string
     * @throws DslSyntaxException if an unexpected token is found
     */
    private function getFields($tokens)
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
            $fields[] = $token->getSource();
        }

        return $this->fieldsToSQL($fields);
    }

    /**
     * @param MethodNameToken $tokens
     * @return string
     * @throws DslSyntaxException if parsing this token failed
     */
    private function getConstraints($tokens)
    {
        $hasSeenBy = false;
        $expectingValue = true;
        $result = 'WHERE ';
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
                $result .= $token->getSource() . ' = ?';
                $expectingValue = false;
            } else {
                $expectingValue = true;
                if ($token instanceof AndToken) {
                    $result .= ' AND ';
                } else if ($token instanceof OrToken) {
                    $result .= ' OR ';
                } else {
                    throw new DslSyntaxException('Expected And or Or token at ' . $token->getSource(), DATADO_UNEXPECTED_TOKEN);
                }
            }


        }
        if (!$hasSeenBy) {
            return '';
        }
        return $result;
    }

    private function fieldsToSQL($fields)
    {
        if (0 === count($fields)) {
            return '*';
        }

        return implode($fields, ', ');
    }
}