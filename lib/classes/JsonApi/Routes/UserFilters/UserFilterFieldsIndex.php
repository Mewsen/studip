<?php

namespace JsonApi\Routes\UserFilters;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\JsonApiController;

class UserFilterFieldsIndex extends JsonApiController
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameters)
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        $fields = [];
        foreach (\UserFilterField::getAvailableFilterFields() as $class => $name) {
            // Generic datafield conditions must be handled differently.
            if (str_contains($class, '_')) {
                [$classname, $typeparam] = explode('_', $class);
                $fields[] = new $classname($typeparam);
            } else {
                $fields[] = new $class();
            }
        }

        return $this->getContentResponse($fields);
    }

}
