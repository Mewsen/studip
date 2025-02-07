<?php

namespace Studip\Middleware;

use Closure;
use Psr\Http\Message\RequestInterface;
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
    public function __invoke(RequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $uri = $request->getUri();
        $path = $this->normalize($uri->getPath());
        if ($this->responseFactory && ($uri->getPath() !== $path)) {
            return $this->responseFactory->createResponse(301)
                ->withHeader('Location', (string) $uri->withPath($path));
        }

        $response = $handler->handle($request);

        return $response;
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
