<?php
/**
 * //TODO PHPDoc
 * @author Thomas Biesaart
 */

namespace DataDo\Data;


interface MethodNameParser
{
    /**
     * Parse a dsl method name into a {@link MethodNameToken}.
     * @param $methodName string the dsl method name
     * @return MethodNameToken the token
     */
    public function parse($methodName);
}