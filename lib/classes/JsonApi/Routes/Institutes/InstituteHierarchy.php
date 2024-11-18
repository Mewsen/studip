<?php
namespace JsonApi\Routes\Institutes;

use DI\NotFoundException;
use Institute;
use JsonApi\JsonApiController;
use Psr\Http\Message\{
    ResponseInterface as Response,
    ServerRequestInterface as Request
};

final class InstituteHierarchy extends JsonApiController
{
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $institute = Institute::find($args['id']);
        if (!$institute) {
            throw new NotFoundException();
        }

        $hierarchy = $this->getHierarchyUp($institute);

        return $this->getContentResponse($hierarchy);
    }

    private function getHierarchyUp(Institute $institute): array
    {
        $hierarchy = [];
        do {
            $hierarchy[] = $institute;

            $range_tree = \RangeTreeNode::findOneBySQL(
                "studip_object_id = ?",
                [$institute->id]
            );

            $institute = $range_tree?->parent->institute;
        } while ($institute);

        return array_reverse($hierarchy);
    }
}
