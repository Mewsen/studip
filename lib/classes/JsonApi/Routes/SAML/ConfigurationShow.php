<?php

namespace JsonApi\Routes\SAML;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Routes\Route;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Studip\SAML\SetupInformation;

class ConfigurationShow extends Route
{
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        if (!$GLOBALS['perm']->have_perm('root')) {
            throw new AuthorizationFailedException();
        }

        $setupInformation = $this->container->get(SetupInformation::class);
        $config = $setupInformation->getConfiguration();

        return $this->jsonResponse($response, [
            'data' => [
                'type' => 'saml-configuration',
                'id' => '1',
                'attributes' => $config,
            ],
        ]);
    }
}