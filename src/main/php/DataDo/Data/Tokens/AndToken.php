<?php
/**
 * //TODO PHPDoc
 * @author Thomas Biesaart
 */

namespace DataDo\Data\Tokens;


class AndToken extends ConstantToken
{
    public function __construct() {
        parent::__construct('And');
    }
}