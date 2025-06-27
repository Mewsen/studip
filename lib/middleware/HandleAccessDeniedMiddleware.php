<?php
namespace Studip\Middleware;

use AccessDeniedException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Request;
use URLHelper;

final class HandleAccessDeniedMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory
    ) {
    }

    /**
     * @SuppressWarnings(StaticAccess)
     * @SuppressWarnings(SuperGlobals)
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (AccessDeniedException $ade) {
            $_SESSION['redirect_after_login'] ??= Request::url();
            return $this->responseFactory->createResponse(302)
                ->withHeader('Location', URLHelper::getURL('dispatch.php/login'));
        }
    }
}
