<?php

namespace Hoangm\Query;

abstract class Data
{
    use Trait\HasAttribute;

    public function __construct($attributes = [])
    {
        $this->attributes = $attributes;
    }

    public static function collection(array $items)
    {
        return new Collection($items);
    }

    public function toArray()
    {
        return $this->attributesToArray();
    }

    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }
}