<?php

namespace Studip\LTI13a;

use Keyring;
use OAT\Library\Lti1p3Core\Security\Key\KeyChainFactoryInterface;
use OAT\Library\Lti1p3Core\Security\Key\KeyChainInterface;
use OAT\Library\Lti1p3Core\Security\Key\KeyInterface;

class KeyChainFactory implements KeyChainFactoryInterface
{
    /**
     * @throws \Studip\KeyringException In case no keychain can be generated.
     */
    public function create(
        string $identifier,
        string $keySetName,
        $publicKey,
        $privateKey = null,
        ?string $privateKeyPassPhrase = null,
        string $algorithm = KeyInterface::ALG_RS256
    ): KeyChainInterface
    {
        $keyring = null;
        if (!$publicKey && !$privateKey) {
            $keyring = Keyring::generate($identifier, 'global', $privateKeyPassPhrase, $algorithm);
        } else {
            $keyring = Keyring::findOneBySQL('range_id = :id', ['id' => $identifier]);
            if ($keyring) {
                return $keyring->toKeyChain();
            } else {
                throw new \Studip\KeyringException(
                    'Keyring not found.',
                    \Studip\KeyringException::NOT_FOUND
                );

            }
        }
        return $keyring;
    }
}
