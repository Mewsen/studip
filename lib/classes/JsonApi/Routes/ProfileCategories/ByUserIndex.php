<?php

namespace JsonApi\Routes\ProfileCategories;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use JsonApi\Routes\Users\Authority as UserAuthority;
use JsonApi\Schemas\ProfileCategory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ByUserIndex extends JsonApiController
{
    protected $allowedIncludePaths = [ProfileCategory::REL_USER];
    protected $allowedPagingParameters = ['offset', 'limit'];

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(Request $request, Response $response, $args): Response
    {
        $user = \User::find($args['id']);
        $observer = $this->getUser($request);

        if (!$user) {
            throw new RecordNotFoundException();
        }

        if (!UserAuthority::canShowUser($observer, $user)) {
            throw new AuthorizationFailedException();
        }

        $entries = \Kategorie::findByUserId($user->id);
        $entries = array_filter($entries, fn($entry) => Authority::canShowCategory($observer, $entry));
        [$offset, $limit] = $this->getOffsetAndLimit();

        return $this->getPaginatedContentResponse(array_slice($entries, $offset, $limit), count($entries));
    }
}
