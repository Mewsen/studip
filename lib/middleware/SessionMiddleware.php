<?php
/**
 * PSR 15 middleware Stud.IP Session management
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

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Studip\Session\Manager;

final class SessionMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly Manager $session_manager
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->session_manager->start();
        $response = $handler->handle($request);
        try {
            \NotificationCenter::postNotification('PageCloseWillExecute', null);
            $this->session_manager->save();
            if (isset($GLOBALS['user'])) {
                $GLOBALS['user']->set_last_action();
            }
            \NotificationCenter::postNotification('PageCloseDidExecute', null);
        } catch (\NotificationVetoException $e) {}
        return $response;
    }
}
