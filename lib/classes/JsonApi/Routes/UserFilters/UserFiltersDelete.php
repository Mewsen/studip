<?php

namespace JsonApi\Routes\UserFilters;

use JsonApi\Errors\AuthorizationFailedException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;

/**
 * Deletes a user filter
 */
class UserFiltersDelete extends JsonApiController
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        $user = $this->getUser($request);

        if (!Authority::canEditUserFilters($user)) {
            throw new AuthorizationFailedException();
        }

        $filter = new \UserFilter($args['id']);

        if ($filter['id'] !== $args['id']) {
            throw new RecordNotFoundException();
        }

        $filter->delete();

        return $this->getCodeResponse(204);
    }

}
