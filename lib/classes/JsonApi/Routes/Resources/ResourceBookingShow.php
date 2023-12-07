<?php
namespace JsonApi\Routes\Resources;

use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use Psr\Http\Message\{
    RequestInterface as Request,
    ResponseInterface as Response
};
use ResourceBooking;

final class ResourceBookingShow extends JsonApiController
{
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $booking = ResourceBooking::find($args['id']);
        if (!$booking) {
            throw new RecordNotFoundException("No booking with the id {$args['id']}");
        }

        return $this->getContentResponse($booking);
    }
}
