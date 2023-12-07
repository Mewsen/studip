<?php
namespace JsonApi\Routes\Resources;

use JsonApi\Schemas\ResourceSchema;
use JsonApi\Errors\BadRequestException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use Psr\Http\Message\{
    RequestInterface as Request,
    ResponseInterface as Response
};

final class ResourceShow extends JsonApiController
{
    protected $allowedIncludePaths = [ResourceSchema::REL_CATEGORY];

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        if (empty($args['id'])) {
            throw new BadRequestException('Id must not be empty.');
        }

        $resource = \Resource::find($args['id']);
        if ($resource === null) {
            throw new RecordNotFoundException("No resource found with id {$args['id']}");
        }

        return $this->getContentResponse($resource);
    }
}
