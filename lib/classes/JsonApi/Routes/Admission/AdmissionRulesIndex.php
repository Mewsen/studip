<?php

namespace JsonApi\Routes\Admission;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\JsonApiController;

class AdmissionRulesIndex extends JsonApiController
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameters)
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        $rules = [];
        foreach (array_keys(\AdmissionRule::getAvailableAdmissionRules()) as $class) {
            $rules[] = new $class();
        }

        return $this->getContentResponse($rules);
    }

}
