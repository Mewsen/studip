<?php
namespace Studip\Lti\LTI1p3;

use Keyring;
use OAT\Library\Lti1p3Core\Security\Key\KeyChainInterface;
use OAT\Library\Lti1p3Core\Security\Key\KeyChainRepositoryInterface;

final class KeyManager implements KeyChainRepositoryInterface
{
    public function find(string $identifier): ?KeyChainInterface
    {
        return Keyring::find($identifier)?->toKeyChain();
    }

    public function findByKeySetName(string $keySetName): array
    {
        $keyring = Keyring::findOneByRange_id($keySetName);
        if ($keyring) {
            return [$keyring->toKeyChain()];
        }

        return [];
    }
}
