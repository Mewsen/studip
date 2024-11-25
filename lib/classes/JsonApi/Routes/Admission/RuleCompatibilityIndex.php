<?php
namespace JsonApi\Routes\Admission;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\NonJsonApiController;

class RuleCompatibilityIndex extends NonJsonApiController
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameters)
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        $response->getBody()->write(json_encode(\AdmissionRuleCompatibility::getCompatibilityMatrix()));

        return $response->withHeader('Content-type', 'application/json');
    }

}
