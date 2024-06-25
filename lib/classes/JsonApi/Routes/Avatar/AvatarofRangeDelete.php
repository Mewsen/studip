<?php

namespace JsonApi\Routes\Avatar;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Delete one Avatar.
 */
class AvatarofRangeDelete extends JsonApiController
{
    use AvatarHelpers;
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        $range_id = $args['id'];
        $range_type = $args['type'];
        $user = $this->getUser($request);

        ['class' => $class, 'has_perm' => $has_perm] = self::getAvatarClass($range_id, $range_type, $user);

        if (!$has_perm) {
            throw new AuthorizationFailedException();
        }

        $class::getAvatar($range_id)->reset();

        return $this->getCodeResponse(204);
    }
}
