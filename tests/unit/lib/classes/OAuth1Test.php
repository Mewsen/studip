<?php

use Psr\Http\Message\ServerRequestInterface;
use Studip\OAuth1;

/**
 * All values are from the OAuth 1.0 Authentication Sandbox (using the example
 * used in the OAuth Specification).
 *
 * @see http://lti.tools/oauth/
 */
final class OAuth1Test extends \Codeception\Test\Unit
{
    /**
     * @covers OAuth1::getSignatureBaseString
     */
    public function testCreationOfBaseString(): void
    {
        $this->assertEquals(
            'GET&http%3A%2F%2Fphotos.example.net%2Fphotos&file%3Dvacation.jpg%26oauth_consumer_key%3Ddpf43f3p2l4k3l03%26oauth_nonce%3Dkllo9940pd9333jh%26oauth_signature_method%3DHMAC-SHA1%26oauth_timestamp%3D1191242096%26oauth_token%3Dnnch734d00sl2jdk%26oauth_version%3D1.0%26size%3Doriginal',
            OAuth1::getSignatureBaseString($this->getDefaultTestRequest())
        );
    }

    /**
     * @covers OAuth1::signRequest
     */
    public function testSigningARequest(): void
    {
        $this->assertEquals(
            'tR3+Ty81lMeYAr/Fid0kMTYa/WM=',
            OAuth1::signRequest(
                $this->getDefaultTestRequest(),
                'kd94hf93k423kf44',
                'pfkkdhi9sl3r4s00',
                'HMAC-SHA1'
            )
        );
    }

    /**
     * @covers OAuth1::verifyRequest
     */
    public function testVerifyingARequest(): void
    {
        $this->assertTrue(
            OAuth1::verifyRequest(
                $this->getDefaultTestRequest(['oauth_signature' => 'tR3+Ty81lMeYAr/Fid0kMTYa/WM=']),
                'kd94hf93k423kf44',
                'pfkkdhi9sl3r4s00'
            )
        );
    }

    /**
     * @covers OAuth1::verifyRequest
     * @covers OAuth1::extractParameters
     */
    public function testVerifyingARequestFromAuthorizationHeader(): void
    {
        $parameters = [
            ...$this->getDefaultParameters(),
            'oauth_signature' => 'tR3+Ty81lMeYAr/Fid0kMTYa/WM='
        ];


        $request = $this->getTestRequest()->withHeader(
            'Authorization',
            'OAuth ' . implode(',', array_map(
                fn($key, $value) => sprintf('%s="%s"', $key, $value),
                array_keys($parameters),
                array_values($parameters)
            ))
        );

        $this->assertTrue(
            OAuth1::verifyRequest(
                $request,
                'kd94hf93k423kf44',
                'pfkkdhi9sl3r4s00'
            )
        );
    }

    private function getTestRequest(): ServerRequestInterface
    {
        $factory = new Slim\Psr7\Factory\ServerRequestFactory();
        return $factory->createServerRequest(
            'GET',
            'http://photos.example.net/photos'
        )->withQueryParams([
            'size' => 'original',
            'file' => 'vacation.jpg',
        ]);
    }

    private function getDefaultTestRequest(array $parameters = []): ServerRequestInterface
    {
        $request = $this->getTestRequest();
        return $request->withQueryParams([
            ...$request->getQueryParams(),
            ...$this->getDefaultParameters(),
            ...$parameters,
        ]);
    }

    private function getDefaultParameters(): array
    {
        return [
            'oauth_consumer_key' => 'dpf43f3p2l4k3l03',
            'oauth_token' => 'nnch734d00sl2jdk',
            'oauth_nonce' => 'kllo9940pd9333jh',
            'oauth_timestamp' => '1191242096',
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_version' => '1.0',
        ];
    }
}
