<?php

namespace DataDo\Data\Tokens;


class AndToken extends ConstantToken
{
    public function __construct() {
        parent::__construct('And');
    }
}