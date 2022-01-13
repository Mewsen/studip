<?php

namespace JsonApi\Routes\Courseware;

use Courseware\Block;
use Courseware\CustomFiles;
use JsonApi\Routes\Files\RoutesHelperTrait;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Create a block in a container.
 */
class CustomFilesList extends JsonApiController
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

        if (!Authority::canShowBlock($user = $this->getUser($request), $resource)) {
            throw new AuthorizationFailedException();
        }

        if (!$resource->type instanceof CustomFiles) {
            return $response;
        }

        return $this->getContentResponse($resource->type->getCustomFiles());
    }
}
