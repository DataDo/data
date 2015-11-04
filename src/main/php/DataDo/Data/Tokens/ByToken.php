<?php
/**
 * //TODO PHPDoc
 * @author Thomas Biesaart
 */

namespace DataDo\Data\Tokens;


class ByToken extends ConstantToken
{
    public function __construct() {
        parent::__construct('By');
    }
}