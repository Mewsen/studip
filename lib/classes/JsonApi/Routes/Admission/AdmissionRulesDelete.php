<?php

namespace JsonApi\Routes\Admission;

use JsonApi\Errors\AuthorizationFailedException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;

/**
 * Deletes an admission rule.
 */
class AdmissionRulesDelete extends JsonApiController
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        $user = $this->getUser($request);

        if (!Authority::canEditAdmissionRules($user)) {
            throw new AuthorizationFailedException();
        }

        [$type, $id] = explode('_', $args['id']);

        $rule = \AdmissionRule::getRule($type, $id);
        if (!$rule) {
            throw new RecordNotFoundException();
        }

        $rule->delete();

        return $this->getCodeResponse(204);
    }
}
