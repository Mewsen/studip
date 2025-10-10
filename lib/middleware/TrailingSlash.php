<?php

namespace Studip\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

class TrailingSlash
{
    public function __construct(protected ResponseFactoryInterface $responseFactory)
    {
    }

    /**
     * Handle the incoming request.
     *
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($request->getMethod() === 'GET' && $this->responseFactory) {
            $uri = $request->getUri();
            $path = $this->normalize($uri->getPath());
            if ($uri->getPath() !== $path) {
                return $this->responseFactory->createResponse(301)
                    ->withHeader('Location', (string) $uri->withPath($path));
            }
        }

        return $handler->handle($request);
    }

    private function normalize(string $path): string
    {
        if ($path === '') {
            return '/';
        }

        if (strlen($path) > 1) {
            return rtrim($path, '/');
        }

        return $path;
    }
}
