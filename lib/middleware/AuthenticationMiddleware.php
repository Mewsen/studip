<?php
/**
 * PSR 15 middleware Stud.IP Authentication
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      André Noack <noack@data-quest.de>
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 * @since       6.0
 */
namespace Studip\Middleware;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Studip\Authentication\Manager;

final class AuthenticationMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly Manager $auth_manager,
        private readonly ResponseFactoryInterface $response_factory
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->auth_manager->start()) {
            if (isset($_SESSION['redirect_after_login'] ) && \User::findCurrent()) {
                $redirect = $_SESSION['redirect_after_login'];
                unset($_SESSION['redirect_after_login']);

                return $this->response_factory->createResponse(302)
                    ->withHeader('Location', $redirect);
            }

            return $handler->handle($request);
        } else {
            if (!match_route('dispatch.php/start')) {
                $_SESSION['redirect_after_login'] ??= \Request::url();
            } else {
                unset($_SESSION['redirect_after_login']);
            }
            return $this->response_factory->createResponse(302)
                ->withHeader('Location', \URLHelper::getURL('dispatch.php/login'));
        }
    }
}
