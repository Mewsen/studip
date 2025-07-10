<?php
namespace Studip\Middleware;

use Forum\ForumTopic;
use NotificationCenter;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\{ResponseInterface as Response,
    ServerRequestInterface as Request,
    UriInterface
};
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\App;

final class LegacyRedirectorMiddleware implements MiddlewareInterface
{
    private array $legacyRoutes = [];

    public function __construct(
        private readonly App $app,
        private readonly ResponseFactoryInterface $response_factory
    ) {
        $this->addLegacyRoute(
            '#^course/forum/index/index#',
            [$this, 'redirectForumPost']
        );

        NotificationCenter::postNotification('LEGACY_REDIRECTOR_DID_CREATE', $this);
    }

    public function addLegacyRoute(string $route, callable $handler): void
    {
        $this->legacyRoutes[$route] = $handler;
    }

    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        $path = $this->getRelativePath($request);

        foreach ($this->legacyRoutes as $legacyRoute => $routeHandler) {
            if ($this->routeMatches($path, $legacyRoute)) {
                /** @var UriInterface $newUri */
                $newUri = $routeHandler($request);

                $response = $this->response_factory->createResponse(301);
                $response->getBody()->write('');
                return $response->withHeader('Location', (string) $newUri);
            }
        }

        return $handler->handle($request);
    }

    private function getRelativePath(Request $request): string
    {
        $basePath = $this->app->getBasePath();
        $fullPath = $request->getUri()->getPath();

        return ltrim(substr($fullPath, strlen($basePath)), '/');
    }

    private function routeMatches(string $path, string $route): bool
    {
        if (str_starts_with($route, '#') && str_ends_with($route, '#')) {
            return preg_match($route, $path);
        }

        return str_starts_with($path, $route);
    }

    private function redirectForumPost(Request $request): UriInterface
    {
        $uri = $request->getUri();
        $range_id = $request->getQueryParams()['cid'];
        $forum_id = explode('/', $this->getRelativePath($request))[4] ?? null;

        if ($forum_id === $range_id) {
            $redirectUri = $uri->withPath(
                str_replace(
                    'course/forum/index/index/' . $forum_id,
                    'course/forum/topics',
                    $uri->getPath()
                )
            );
        } elseif (ForumTopic::exists($forum_id)) {
            $redirectUri = $uri->withPath(
                str_replace(
                    'course/forum/index/index',
                    'course/forum/topics/show',
                    $uri->getPath()
                )
            );
        } else {
            $redirectUri = $uri->withPath(
                str_replace(
                    'course/forum/index/index',
                    'course/forum/discussions/show',
                    $uri->getPath()
                )
            );
        }

        return $redirectUri;
    }
}
