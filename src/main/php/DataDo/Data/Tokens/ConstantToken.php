<?php
/**
 * //TODO PHPDoc
 * @author Thomas Biesaart
 */

namespace DataDo\Data\Tokens;


abstract class ConstantToken extends AbstractToken
{
    private $name;

    protected function  __construct($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getSource()
    {
        return $this->name;
    }
}