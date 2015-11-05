<?php

namespace DataDo\Data\Tokens;


class OrToken extends ConstantToken
{
    public function __construct() {
        parent::__construct('Or');
    }
}