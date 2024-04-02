<?php

namespace Studip\LTI13a;

use OAT\Library\Lti1p3Core\Security\Key\KeyChainInterface;
use OAT\Library\Lti1p3Core\Security\Key\KeyChainRepositoryInterface;

class KeyManager implements KeyChainRepositoryInterface
{

    #[\Override] public function find(string $identifier): ?KeyChainInterface
    {
        $keyring = \Keyring::findOneByRange_id($identifier);
        if ($keyring) {
            return $keyring->toKeyChain();
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    #[\Override] public function findByKeySetName(string $keySetName): array
    {
        $keyring = \Keyring::findOneByRange_id($keySetName);
        if ($keyring) {
            return [$keyring->toKeyChain()];
        }
        return [];
    }
}
