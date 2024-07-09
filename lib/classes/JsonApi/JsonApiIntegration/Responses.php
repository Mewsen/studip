<?php

namespace JsonApi\JsonApiIntegration;

use Neomerx\JsonApi\Http\BaseResponses;
use Neomerx\JsonApi\Contracts\Encoder\EncoderInterface;
use Neomerx\JsonApi\Contracts\Http\Headers\MediaTypeInterface;
use Neomerx\JsonApi\Contracts\Encoder\Parameters\EncodingParametersInterface;
use Neomerx\JsonApi\Contracts\Http\Headers\SupportedExtensionsInterface;
use Neomerx\JsonApi\Contracts\Schema\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

/**
 * Diese Factory-Klasse verknüpft die "neomerx/json-api"-Bibliothek mit der
 * Slim-Applikation. Hier wird festgelegt, wie Slim-artige Response-Objekte gebildet
 * werden.
 */
class Responses extends BaseResponses
{
    public function __construct(
        private EncoderInterface $encoder,
        private MediaTypeInterface $outputMediaType,
        private ResponseFactoryInterface $responseFactory
    ) {
    }

    /**
     * Diese Methode ist die Schlüsselstelle der ganzen Klasse. Es
     * werden Body, Statuscode und Headers der zukünftigen Response
     * übergeben und eine \Slim\Http\Response zurückgegeben.
     *
     * @param string|null $content    der Body der zukünftigen Response
     * @param int         $statusCode der numerische Statuscode der
     *                                zukünftigen Response
     * @param array       $headers    die Header der zukünftigen Response
     *
     * @return mixed die fertige Slim-Response
     */
    protected function createResponse(?string $content, int $statusCode, array $headers)
    {
        $response = $this->responseFactory->createResponse($statusCode);
        foreach ($headers as $header => $value) {
            $response = $response->withHeader($header, $value);
        }
        $response->getBody()->write($content ?? '');

        return $response->withProtocolVersion('1.1');
    }

    /**
     * {@inheritdoc}
     *
     * @internal
     */
    protected function getEncoder(): EncoderInterface
    {
        return $this->encoder;
    }

    /**
     * {@inheritdoc}
     *
     * @internal
     */
    protected function getMediaType(): MediaTypeInterface
    {
        return $this->outputMediaType;
    }
}
