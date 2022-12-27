<?php

namespace Hoangm\Query\Exeptions;
use Hoangm\Query\Exeptions\RecordsNotFoundException;
class ModelNotFoundExeption extends RecordsNotFoundException
{
    protected string $models;
    protected int|array $ids;

    public function setModel($model, $id = []):ModelNotFoundExeption
    {
        $this->models = $model;
        $this->ids = $id;
        return $this;
    }

    public function getModels(): string
    {
        return $this->models;
    }

    public function getIds() : int|array
    {
        return $this->ids;
    }

    public function __toString()
    {
        return "Model {$this->models} with id {$this->ids} not found";
    }
}