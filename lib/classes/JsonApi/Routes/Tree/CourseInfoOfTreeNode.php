<?php

namespace JsonApi\Routes\Tree;

use JsonApi\Errors\BadRequestException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\NonJsonApiController;

class CourseInfoOfTreeNode extends NonJsonApiController
{
    use HelperTrait;

    protected $allowedFilteringParameters = ['semester', 'semclass'];

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

        $info = [
            'courses' => (int) $node->countCourses($filters['semester'], $filters['semclass'], true)
        ];

        $response->getBody()->write(json_encode($info));

        return $response->withHeader('Content-type', 'application/json');
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
