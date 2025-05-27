<?php

namespace JsonApi\Routes\ShortUrls;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\ConflictException;
use JsonApi\JsonApiController;
use JsonApi\Routes\ValidationTrait;
use Psr\Http\Message\{
    ResponseInterface as Response,
    ServerRequestInterface as Request
};

final class ShortUrlCreate extends JsonApiController
{
    use ValidationTrait;

    public function __invoke(Request $request, Response $response, $args): Response
    {
        $user = $this->getUser($request);

        if (!Authority::canCreateShortUrl($user)) {
            throw new AuthorizationFailedException();
        }


        $json = $this->validate($request, $args);

        if (\ShortUrl::countBySql('path = ? AND user_id = ?', [ $json['data']['attributes']['path'], $user->id]) > 0) {
            throw new ConflictException(_('Sie haben für diese Seite bereits eine Kurz-URL erstellt!'));
        }

        if (\ShortUrl::countBySql('alias = ?', [ $json['data']['attributes']['alias']])) {
            throw new ConflictException(_('Sie haben für diese Seite bereits eine Kurz-URL erstellt!'));
        }

        $short_url = \ShortUrl::create([
            'path'    => $json['data']['attributes']['path'],
            'alias'   => $json['data']['attributes']['alias'],
            'user_id' => $user->id,
        ]);

        return $this->getContentResponse($short_url);
    }

    protected function validateResourceDocument($json, $data)
    {
        if (!self::arrayHas($json, 'data.attributes.path')) {
            return 'No url for the short-url defined';
        }

        if (!trim(self::arrayGet($json, 'data.attributes.alias'))) {
            return 'No alias for the short-url defined';
        }

        return null;
    }
}
