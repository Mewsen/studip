<?php

namespace Studip\LTI13a;

use OAT\Library\Lti1p3Core\Security\Key\KeyChain;
use OAT\Library\Lti1p3Core\Security\Key\KeyChainFactoryInterface;
use OAT\Library\Lti1p3Core\Security\Key\KeyChainInterface;
use OAT\Library\Lti1p3Core\Security\Key\KeyChainRepositoryInterface;
use OAT\Library\Lti1p3Core\Security\Key\KeyInterface;

class KeyChainFactory implements KeyChainFactoryInterface
{
    public function create(string $identifier, string $keySetName, $publicKey, $privateKey = null, ?string $privateKeyPassPhrase = null, string $algorithm = KeyInterface::ALG_RS256): KeyChainInterface
    {
        $keyring = null;
        if (!$publicKey && !$privateKey) {
            try {
                $keyring = \Keyring::generate($identifier, 'global', $privateKeyPassPhrase, $algorithm);
            } catch (\StudipException $e) {
                //TODO
            }
        } else {
            $keyring = \Keyring::findOneBySQL('range_id = :id', ['id' => $identifier]);
            if ($keyring) {
                return $keyring->toKeyChain();
            }
        }
        throw new \StudipException('Unable to create a keyring.');
    }
}
