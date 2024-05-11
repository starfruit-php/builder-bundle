<?php

namespace Starfruit\BuilderBundle\Controller\API;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class BaseController extends \Starfruit\BuilderBundle\Controller\BaseController
{
    /**
     * Return a success response.
     * 
     * @param array $response
     * 
     * @return JsonResponse
     */
    public function sendResponse($response = [])
    {
        return new JsonResponse($response, Response::HTTP_OK);
    }

    /**
     * Return an error response.
     * 
     * @param mix $error
     * @param int $statusCode
     * 
     * @return JsonResponse
     */
    public function sendError($error, $statusCode = Response::HTTP_BAD_REQUEST)
    {
        // logging if status code = 500
        if ($statusCode == Response::HTTP_INTERNAL_SERVER_ERROR) {
            
        } else {
            if (is_array($error)) {
                $error = [
                    "error" => $error
                ];
            }

            if (is_string($error)) {
                $error = [
                    "error" => [
                        "message" => $this->translator->trans($error)
                    ]
                ];
            }
        }

        return new JsonResponse($error, $statusCode);
    }
}
