<?php

namespace JsonApi\Routes\Admission;

use JsonApi\Errors\AuthorizationFailedException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\JsonApiController;
use JsonApi\Routes\ValidationTrait;

/**
 * Create a new admission rule.
 */
class AdmissionRulesCreate extends JsonApiController
{
    use ValidationTrait;

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        $json = $this->validate($request);
        $user = $this->getUser($request);

        if (!Authority::canCreateAdmissionRules($user)) {
            throw new AuthorizationFailedException();
        }

        $rule = \AdmissionRule::getRule($args['type']);
        $rule->setAllData(self::arrayGet($json, 'data.attributes.payload'));
        $rule->id = '';

        return $this->getCreatedResponse($rule);
    }

    protected function validateResourceDocument($json, $data)
    {
        if (!self::arrayHas($json, 'data')) {
            return 'Missing `data` member at document´s top level.';
        }
        if (!self::arrayHas($json, 'data.attributes')) {
            return 'Missing `attributes` member of data block.';
        }
        if (!self::arrayHas($json, 'data.attributes.payload')) {
            return 'Missing `payload` member of attributes block.';
        }
    }

}
