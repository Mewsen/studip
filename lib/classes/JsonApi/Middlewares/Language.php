<?php
namespace JsonApi\Middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * This class defines a middleware that tries to set the language for Stud.IP
 * by analyzing the HTTP header "Accept-Language".
 *
 * @author Jan-Hendrik Willms <tleilax+studip@gmail.com>
 */
final class Language
{
    public function __invoke(Request $request, ResponseInterface $response, callable $next)
    {
        $language = $_SESSION['_language'] ?? get_accepted_languages($request);

        init_i18n($language);
        $_SESSION['_language'] = $language;

        return $next($request, $response);
    }
}
