<?php
/**
 * //TODO PHPDoc
 * @author Thomas Biesaart
 */

namespace DataDo\Data;


class QueryBuilderResult
{
    /** @var  string */
    private $sql;
    /** @var  integer */
    private $resultMode;

    public function __construct($sql, $resultMode)
    {
        $this->sql = $sql;
        $this->resultMode = $resultMode;
    }

    const RESULT_SELECT_SINGLE = 0;
    const RESULT_SELECT_MULTIPLE = 1;
    const RESULT_DELETE = 2;

    /**
     * Get the SQL string from this result.
     * @return string
     */
    public function getSql()
    {
        return $this->sql;
    }

    /**
     * Get the result mode.
     * @return int
     */
    public function getResultMode()
    {
        return $this->resultMode;
    }
}