<?php

namespace Studip\OAuth2;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Trails\Controller;
use Trails\Response as TrailsResponse;

trait NegotiatesWithPsr7
{
    protected function getPsrRequest(): ServerRequestInterface
    {
        return studipApp(ServerRequestInterface::class);
    }

    protected function getPsrResponse(): ResponseInterface
    {
        return studipApp(ResponseFactoryInterface::class)->createResponse();
    }

    protected function convertPsrResponse(ResponseInterface $response): TrailsResponse
    {
        $trailsResponse = new TrailsResponse((string) $response->getBody(), [], $response->getStatusCode());
        foreach ($response->getHeaders() as $key => $values) {
            foreach ($values as $value) {
                $trailsResponse->add_header($key, $value);
            }
        }

        return $trailsResponse;
    }

    protected function renderPsrResponse(ResponseInterface $response): void
    {
        if (!($this instanceof Controller)) {
            throw new \Exception('Can only render responses on trails controllers');
        }

        $this->set_status($response->getStatusCode());
        $this->render_text((string) $response->getBody());
        foreach ($response->getHeaders() as $key => $values) {
            foreach ($values as $value) {
                $this->response->add_header($key, $value);
            }
        }
    }
}
