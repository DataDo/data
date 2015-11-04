<?php
/**
 * //TODO PHPDoc
 * @author Thomas Biesaart
 */

namespace DataDo\Data;


class DefaultQueryBuilder implements QueryBuilder
{

    public function build($methodQueryResult, $tableName)
    {
        $resultMode = $this->getMode($methodQueryResult);
        $fields = $this->getFields($methodQueryResult);
        $constraints = $this->getConstraints($methodQueryResult);

        $sql = "SELECT $fields FROM $tableName $constraints";

        return new QueryBuilderResult($sql, $resultMode);
    }


    private function getMode($methodQueryResult)
    {
        if ($methodQueryResult->getQueryMode() === 'find') {
            return QueryBuilderResult::RESULT_MULTIPLE;
        }

        if ($methodQueryResult->getQueryMode() === 'get') {
            return QueryBuilderResult::RESULT_SINGLE;
        }

        throw new \InvalidArgumentException("No query could be built for $methodQueryResult->methodName");
    }

    private function getFields($methodQueryResult)
    {
        return '*';
    }

    private function getConstraints($methodQueryResult)
    {
        return '';
    }

    private function parseMethodName($methodName)
    {
        // Get query mode string

    }
}