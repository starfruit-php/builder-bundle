<?php

namespace Starfruit\BuilderBundle\Model\Document\Editable;

class CustomLayout
{
    public function __construct(
        protected string $label,
        protected array $layoutItems = [],
        protected string $description = '',
    )
    {
    }

    public function render()
    {
        $layouts = [];

        if (!empty($this->layoutItems)) {
            foreach ($this->layoutItems as $layoutItem) {
                if ($layoutItem instanceof LayoutElement) {
                    $layouts[] = $layoutItem->render();
                }
            }
        }

        return [
            'label' => $this->label,
            'description' => $this->description,
            'layouts' => $layouts,
        ];
    }
}
