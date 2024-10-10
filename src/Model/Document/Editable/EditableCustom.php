<?php

namespace Starfruit\BuilderBundle\Model\Document\Editable;

class EditableCustom
{
    public function __construct(
        protected string $title,
        protected array $customLayouts,
    )
    {
    }

    public function render()
    {
        $customLayouts = [];

        if (!empty($this->customLayouts)) {
            foreach ($this->customLayouts as $customLayout) {
                if ($customLayout instanceof CustomLayout) {
                    $customLayouts[] = $customLayout->render();
                }
            }
        }

        return [
            'adminLayoutTitle' => $this->title,
            'customLayouts' => $customLayouts,
        ];
    }
}
