<?php

namespace JsonApi\Routes\Avatar;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AvatarOfRangeShow extends JsonApiController
{
    use AvatarHelpers;
    public function __invoke(Request $request, Response $response, $args): Response
    {
        $range_id = $args['id'];
        $range_type = $args['type'];
        $user = $this->getUser($request);

        ['class' => $class] = self::getAvatarClass($range_id, $range_type, $user);

        $resource = $class::getAvatar($range_id);

        if (!$resource) {
            throw new RecordNotFoundException();
        }

        if (!Authority::canShowAvatarOfRange($this->getUser($request), $resource)) {
            throw new AuthorizationFailedException();
        }

        return $this->getContentResponse($resource);
    }
}