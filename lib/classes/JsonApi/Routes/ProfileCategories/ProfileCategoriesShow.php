<?php

namespace JsonApi\Routes\ProfileCategories;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ProfileCategoriesShow extends JsonApiController
{
    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(Request $request, Response $response, array $args)
    {
        $category = \Kategorie::find($args['id']);

        if (!$category) {
            throw new RecordNotFoundException();
        }

        if (!Authority::canShowCategory($this->getUser($request), $category)) {
            throw new AuthorizationFailedException();
        }

        return $this->getContentResponse($category);
    }
}
