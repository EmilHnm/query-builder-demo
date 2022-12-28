<?php

namespace Hoangm\Query\Trait;

use DateTime;

trait HasTimestamps {
    public bool $timestamps = true;

    protected static string $createdAtColumn = 'created_at';
    protected static string $updatedAtColumn = 'updated_at';

    public function touch(string|null $value = null) {
        if($value) {
            $this->$value = $this->freshTimestamp();
            return $this->save();
        } 

        if(!$this->timestamps) {
            return false;
        }

        $this->updateTimestamps();

        return true;
    }


    public function updateTimestamps()
    {
        $time = $this->freshTimestamp();

        $updatedAtColumn = $this->getUpdatedAtColumn();

        if (! is_null($updatedAtColumn) && ! $this->exists) {
            $this->setUpdatedAt($time);
        }

        $createdAtColumn = $this->getCreatedAtColumn();

        if (! $this->exists && ! is_null($createdAtColumn)) {
            $this->setCreatedAt($time);
        }

        return $this;
    }

    public function freshTimestamp()
    {
        return date('Y-m-d H:i:s');
    }

    public function getCreatedAtColumn()
    {
        return static::$createdAtColumn;
    }

    public function getUpdatedAtColumn()
    {
        return static::$updatedAtColumn;
    }

    public function setUpdatedAt($value)
    {
        $this->{$this->getUpdatedAtColumn()} = $value;
        return $this;
    }

    public function setCreatedAt($value)
    {
        $this->{$this->getCreatedAtColumn()} = $value;
        return $this;
    }
}