<?php

namespace Ikeraslt\Finvalda\Exceptions;


use Exception;

class WrongAttributeException extends Exception
{
    protected $attribute;

    /**
     * WrongAttributeException constructor.
     *
     * @param $attribute
     */
    public function __construct($attribute)
    {
        $this->attribute = $attribute;
    }

    /**
     * @return string
     */
    public function getAttribute()
    {
        return $this->attribute;
    }
}