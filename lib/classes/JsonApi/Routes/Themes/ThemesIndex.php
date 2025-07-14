<?php

namespace JsonApi\Routes\Themes;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\JsonApiController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ThemesIndex extends JsonApiController
{
    protected $allowedPagingParameters = ['offset', 'limit'];

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(Request $request, Response $response): Response
    {
        if (!Authority::canIndexThemes($this->getUser($request))) {
            throw new AuthorizationFailedException();
        }

        list($offset, $limit) = $this->getOffsetAndLimit();
        $total = \Theme::countBySQL('1');
        $themes = \Theme::findBySQL("1 ORDER BY name ASC LIMIT {$offset}, {$limit}");

        return $this->getPaginatedContentResponse($themes, $total);
    }
}
