<?php

namespace DataDo\Data\Parser;


use DataDo\Data\MethodNameToken;
use DataDo\Data\Tokens\AllToken;
use DataDo\Data\Tokens\AndToken;
use DataDo\Data\Tokens\ByToken;
use DataDo\Data\Tokens\DistinctToken;
use DataDo\Data\Tokens\LikeToken;
use DataDo\Data\Tokens\OrToken;
use DataDo\Data\Tokens\Token;
use DataDo\Data\Tokens\ValueToken;

/**
 * This is the default implementation of MethodNameParser.
 * @package DataDo\Data
 */
class DefaultMethodNameParser extends AbstractMethodNameParser
{
    /**
     * {@inheritdoc}
     */
    public function parse($methodName)
    {
        return new MethodNameToken(
            $methodName,
            $this->getQueryMode($methodName),
            $this->getTokens($methodName)
        );
    }

    /**
     * Get the first word of the method as the query mode.
     * @param $methodName string the name of the method
     * @return string the query mode token
     */
    private function getQueryMode($methodName)
    {
        $index = strcspn($methodName, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ');
        return substr($methodName, 0, $index);
    }

    /**
     * Split the method name into tokens and parse them.
     * @param $methodName string the method name
     * @return Token[]
     */
    private function getTokens($methodName)
    {
        preg_match_all('([A-Z_-][^A-Z_-]*)', $methodName, $rawTokens);
        $result = array();
        $lastToken = null;
        $seenBy = false;
        foreach ($rawTokens[0] as $token) {
            $newToken = $this->getToken($token, $seenBy);

            if ($newToken instanceof ValueToken) {
                $lastToken = $lastToken !== null && $lastToken instanceof ValueToken ?
                    new ValueToken($lastToken->getSource() . $newToken->getSource()) :
                    $newToken;
            } else {
                if ($lastToken instanceof ValueToken) {
                    // Add the last token too
                    $result[] = $lastToken;
                    $lastToken = null;
                }
                $result[] = $newToken;
            }
        }

        if ($lastToken !== null) {
            $result[] = $lastToken;
        }


        return $result;
    }


    /**
     * Translate a token source to a token object.
     * @param $token string the token source
     * @return Token the token object
     */
    private function getToken($token, &$hasSeenBy)
    {
        switch ($token) {
            case 'And':
                return new AndToken();
            case 'Or':
                return new OrToken();
            case 'By':
            case 'Where':
                $hasSeenBy = true;
                return new ByToken();
            case 'All':
                return new AllToken();
            case 'Distinct':
                if ($hasSeenBy) {
                    return new ValueToken($token);
                } else {
                    return new DistinctToken();
                }
            case 'Like':
                if ($hasSeenBy) {
                    return new LikeToken();
                } else {
                    return new ValueToken($token);
                }
            default:
                return new ValueToken($token);
        }
    }
}