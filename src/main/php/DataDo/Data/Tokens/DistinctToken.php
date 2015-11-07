<?php

namespace DataDo\Data\Tokens;


class DistinctToken extends ConstantToken
{
    public function __construct() {
        parent::__construct('Distinct');
    }
}