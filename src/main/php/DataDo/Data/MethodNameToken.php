<?php
/**
 * //TODO PHPDoc
 * @author Thomas Biesaart
 */

namespace DataDo\Data;


class MethodNameToken
{
    private $methodName;
    private $queryMode;
    private $tokens;

    /**
     * MethodNameToken constructor.
     * @param $methodName
     * @param $queryMode
     * @param $tokens
     */
    public function __construct($methodName, $queryMode, $tokens)
    {
        $this->methodName = $methodName;
        $this->queryMode = $queryMode;
        $this->tokens = $tokens;
    }


    /**
     * @return mixed
     */
    public function getMethodName()
    {
        return $this->methodName;
    }

    /**
     * @return mixed
     */
    public function getQueryMode()
    {
        return $this->queryMode;
    }

    /**
     * @return mixed
     */
    public function getTokens()
    {
        return $this->tokens;
    }


}