<?php

namespace Studip\Plugins;

use Closure;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use StudIPPlugin;

class CustomPerformLegacyRouteStrategy implements LegacyRouteStrategy
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getCallable(string $unconsumedPath): Closure
    {
        return function (Request $request, Response $response) use ($unconsumedPath) {
            ob_start();
            /** @var ContainerInterface $this */
            $plugin = $this->get(StudIPPlugin::class);
            $plugin->perform($unconsumedPath);
            $content = ob_get_clean();
            $response->getBody()->write($content);
            $responseCode = http_response_code();
            if (!is_bool($responseCode)) {
                $response = $response->withStatus($responseCode);
            }
            return $response;
        };
    }
}
