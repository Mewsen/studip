<?php
namespace JsonApi\Routes\Clipboards;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use Psr\Http\Message\{
    ResponseInterface as Response,
    ServerRequestInterface as Request
};

final class ClipboardsDelete extends JsonApiController
{
    public function __invoke(Request $request, Response $response, $args): Response
    {
        $clipboard = \Clipboard::find($args['id']);
        if (!$clipboard) {
            throw new RecordNotFoundException();
        }

        $user = $this->getUser($request);

        if (!Authority::canDeleteClipboard($user, $clipboard)) {
            throw new AuthorizationFailedException();
        }

        $clipboard->delete();

        return $this->getCodeResponse(204);
    }
}
