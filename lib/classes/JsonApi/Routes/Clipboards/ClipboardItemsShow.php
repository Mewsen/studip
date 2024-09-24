<?php
namespace JsonApi\Routes\Clipboards;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use Psr\Http\Message\{
    ResponseInterface as Response,
    ServerRequestInterface as Request
};

final class ClipboardItemsShow extends JsonApiController
{
    public function __invoke(Request $request, Response $response, $args): Response
    {
        $item = \ClipboardItem::find($args['id']);
        if (!$item) {
            throw new RecordNotFoundException();
        }

        $user = $this->getUser($request);
        if (!Authority::canAccessClipboard($user, $item->clipboard)) {
            throw new AuthorizationFailedException();
        }

        return $this->getContentResponse($item);
    }
}
