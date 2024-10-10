<?php

namespace Starfruit\BuilderBundle\Controller;

use Starfruit\BuilderBundle\Controller\API\BaseController;
use Symfony\Component\Routing\Annotation\Route;

use Starfruit\BuilderBundle\Model\Document\Editable\EditableCustom;
use Starfruit\BuilderBundle\Model\Document\Editable\CustomLayout;
use Starfruit\BuilderBundle\Model\Document\Editable\LayoutItem;

/**
 * @Route("/builder/api/example") 
 */
class ExampleController extends BaseController
{
    /**
     * @Route("/check", methods={"GET"})
     */
    public function check()
    {
        $options = [
            'name' => 'required|length:min,2,max,255',
        ];

        $invalidRequest = $this->validateRequest($options);
        if ($invalidRequest) return $this->sendError($invalidRequest);

        return $this->sendResponse(['status' => "Success"]);
    }

    public function editableCustom()
    {
        $editableCustom = new EditableCustom(
            'Editable Custom Example',
            [
                new CustomLayout(
                    'Media',
                    [
                        new LayoutItem('image', 'logo', ['title' => 'Website Logo'], 6),
                        new LayoutItem('image', 'banner', [], 6),
                    ]
                ),
                new CustomLayout(
                    'Content',
                    [
                        new LayoutItem('input', 'name', ['placeholder' => 'Page name']),
                    ]
                ),
            ]
        );      

        $editableCustom = $editableCustom->render();
        return $editableCustom;
    }
}
