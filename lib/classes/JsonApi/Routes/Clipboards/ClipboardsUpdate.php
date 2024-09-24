<?php
namespace JsonApi\Routes\Clipboards;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use JsonApi\Routes\ValidationTrait;
use Psr\Http\Message\{
    ResponseInterface as Response,
    ServerRequestInterface as Request
};

final class ClipboardsUpdate extends JsonApiController
{
    use ValidationTrait;

    public function __invoke(Request $request, Response $response, $args): Response
    {
        $clipboard = \Clipboard::find($args['id']);
        if (!$clipboard) {
            throw new RecordNotFoundException();
        }

        $user = $this->getUser($request);

        if (!Authority::canUpdateClipboard($user, $clipboard)) {
            throw new AuthorizationFailedException();
        }

        $json = $this->validate($request, $args);

        $clipboard->name = $json['data']['attributes']['name'];
        $clipboard->store();

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
