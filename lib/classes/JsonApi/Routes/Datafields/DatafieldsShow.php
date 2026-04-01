<?php

namespace JsonApi\Routes\Datafields;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class DatafieldsShow extends JsonApiController
{
    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(Request $request, Response $response, array $args)
    {
        $datafield = \DataField::find($args['id']);

        if (!$datafield) {
            throw new RecordNotFoundException();
        }

        if (!Authority::canShowDatafield($this->getUser($request), $datafield)) {
            throw new AuthorizationFailedException();
        }

        return $this->getContentResponse($datafield);
    }
}
