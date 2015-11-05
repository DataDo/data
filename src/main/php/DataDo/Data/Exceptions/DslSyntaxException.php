<?php
/**
 * //TODO PHPDoc
 * @author Thomas Biesaart
 */

namespace DataDo\Data\Exceptions;
use Exception;

/**
 * This exception generally occurs when there is a syntax error in a dsl method.
 * @package DataDo\Data\Exceptions
 */
class DslSyntaxException extends DataDoException
{
    public function __construct($message, $code = DATADO_SYNTAX_ERROR, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

define('DATADO_ILLEGAL_RESULT_MODE', DATADO_SYNTAX_ERROR + 100);
define('DATADO_UNEXPECTED_TOKEN', DATADO_SYNTAX_ERROR + 200);