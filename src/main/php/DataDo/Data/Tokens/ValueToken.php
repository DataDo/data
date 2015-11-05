<?php

namespace DataDo\Data\Tokens;


class ValueToken extends AbstractToken
{
    private $value;

    public function __construct($value) {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'Value';
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->value;
    }
}