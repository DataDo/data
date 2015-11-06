<?php
/**
 * //TODO PHPDoc
 * @author Thomas Biesaart
 */

namespace DataDo\Data\Exceptions;


use Exception;

class ConfigurationException extends DataDoException
{
    public function __construct($message, $code = DATADO_CONFIG_ERROR, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}