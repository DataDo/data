<?php
/**
 * //TODO PHPDoc
 * @author Thomas Biesaart
 */

namespace DataDo\Data\Tokens;


class ValueToken implements Token
{
    private $value;

    public function __construct($value) {
        $this->value = $value;
    }

    public function getName()
    {
        return 'Value';
    }

    public function getSource()
    {
        return $this->value;
    }
}