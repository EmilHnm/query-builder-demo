<?php

namespace Hoangm\Query;

class Collection
{
    protected $array = [];

    public function __construct($listitem = [])
    {
        $this->array = $listitem;
    }
    public function all()
    {
        return $this->array;
    }
    public function first()
    {
        return $this->array[0];
    }
    public function last()
    {
        return $this->array[count($this->array) - 1];
    }
    public function map($callback)
    {
        return array_map($callback, $this->array);
    }
    public function filter($callback)
    {
        return array_filter($this->array, $callback);
    }
    public function pluck($key = "index")
    {
        $tempArray = [];
        foreach ($this->array as $item) {
            $tempArray[] = $item->$key;
        }
        return $tempArray;
    }
    public function sortBy($callback)
    {
        usort($this->array, $callback);
        return $this->array;
    }

    public function toJson()
    {
        return json_encode($this->array);
    }
}
