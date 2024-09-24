<?php
namespace JsonApi\Routes\Clipboards;

use JsonApi\Errors\BadRequestException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use Psr\Http\Message\{
    ResponseInterface as Response,
    ServerRequestInterface as Request
};

final class ClipboardItemsDelete extends JsonApiController
{
    protected $allowedFilteringParameters = ['range_id'];

    public function __invoke(Request $request, Response $response, $args): Response
    {
        $clipboard = \Clipboard::find($args['id']);
        if (!$clipboard) {
            throw new RecordNotFoundException('Clipboard not found');
        }

        $user = $this->getUser($request);
        if (!Authority::canUpdateClipboard($user, $clipboard)) {
            throw new \AccessDeniedException();
        }

        $item = null;
        if (isset($args['itemId'])) {
            $item = \ClipboardItem::find($args['itemId']);
        } else {
            $filtering = iterator_to_array($this->getQueryParameters()->getFilters());
            if (!isset($filtering['range_id'])) {
                throw new BadRequestException('No range_id filter given');
            }
            $item = \ClipboardItem::findOneBySQL(
                'clipboard_id = ? AND range_id = ?',
                [$clipboard->id, $filtering['range_id']]
            );
        }

        if (!$item) {
            throw new RecordNotFoundException('Item not found');
        }

        if ($item->clipboard_id !== $clipboard->id) {
            throw new BadRequestException('Item does not belong to clipboard');
        }

        $item->delete();

        return $this->getCodeResponse(204);
    }
}
