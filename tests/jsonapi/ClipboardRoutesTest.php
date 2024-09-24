<?php

use JsonApi\Routes\Clipboards\ClipboardItemsCreate;
use JsonApi\Routes\Clipboards\ClipboardItemsDelete;
use JsonApi\Routes\Clipboards\ClipboardsCreate;
use JsonApi\Routes\Clipboards\ClipboardsDelete;
use JsonApi\Routes\Clipboards\ClipboardsUpdate;
use JsonApi\Schemas\Clipboard as ClipboardSchema;
use JsonApi\Schemas\ClipboardItem as ClipboardItemSchema;
use WoohooLabs\Yang\JsonApi\Response\JsonApiResponse;
use WoohooLabs\Yang\JsonApi\Schema\Resource\ResourceObject;

require_once __DIR__ . '/JSONAPIHelperTrait.php';

class ClipboardRoutesTest extends Codeception\Test\Unit
{
    use JSONAPIHelperTrait;

    public function testCreateClipboard(): void
    {
        $resource = $this->createClipboard(
            $this->tester->getCredentialsForTestDozent()
        );

        $this->assertHasRelations($resource, 'user', 'clipboard-items');
        $this->assertEquals(ClipboardSchema::TYPE, $resource->type());
        $this->assertEquals('Test-Clipboard', $resource->attribute('name'));
    }

    public function testUpdateClipboard(): void
    {
        $credentials = $this->tester->getCredentialsForTestDozent();
        $resource = $this->createClipboard($credentials);

        $response = $this->sendMockRequest(
            "/clipboards/{id}",
            ClipboardsUpdate::class,
            $credentials,
            ['id' => $resource->id()],
            [
                'considered_successful' => [200],
                'method' => 'PATCH',
                'json_body' => [
                    'data' => [
                        'attributes' => ['name' => 'Foo Bar'],
                    ],
                ],
            ],
        );

        $resource = $this->getResourceFromResponse($response);

        $this->assertEquals('Foo Bar', $resource->attribute('name'));
    }

    public function testDeleteClipboard(): void
    {
        $credentials = $this->tester->getCredentialsForTestDozent();

        $resource = $this->createClipboard($credentials);

        $this->sendMockRequest(
            "/clipboards/{id}",
            ClipboardsDelete::class,
            $credentials,
            ['id' => $resource->id()],
            [
                'considered_successful' => [204],
                'method' => 'DELETE',
            ],
        );
    }

    public function testAddItemToClipboard(): void
    {
        $credentials = $this->tester->getCredentialsForTestDozent();
        $resource = $this->createClipboard($credentials);

        $resource = $this->createClipboardItem(
            $credentials,
            $resource->id(),
            'abcd1234',
            'Room'
        );

        $this->assertHasRelations($resource, 'clipboard');
        $this->assertEquals(ClipboardItemSchema::TYPE, $resource->type());
        $this->assertEquals('abcd1234', $resource->attribute('range_id'));
        $this->assertEquals('Room', $resource->attribute('range_type'));
    }

    public function testRemoveItemFromClipboard(): void
    {
        $credentials = $this->tester->getCredentialsForTestDozent();
        $clipboard = $this->createClipboard($credentials);
        $item = $this->createClipboardItem(
            $credentials,
            $clipboard->id(),
            'abcd1234',
            'Room'
        );

        $this->sendMockRequest(
            "/clipboards/{id}/items/{itemId}",
            ClipboardItemsDelete::class,
            $credentials,
            [
                'id' => $clipboard->id(),
                'itemId' => $item->id(),
            ],
            [
                'considered_successful' => [204],
                'method' => 'DELETE',
            ],
        );
    }

    protected function createClipboard(array $credentials, string $name = 'Test-Clipboard'): ResourceObject
    {
        $response = $this->sendMockRequest(
            "/clipboards",
            ClipboardsCreate::class,
            $credentials,
            [],
            [
                'considered_successful' => [200],
                'method' => 'POST',
                'json_body' => [
                    'data' => [
                        'type' => ClipboardSchema::TYPE,
                        'attributes' => ['name' => $name],
                    ],
                ],
            ],
        );

        return $this->getResourceFromResponse($response);
    }

    protected function createClipboardItem(
        array $credentials,
        string $clipboard_id,
        string $range_id,
        string $range_type
    ): ResourceObject {
        $response = $this->sendMockRequest(
            "/clipboards/{id}/items",
            ClipboardItemsCreate::class,
            $credentials,
            ['id' => $clipboard_id],
            [
                'considered_successful' => [200],
                'method' => 'POST',
                'json_body' => [
                    'data' => [
                        'attributes' => [
                            'range_id'   => $range_id,
                            'range_type' => $range_type,
                        ],
                    ],
                ],
            ],
        );

        return $this->getResourceFromResponse($response);
    }
}
