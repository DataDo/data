<?php
/**
 * //TODO PHPDoc
 * @author Thomas Biesaart
 */

namespace DataDo\Data;


use DataDo\Data\Tokens\AndToken;
use DataDo\Data\Tokens\ByToken;
use DataDo\Data\Tokens\OrToken;
use DataDo\Data\Tokens\ValueToken;

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
        $result = array();
        $lastToken = null;
        foreach ($rawTokens[0] as $token) {
            $newToken = $this->getToken($token);

            if ($newToken instanceof ValueToken) {
                if ($lastToken != null && $lastToken instanceof ValueToken) {
                    // Append this
                    $lastToken = new ValueToken($lastToken->getSource() . $newToken->getSource());
                } else {
                    // This is the start of a new token
                    $lastToken = $newToken;
                }
            } else {
                if ($lastToken instanceof ValueToken) {
                    // Add the last token too
                    $result[] = $lastToken;
                    $lastToken = null;
                }
                $result[] = $newToken;
            }
        }

        if($lastToken !== null) {
            $result[] = $lastToken;
        }


        return $result;
    }


    private function getToken($token)
    {
        if ($token === 'And') {
            return new AndToken();
        }

        if ($token === 'Or') {
            return new OrToken();
        }

        if ($token === 'By') {
            return new ByToken();
        }

        return new ValueToken($token);
    }
}