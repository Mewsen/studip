<?php
namespace JsonApi\Routes\Resources;

use JsonApi\Schemas\ResourceBookingIntervalSchema;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use ResourceBookingInterval;

final class ResourceBookingIntervalShow extends JsonApiController
{
    protected $allowedIncludePaths = [
        ResourceBookingIntervalSchema::REL_BOOKING,
        ResourceBookingIntervalSchema::REL_RESOURCE,
    ];

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $interval = ResourceBookingInterval::find($args['id']);
        if (!$interval) {
            throw new RecordNotFoundException("No booking interval with the id {$args['id']}");
        }

        return $this->getContentResponse($interval);
    }
}
