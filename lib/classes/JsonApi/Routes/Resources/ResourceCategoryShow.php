<?php
namespace JsonApi\Routes\Resources;

use JsonApi\Errors\BadRequestException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use Psr\Http\Message\{
    RequestInterface as Request,
    ResponseInterface as Response
};

final class ResourceCategoryShow extends JsonApiController
{
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        if (empty($args['id'])) {
            throw new BadRequestException('Id must not be empty.');
        }

        $resource = \ResourceCategory::find($args['id']);
        if ($resource === null) {
            throw new RecordNotFoundException("No resource category found with id {$args['id']}");
        }

        return $this->getContentResponse($resource);
    }
}
