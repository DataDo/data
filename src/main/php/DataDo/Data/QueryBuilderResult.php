<?php
/**
 * //TODO PHPDoc
 * @author Thomas Biesaart
 */

namespace DataDo\Data;


class QueryBuilderResult
{
    public $sql;
    public $resultMode;

    public function __construct($sql, $resultMode)
    {
        $this->sql = $sql;
        $this->resultMode = $resultMode;
    }

    const RESULT_SINGLE = 0;
    const RESULT_MULTIPLE = 1;
}