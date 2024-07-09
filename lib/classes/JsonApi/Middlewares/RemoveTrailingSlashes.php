<?php

namespace JsonApi\Middlewares;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

/**
 * Diese Klasse definiert eine Middleware, die Requests  umleitet,
 * die mit einem Schrägstrich enden (und zwar jeweils auf das Pendant
 * ohne Schrägstrich).
 */
class RemoveTrailingSlashes
{
    public function __construct(private ResponseFactoryInterface $responseFactory)
    {
    }

    /**
     * Diese Middleware überprüft den Pfad der URI des Requests. Endet
     * diese auf einem Schrägstrich, wird nicht weiter an `$next`
     * delegiert, sondern eine Response mit `Location`-Header also
     * einem Redirect zurückgegeben.
     *
     * @param Request        $request das Request-Objekt
     * @param RequestHandler $handler der PSR-15 Request Handler
     *
     * @return ResponseInterface das neue Response-Objekt
     */
    public function __invoke(Request $request, RequestHandler $handler)
    {
        $uri = $request->getUri();
        $path = $uri->getPath();

        if ('/' != $path && '/' == substr($path, -1)) {
            // recursively remove slashes when its more than 1 slash
            $path = rtrim($path, '/');

            // permanently redirect paths with a trailing slash
            // to their non-trailing counterpart
            $uri = $uri->withPath($path);

            if ('GET' == $request->getMethod()) {
                return $this->responseFactory->createResponse(301)->withHeader('Location', (string) $uri);
            } else {
                $request = $request->withUri($uri);
            }
        }

        return $handler->handle($request);
    }
}
