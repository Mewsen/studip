<?php

namespace Trails;

/**
 * @author        mlunzena
 * @copyright (c) Authors
 * @version       $Id: trails.php 7001 2008-04-04 11:20:27Z mlunzena $
 */
class Exception extends \Exception
{
    protected array $headers;

    /**
     * @param int         $status  the status code to be set in the response
     * @param string|null $reason  a human readable presentation of the status code
     * @param array       $headers a hash of additional headers to be set in the response
     */
    public function __construct(int $status = 500, string $reason = null, array $headers = [])
    {
        parent::__construct(
            $reason ?? Response::get_reason($status),
            $status
        );

        $this->setHeaders($headers);
    }

    public function setHeaders(array $headers): void
    {
        $this->headers = $headers;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function __toString(): string
    {
        return "{$this->code} {$this->message}";
    }
}
