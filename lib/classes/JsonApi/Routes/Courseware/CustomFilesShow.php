<?php

namespace JsonApi\Routes\Courseware;

use Courseware\Block;
use GuzzleHttp\Psr7;
use JsonApi\Routes\Files\RoutesHelperTrait;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\NonJsonApiController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Create a block in a container.
 */
class CustomFilesShow extends NonJsonApiController
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

        return $response->withBody(
            $stream = Psr7\stream_for($resource->type->readCustomFile($args['id']))
        );
    }
}
