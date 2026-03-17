<?php
namespace Studip\Middleware;

use AccessDeniedException;
use LoginException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use User;

final class HandleAccessDeniedMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (AccessDeniedException $ade) {
            if (!User::findCurrent()) {
                throw new LoginException();
            }

            throw $ade;
        }
    }
}
