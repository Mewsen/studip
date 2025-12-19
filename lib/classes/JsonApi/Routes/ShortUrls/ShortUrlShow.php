<?php
namespace JsonApi\Routes\ShortUrls;

use JsonApi\JsonApiController;
use Psr\Http\Message\{
    ResponseInterface as Response,
    ServerRequestInterface as Request
};

final class ShortUrlShow extends JsonApiController
{
    public function __invoke(Request $request, Response $response, $args): Response
    {
        $user = $this->getUser($request);

        $short_urls = \ShortUrl::findBySql('user_id = ? ORDER BY `alias` ASC', [$user->id]);

        return $this->getContentResponse($short_urls);
    }
}
