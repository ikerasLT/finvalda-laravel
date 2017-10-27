<?php

namespace Ikeraslt\Finvalda\Models;


use Illuminate\Support\Collection;

class Model
{
    protected $finvalda_class;
    protected $finvalda_param;

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
    public function getFinvaldaClass($delete = false)
    {
        return $delete ? 'Del' . $this->finvalda_class : $this->finvalda_class;
    }

    /**
     * @return string
     */
    public function getFinvaldaParam()
    {
        return $this->finvalda_param;
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

    protected function dropNullValues($object)
    {
        foreach ($object as $key => &$value) {
            if (is_array($value)) {
                $value = $this->dropNullValues($value);
            } elseif (is_null($value)) {
                unset($object[$key]);
            }
        }

        return $object;
    }
}