<?php

namespace Studip\LTI13a;

use OAT\Library\Lti1p3Core\Security\Nonce\NonceGeneratorInterface;
use OAT\Library\Lti1p3Core\Security\Nonce\NonceInterface;
use OAT\Library\Lti1p3Core\Security\Nonce\Nonce;

class NonceGenerator implements NonceGeneratorInterface
{
    protected bool $pass_nonce_from_request = false;
    public function __construct(bool $pass_nonce_from_request = false)
    {
        $this->pass_nonce_from_request = $pass_nonce_from_request;
    }

    #[\Override] public function generate(?int $ttl = null): NonceInterface
    {
        $expiration = new \DateTime();
        $expiration = $expiration->add(new \DateInterval('PT5M'));
        if ($this->pass_nonce_from_request) {
            return new Nonce(
                \Request::get('nonce'),
                $expiration
            );
        } else {
            $nonce = md5(random_bytes(16) . 'lti13a_nonce');
            //TODO: save nonce.
            return new Nonce(
                $nonce,
                $expiration
            );
        }
    }
}
