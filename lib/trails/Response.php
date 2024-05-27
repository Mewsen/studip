<?php
namespace Trails;

/**
 * This class represents a response returned by a controller that was asked to
 * perform for a given request. A Response contains the body, status and
 * additional headers which can be renderer back to the client.
 *
 * @package       trails
 *
 * @author        mlunzena
 * @copyright (c) Authors
 * @version       $Id: trails.php 7001 2008-04-04 11:20:27Z mlunzena $
 */
class Response
{
    public $body = '';
    public $status;
    public $reason;
    public $headers = [];

    /**
     * Constructor.
     *
     * @param string      $body    the body of the response defaulting to ''
     * @param array       $headers an array of additional headers defaulting to an
     *                             empty array
     * @param int|null    $status  the status code of the response defaulting to a
     *                             regular 200
     * @param string|null $reason  the descriptional reason for a status code defaulting to
     *                             the standard reason phrases defined in RFC 2616
     */
    public function __construct(
        string $body = '',
        array $headers = [],
        ?int $status = null,
        ?string $reason = null
    ) {
        $this->set_body($body);

        $this->headers = $headers;

        if (isset($status)) {
            $this->set_status($status, $reason);
        }
    }

    /**
     * Sets the body of the response.
     *
     * @param string $body the body
     * @return static   this response object. Useful for cascading method calls.
     */
    public function set_body($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * Sets the status code and an optional custom reason. If none is given, the
     * standard reason phrase as of RFC 2616 is used.
     *
     * @param integer $status the status code
     * @param string  $reason the custom reason, defaulting to the one given in RFC 2616
     * @return static    this response object. Useful for cascading method calls.
     */
    public function set_status($status, $reason = null)
    {
        $this->status = $status;
        $this->reason = $reason ?? self::get_reason($status);
        return $this;
    }

    /**
     * Returns the reason phrase of this response according to RFC2616.
     *
     * @param int $status the response's status
     * @return string  the reason phrase for this response's status
     */
    public static function get_reason($status)
    {
        return match ($status) {
            100 => 'Continue',
            101 => 'Switching Protocols',

            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',

            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => '(Unused)',
            307 => 'Temporary Redirect',

            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',

            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',

            default => '',
        };
    }

    /**
     * Adds an additional header to the response.
     *
     * @param string $key   the left hand key part
     * @param string $value the right hand value part
     * @return static   this response object. Useful for cascading method calls.
     */
    public function add_header($key, $value)
    {
        $this->headers[$key] = $value;
        return $this;
    }

    /**
     * Outputs this response to the client using "echo" and "header".
     * @return void
     */
    public function output()
    {
        if (isset($this->status)) {
            $this->send_header(
                "HTTP/1.1 {$this->status} {$this->reason}",
                true,
                $this->status
            );
        }

        foreach ($this->headers as $k => $v) {
            $this->send_header("{$k}: {$v}");
        }

        echo $this->body;
    }

    /**
     * Internally used function to actually send headers
     *
     * @param string  $header  the HTTP header
     * @param bool    $replace optional; TRUE if previously sent header should be
     *                    replaced - FALSE otherwise (default)
     * @param integer $status  optional; the HTTP response code
     * @return void
     */
    public function send_header($header, $replace = false, $status = null)
    {
        if (isset($status)) {
            header($header, $replace, $status);
        } else {
            header($header, $replace);
        }
    }
}
