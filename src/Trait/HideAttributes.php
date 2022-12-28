<?php

namespace Hoangm\Query\Trait;

trait HideAttributes
{
    protected array $hidden = [];
    protected array $visible = [];

    public function getHidden(): array
    {
        return $this->hidden;
    }

    public function setHidden(array $hidden): self
    {
        $this->hidden = $hidden;
        return $this;
    }

    public function getVisible(): array
    {
        return $this->visible;
    }

    public function setVisible(array $visible): self
    {
        $this->visible = $visible;
        return $this;
    }
}