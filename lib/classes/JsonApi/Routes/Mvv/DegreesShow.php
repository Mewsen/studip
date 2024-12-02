<?php

namespace JsonApi\Routes\Mvv;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;

class DegreesShow extends JsonApiController
{
    protected $allowedIncludePaths = [];

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        $degree = \Abschluss::find($args['id']);
        if (!$degree) {
            throw new RecordNotFoundException('Could not find degree.');
        }

        return $this->getContentResponse($degree);
    }
}
