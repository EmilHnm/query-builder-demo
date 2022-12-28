<?php

namespace Hoangm\Query\Trait;

use DateTime;
use DateTimeInterface;
use Hoangm\Query\Interface\Arrayable;

trait HasAttribute
{
    public array $attributes = [];

    /**
     * The model attribute's original state.
     *
     * @var array
     */
    protected $original = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [];

    protected static $default_casts = [

    ];

    public function __get($name) {
        if (array_key_exists($name, $this->getAttributes())) {
            return $this->getAttribute($name);
        } else {
            return null;
        }
    }

    public function __set($key, $value) {
        return $this->setAttribute($key, $value);
    }
    /**
     * @return array
     */
    public function getOriginal(): array
    {
        return $this->original;
    }

    /**
     * @param array $original
     *
     * @return $this
     */
    protected function setOriginal(array $original)
    {
        $this->original = $original;
        return $this;
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param array $attributes
     *
     * @return $this
     */
    public function setAttributes(array $attributes)
    {
        $this->setOriginal($attributes);
        foreach ($attributes as $key => $value) {
            $this->setAttribute($key, $value);
        }
        return $this;
    }

    /**
     * Get a plain attribute.
     *
     * @param string $key
     * @return mixed
     */
    public function getAttribute(string $key)
    {
        return $this->getAttributes()[$key] ?? null;
    }

    /**
     * @param $key
     * @param $value
     *
     * @return $this
     */
    public function setAttribute($key, $value)
    {
        if ($this->isCastAttribute($key)) {
            $value = $this->castAttribute($key, $value);
        }
        $this->attributes[$key] = $value;

        return $this;
    }

    /**
     * @return array
     */
    protected function getCasts(): array
    {
        return array_merge(self::$default_casts, $this->casts);
    }

    /**
     * @param $key
     *
     * @return bool
     */
    protected function isCastAttribute($key) {
        $casts = $this->getCasts();
        return in_array($key, array_keys($casts));
    }

    /**
     * @param $key
     * @param $value
     *
     * @return mixed
     */
    protected function castAttribute($key, $value) {
        $cast_type = $this->getCastType($key);
        if (is_null($value)) {
            return null;
        }

        switch ($cast_type) {
            case 'int':
            case 'integer':
                return (int) $value;
            case 'real':
            case 'float':
            case 'double':
                return (float) $value;
            case 'string':
                return (string) $value;
            case 'bool':
            case 'boolean':
                return (bool) $value;
            case 'array':
            case 'json':
                return json_decode($value, true);
//            case 'collection':
//                if (!is_array($value)) {
//                    $value = json_decode($value, true);
//                }
//                return new Collection($value);
            case 'datetime':
                $timestamp = strtotime($value);
                $time = new DateTime();
                $time->setTimestamp($timestamp);
                return $time;
        }

        return $value;
    }

    /**
     * @param $key
     *
     * @return mixed|null
     */
    protected function getCastType($key) {
        return $this->getCasts()[$key] ?? null;
    }

    /**
     * Convert the model's attributes to an array.
     *
     * @return array
     */
    protected function attributesToArray() {
        $attributes = $this->getAttributes();

        $result = [];
        foreach ($attributes as $key => $value) {
            if ($cast_type = $this->getCastType($key)) {
                if ($cast_type === 'date' || $value === 'datetime' || $value instanceof DateTimeInterface) {
                    $value = $this->serializeDate($value);
                } elseif ($cast_type instanceof Arrayable) {
                    $value = $value->toArray();
                }
            } elseif ($value instanceof Arrayable) {
                $value = $value->toArray();
            }
            $result[$key] = $value;
        }
        return $result;
    }


}