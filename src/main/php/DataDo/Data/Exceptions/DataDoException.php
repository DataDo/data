<?php

namespace DataDo\Data\Exceptions;
use Exception;

/**
 * This Exception represents the base of all exceptions in this library.
 * @package DataDo\Data\Exceptions
 */
abstract class DataDoException extends Exception
{
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

define('DATADO_ERROR', 60000);
define('DATADO_SYNTAX_ERROR', DATADO_ERROR + 7000);
define('DATADO_CONFIG_ERROR', DATADO_ERROR + 8000);