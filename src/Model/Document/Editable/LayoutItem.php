<?php

namespace Starfruit\BuilderBundle\Model\Document\Editable;

class LayoutItem extends LayoutElement
{
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

    public function renderBlock()
    {
        return array_merge($this->params, [
            'type' => $this->editable,
            'name' => $this->name
        ]);
    }
}
