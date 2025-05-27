<?php
namespace JsonApi\Routes\ShortUrls;

use JsonApi\Errors\RecordNotFoundException;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\JsonApiController;
use JsonApi\Routes\ShortUrls\Authority;
use Psr\Http\Message\{
    ResponseInterface as Response,
    ServerRequestInterface as Request
};

class ShortUrlDelete extends JsonApiController
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        $short_url = \ShortUrl::find($args['id']);

        if ($short_url['id'] !== $args['id']) {
            throw new RecordNotFoundException();
        }

        $user = $this->getUser($request);

        if (!Authority::canDeleteShortUrl($user, $short_url)) {
            throw new AuthorizationFailedException();
        }

        $short_url->delete();

        return $this->getCodeResponse(204);
    }

}
