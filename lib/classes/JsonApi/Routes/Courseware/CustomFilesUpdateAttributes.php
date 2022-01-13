<?php

namespace JsonApi\Routes\Courseware;

use Courseware\Block;
use Courseware\Filesystem\CustomFile;
use JsonApi\Routes\Files\RoutesHelperTrait;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Create a block in a container.
 */
class CustomFilesUpdateAttributes extends JsonApiController
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

        $body = $request->getParsedBody();

        $custom_file = new CustomFile(
            $body['data']['id'],
            $args['id'],
            $body['data']['attributes']
        );

        return $this->getContentResponse(
            $resource->type->updateCustomFileMetadata(
                $args['file_id'], $custom_file)
        );
    }
}
