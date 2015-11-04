<?php
/**
 * //TODO PHPDoc
 * @author Thomas Biesaart
 */

namespace DataDo\Data\Tokens;


class OrToken extends ConstantToken
{
    public function __construct() {
        parent::__construct('Or');
    }
}