<?php
namespace JsonApi\Routes\ShortUrls;

use JsonApi\Errors\RecordNotFoundException;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\JsonApiController;
use JsonApi\Routes\ValidationTrait;
use Psr\Http\Message\{
    ResponseInterface as Response,
    ServerRequestInterface as Request
};

final class ShortUrlUpdate extends JsonApiController
{
    use ValidationTrait;

    public function __invoke(Request $request, Response $response, $args): Response
    {
        $user = $this->getUser($request);

        $short_url = \ShortUrl::find($args['id']);

        if ($short_url['id'] !== $args['id']) {
            throw new RecordNotFoundException();
        }

        if (!Authority::canUpdateShortUrl($user, $short_url)) {
            throw new AuthorizationFailedException();
        }


        $json = $this->validate($request);

        $short_url->alias = $json['data']['attributes']['alias'];
        $short_url->store();

        return $this->getContentResponse($short_url);
    }

    protected function validateResourceDocument($json, $data)
    {
        if (!self::arrayHas($json, 'data.attributes.alias')) {
            return 'No alias for the short-url defined';
        }

        return null;
    }
}
