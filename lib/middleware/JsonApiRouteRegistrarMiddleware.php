<?php
namespace Studip\Middleware;

use JsonApi\RouteMap;
use Psr\Http\Message\{
    ResponseInterface as Response,
    ServerRequestInterface as Request
};
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class JsonApiRouteRegistrarMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly \Slim\App $app
    ) {
    }

    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        $this->app->group('/v1', RouteMap::class);

        return $handler->handle($request);
    }
}
