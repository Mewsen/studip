<?php

namespace JsonApi\Routes\ShortUrls;

use ShortUrl;
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

        $short_url = ShortUrl::findOneBySQL(
            '`path` = ? AND `user_id` = ?',
            [$json['data']['attributes']['path'], $user->id]
        );

        if ($short_url) {
            return $this->getContentResponse($short_url);
        }

        if (\ShortUrl::countBySql('alias = ?', [ $json['data']['attributes']['alias']])) {
            throw new ConflictException(_('Der verwendete Alias existiert bereits.'));
        }

        $short_url = \ShortUrl::create([
            'path'    => $json['data']['attributes']['path'],
            'alias'   => $json['data']['attributes']['alias'],
            'title'   => $json['data']['attributes']['title'],
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

        if (!self::arrayHas($json, 'data.attributes.title')) {
            return 'No title for the link target defined';
        }

        return null;
    }
}
