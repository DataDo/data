<?php
/**
 * //TODO PHPDoc
 * @author Thomas Biesaart
 */

namespace DataDo\Data;


class DefaultMethodNameParser implements MethodNameParser
{

    public function parse($methodName)
    {
        $result = new MethodNameToken(
            $methodName,
            $this->getQueryMode($methodName),
            $this->getTokens($methodName)
        );

        return $result;
    }

    private function getQueryMode($methodName)
    {
        $index = strcspn($methodName, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ');
        return substr($methodName, 0, $index);
    }

    private function getTokens($methodName)
    {
        preg_match_all('([A-Z][a-z]+)', $methodName, $rawTokens);
        return $rawTokens[0];
    }
}