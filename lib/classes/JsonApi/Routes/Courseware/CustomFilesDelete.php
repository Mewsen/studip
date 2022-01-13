<?php

namespace JsonApi\Routes\Courseware;

use Courseware\Block;
use JsonApi\Routes\Files\RoutesHelperTrait;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Create a block in a container.
 */
class CustomFilesDelete extends JsonApiController
{
    use RoutesHelperTrait;

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        if (!($resource = Block::find($args['id']))) {
            throw new RecordNotFoundException();
        }

        if (!Authority::canUpdateBlock($user = $this->getUser($request), $resource)) {
            throw new AuthorizationFailedException();
        }

        $resource->type->deleteCustomFile($args['id']);
        return $response;
    }
}
