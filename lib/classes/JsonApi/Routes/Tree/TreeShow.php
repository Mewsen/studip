<?php


namespace JsonApi\Routes\Tree;

use JsonApi\Errors\BadRequestException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use Neomerx\JsonApi\Contracts\Http\ResponsesInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class TreeShow extends JsonApiController
{
    use \JsonApi\Routes\Tree\HelperTrait;

    protected $allowedFilteringParameters = ['semester', 'semclass'];
    protected $allowUnrecognizedParams = true;
    protected $allowedIncludePaths = [
        'children',
        'courseinfo',
        'courses',
        'institute',
        'parent',
    ];

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameters)
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        /** @var class-string<\StudipTreeNode> $classname */
        [$classname, $id] = explode('_', $args['id']);

        $node = $classname::getNode($id);
        if (!$node) {
            throw new RecordNotFoundException();
        }

        $error = $this->validateFilters($request);
        if ($error) {
            throw new BadRequestException($error);
        }

        $filters = $this->getContextFilters($request);

        return $this->getContentResponse(
            $node,
            ResponsesInterface::HTTP_OK,
            [],
            ['courses' => $node->countCourses($filters['semester'], $filters['semclass'], true)]
        );
    }

    private function validateFilters(Request $request): ?string
    {
        $filtering = $request->getQueryParams()['filter'] ?? [];

        return $this->validateSemesterFilter($filtering)
            ?? $this->validateSemClassFilter($filtering);
    }

    private function getContextFilters(Request $request): array
    {
        $filters = array_merge(
            [
                'semester' => 'all',
                'semclass' => 0,
            ],
            $request->getQueryParams()['filter'] ?? []
        );

        $filters['semclass'] = (int) $filters['semclass'];

        return $filters;
    }
}
