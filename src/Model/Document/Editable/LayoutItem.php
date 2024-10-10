<?php

namespace Starfruit\BuilderBundle\Model\Document\Editable;

class LayoutItem
{
    const DEFAULT_PARAMS = [];
    const DEFAULT_COL = 12;

    public function __construct(
        protected string $editable,
        protected string $name,
        protected array $params = self::DEFAULT_PARAMS,
        protected int $col = self::DEFAULT_COL,
    )
    {
    }

    public function render()
    {
        return [
            'editable' => $this->editable,
            'col' => $this->col,
            'params' => array_merge($this->params, ['name' => $this->name]),
        ];
    }
}
