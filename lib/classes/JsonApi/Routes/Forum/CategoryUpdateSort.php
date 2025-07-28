<?php
namespace JsonApi\Routes\Forum;

use CoreForum;
use JsonApi\Errors\RecordNotFoundException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\JsonApiController;
use JsonApi\Routes\ValidationTrait;
use Forum\Category;

class CategoryUpdateSort extends JsonApiController
{
    use ValidationTrait;

    public function __invoke(Request $request, Response $response, $args)
    {
        $json = $this->validate($request);
        $range_id = self::arrayGet($json, 'data.relationships.range.data.id');

        $range = get_object_by_range_id($range_id);
        if (!$range) {
            throw new RecordNotFoundException();
        }

        if (!CoreForum::isModerator($range->id)) {
            throw new AuthorizationFailedException();
        }

        $category_ids = self::arrayGet($json, 'data.attributes.category-ids');

        Category::findEachBySQL(
            function (Category $category) use ($category_ids) {
                $category->position = (int) array_search($category->category_id, $category_ids);
                $category->store();
            },
            "category_id IN (:category_ids) AND range_id = :range_id",
            [
                "category_ids" => $category_ids,
                "range_id" => $range->id
            ]
        );

        return $this->getCodeResponse(204);
    }

    protected function validateResourceDocument($json, $data)
    {
        $required_keys = [
            'data.attributes.category-ids' => 'Missing `data.attributes.category-ids`',
            'data.relationships.range.data.id' => 'Missing `data.relationships.range.data.id`',
        ];

        foreach ($required_keys as $key => $error_message) {
            if (!self::arrayHas($json, $key)) {
                return $error_message;
            }
        }

        return null;
    }
}
