<?php
/**
 * //TODO PHPDoc
 * @author Thomas Biesaart
 */

namespace DataDo\Data;


use DataDo\Data\Tokens\Token;

class MethodNameToken implements \JsonSerializable
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
     * @return string
     */
    public function getMethodName()
    {
        return $this->methodName;
    }

    /**
     * @return string
     */
    public function getQueryMode()
    {
        return $this->queryMode;
    }

    /**
     * @return Token[]
     */
    public function getTokens()
    {
        return $this->tokens;
    }


    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     */
    public function jsonSerialize()
    {
        return [
            'source' => $this->methodName,
            'mode' => $this->queryMode,
            'tokens' => $this->tokens
        ];
    }
}