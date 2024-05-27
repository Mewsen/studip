<?php
namespace Studip;

use Psr\Http\Message\ServerRequestInterface as Request;
use RuntimeException;

/**
 * Basic oauth1 request handling for Stud.IP using PSR-7 http messages.
 *
 * @author Jan-Hendrik Willms <tleilax+studip@gmail.com>
 * @license GPL2 or any later version
 * @since Stud.IP 6.0
 */
final class OAuth1
{
    /**
     * Signs a given request.
     *
     * @throws RuntimeException if a request for any other oauth version then
     *                          1.0 shall be signed
     */
    public static function signRequest(
        Request $request,
        string  $consumerSecret,
        string  $tokenSecret,
        string  $method
    ): string {
        if (
            isset($request->getQueryParams()['oauth_version'])
            && $request->getQueryParams()['oauth_version'] !== '1.0'
        ) {
            throw new RuntimeException(self::class . ' only supports OAuth 1.0 requests');
        }

        return self::hash(
            $method,
            self::getSignatureBaseString($request),
            self::urlencode($consumerSecret) . '&' . self::urlencode($tokenSecret)
        );
    }

    /**
     * Verifies an oauth request.
     *
     * @throws RuntimeException if any necessary oauth parameter is missing
     */
    public static function verifyRequest(
        Request $request,
        string  $consumerSecret,
        string  $tokenSecret
    ): bool {
        $parameters = self::extractParameters($request);

        $required = [
            'oauth_consumer_key',
            'oauth_nonce',
            'oauth_signature',
            'oauth_signature_method',
            'oauth_timestamp',
        ];

        $missing = array_diff($required, array_keys($parameters));
        if (count($missing) > 0) {
            throw new RuntimeException('Missing oauth parameters ' . implode(', ', $missing));
        }

        $signatureToVerify = $parameters['oauth_signature'];
        unset($parameters['oauth_signature']);

        $signature = self::signRequest(
            $request->withQueryParams($parameters),
            $consumerSecret,
            $tokenSecret,
            $parameters['oauth_signature_method']
        );

        return $signature === $signatureToVerify;
    }

    /**
     * Extracts the oauth parameters either from the Authorization header or
     * from the query string.
     */
    public static function extractParameters(Request $request): array
    {
        $parameters = $request->getQueryParams();

        $header = $request->getHeaderLine('Authorization');
        if ($header && str_starts_with($header, 'OAuth ')) {
            $temp = substr($header, 6);
            $chunks = explode(',', $temp);

            foreach ($chunks as $chunk) {
                [$key, $value] = explode('=', $chunk, 2);
                $value = trim($value, '"');
                $parameters[$key] = self::urldecode($value);
            }
        }

        return $parameters;
    }

    /**
     * Creates the base string for the signature. It consists of:
     *
     * - The uppercase request method
     * - The request URL
     * - the sorted and urlencoded parameters of the request
     *
     * The urlencoded parts are concatenated together into a single string
     * separated by the '&' character.
     *
     *
     */
    public static function getSignatureBaseString(Request $request): string
    {
        $parameters = $request->getQueryParams();
        ksort($parameters);

        return implode('&', array_map(
            self::urlencode(...),
            [
                strtoupper($request->getMethod()),
                (string) $request->getUri()->withQuery(''),
                http_build_query($parameters, '', '&', PHP_QUERY_RFC3986),
            ]
        ));
    }

    /**
     * Hashes a given text with a given key by the given method.
     *
     * @throws RuntimeException if the given hash method is not supported
     */
    public static function hash(string $method, string $text, string $key): string
    {
        $method = strtolower($method);
        return match ($method) {
            'hmac-sha1',   'sha1'   => base64_encode(hash_hmac('sha1', $text, $key, true)),
            'hmac-sha256', 'sha256' => base64_encode(hash_hmac('sha256', $text, $key, true)),
            'hmac-sha512', 'sha512' => base64_encode(hash_hmac('sha512', $text, $key, true)),

            'plaintext' => $key,

            default => throw new RuntimeException('Unsupported sigature method "' . $method . '"'),
        };
    }

    /**
     * Urlencodes a given input
     */
    public static function urldecode(string $input): string
    {
        return rawurldecode($input);
    }

    /**
     * Urldecodes a given input
     */
    public static function urlencode(string $input): string
    {
        $encoded = rawurlencode($input);
        return str_starts_with($encoded, '/%7E')
            ? str_replace('/%7E', '/~', $encoded)
            : $encoded;
    }
}
