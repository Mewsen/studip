<?php
namespace JsonApi\Routes\Clipboards;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\JsonApiController;
use JsonApi\Routes\ValidationTrait;
use JsonApi\Schemas\Clipboard;
use Psr\Http\Message\{
    ResponseInterface as Response,
    ServerRequestInterface as Request
};

final class ClipboardItemsCreate extends JsonApiController
{
    use ValidationTrait;

    public function __invoke(Request $request, Response $response, $args): Response
    {
        $json = $this->validate($request, $args);

        $clipboard_id = $args['id'] ?? $json['data']['relationships']['clipboard']['data']['id'];
        $clipboard = \Clipboard::find($clipboard_id);

        $user = $this->getUser($request);
        if (!Authority::canUpdateClipboard($user, $clipboard)) {
            throw new AuthorizationFailedException();
        }

        $range_id   = $json['data']['attributes']['range_id'];
        $range_type = $json['data']['attributes']['range_type'];

        $item = \ClipboardItem::findOneBySql(
            'clipboard_id = ? AND range_id = ? AND range_type = ?',
            [$clipboard_id, $range_id, $range_type]
        );

        if ($item) {
            return $this->getCodeResponse(302, [
                'Location' => $this->getLinkToItem($item),
            ]);
        }

        $item = \ClipboardItem::create([
            'clipboard_id' => $clipboard_id,
            'range_id'     => $range_id,
            'range_type'   => $range_type,
        ]);

        return $this->getContentResponse($item);
    }

    protected function validateResourceDocument($json, $data)
    {
        $clipboardValidationError = $this->validateRequestContainsValidClipboard($json, $data);
        if ($clipboardValidationError !== null) {
            return $clipboardValidationError;
        }

        if (!self::arrayHas($json, 'data.attributes.range_id')) {
            return 'No range_id defined';
        }

        if (!self::arrayHas($json, 'data.attributes.range_type')) {
            return 'No range_type defined';
        }

        $range_type = self::arrayGet($json, 'data.attributes.range_type');
        if (!is_a($range_type, \StudipItem::class, true)) {
            return 'Range type must implement interface StudipItem';
        }

        return null;
    }

    private function validateRequestContainsValidClipboard($json, $data): ?string
    {
        if (isset($data['id'])) {
            if (!\Clipboard::exists($data['id'])) {
                return 'Provided clipboard id is invalid';
            }
        } else {
            if (!self::arrayHas($json, 'data.relationships.clipboard')) {
                return 'No clipboard relationship defined';
            }

            $clipboard = self::arrayGet($json, 'data.relationships.clipboard');
            if (
                !isset($clipboard['data']['type'], $clipboard['data']['id'])
                || $clipboard['data']['type'] !== Clipboard::TYPE
            ) {
                return 'Defined clipboard relationship has invalid format.';
            }
            if (!\Clipboard::exists($clipboard['data']['id'])) {
                return 'Related clipboard does not exist.';
            }
        }

        return null;
    }

    private function getLinkToItem(\ClipboardItem $item): string
    {
        $json = $this->encoder->encodeData($item);
        return json_decode($json, true)['data']['links']['self'];
    }
}
