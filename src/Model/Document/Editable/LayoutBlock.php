<?php

namespace Starfruit\BuilderBundle\Model\Document\Editable;

class LayoutBlock extends LayoutElement
{
    public function __construct(
        protected string $prefix,
        protected array $layoutItems,
        protected ?string $listTitle = null,
        protected int $col = self::DEFAULT_COL,
    )
    {
    }

    public function render()
    {
        $fields = [];

        if (!empty($this->layoutItems)) {
            foreach ($this->layoutItems as $layoutItem) {
                if ($layoutItem instanceof LayoutItem) {
                    $fields[] = $layoutItem->renderBlock();
                }
            }
        }

        return [
            'editable' => 'list',
            'listTitle' => $this->listTitle,
            'col' => $this->col,
            'params' => [
                'prefix' => $this->prefix,
                'fields' => $fields,
            ]
        ];
    }
}
