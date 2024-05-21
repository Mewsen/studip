<?php
namespace JsonApi\Routes\Clipboards;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\JsonApiController;
use JsonApi\Routes\ValidationTrait;
use Psr\Http\Message\{
    ResponseInterface as Response,
    ServerRequestInterface as Request
};

final class ClipboardsCreate extends JsonApiController
{
    use ValidationTrait;

    public function __invoke(Request $request, Response $response, $args): Response
    {
        $user = $this->getUser($request);

        if (!Authority::canCreateClipboard($user)) {
            throw new AuthorizationFailedException();
        }

        $json = $this->validate($request, $args);

        $clipboard = \Clipboard::create([
            'name'    => $json['data']['attributes']['name'],
            'user_id' => $user->id,
        ]);

        return $this->getContentResponse($clipboard);
    }

    protected function validateResourceDocument($json, $data)
    {
        if (!self::arrayHas($json, 'data.attributes.name')) {
            return 'No name for the clipboard defined';
        }

        if (!trim(self::arrayGet($json, 'data.attributes.name'))) {
            return 'Name of the clipboard may not be empty';
        }

        return null;
    }
}
