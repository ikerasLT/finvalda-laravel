<?php

namespace Ikeraslt\Finvalda\Models;


use Illuminate\Support\Collection;

class Model
{
    protected $finvalda_class;

    /**
     * Model constructor.
     *
     * @param array|null $properties
     */
    public function __construct($properties = null)
    {
        if ($properties) {
            foreach ($properties as $key => $value) {
                if (property_exists($this, $key)) {
                    $this->$key = $value;
                }
            }
        }
    }

    /**
     * @return string
     */
    public function getFinvaldaClass()
    {
        return $this->finvalda_class;
    }

    public function toString()
    {
        return $this->__toString();
    }

    public function toArray()
    {
        $array = [];

        foreach (get_object_vars($this) as $key => $value) {
            if ($value instanceof Collection || is_array($value)) {
                foreach ($value as $item) {
                    $array[$key][] = is_object($item) ? $item->toArray() : $item;
                }
            } else {
                $array[$key] = $value;
            }
        }

        return $array;
    }

    public function __toString()
    {
        foreach (get_object_vars($this) as $key => $value) {
            if (is_null($value)) {
                unset($this->{$key});
            }
        }

        return json_encode([$this->getFinvaldaClass() => $this]);
    }
}