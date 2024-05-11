<?php

namespace Starfruit\BuilderBundle\Controller;

use Starfruit\BuilderBundle\Controller\API\BaseController;
use Symfony\Component\Routing\Annotation\Route;

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
}
