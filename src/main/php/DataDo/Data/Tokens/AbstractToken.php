<?php

namespace DataDo\Data\Tokens;


abstract class AbstractToken implements Token, \JsonSerializable
{
    function jsonSerialize() {
        return [
            'name' => $this->getName(),
            'source' => $this->getSource()
        ];
    }
}