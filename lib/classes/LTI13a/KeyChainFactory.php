<?php

namespace Studip\LTI13a;

use OAT\Library\Lti1p3Core\Security\Key\KeyChain;
use OAT\Library\Lti1p3Core\Security\Key\KeyChainFactoryInterface;
use OAT\Library\Lti1p3Core\Security\Key\KeyChainInterface;
use OAT\Library\Lti1p3Core\Security\Key\KeyChainRepositoryInterface;
use OAT\Library\Lti1p3Core\Security\Key\KeyInterface;

class KeyChainFactory implements KeyChainFactoryInterface
{
    /**
     * @param string $identifier THe ID of the keychain.
     *
     * @param string $keySetName The name of the keychain.
     *
     * @param mixed $publicKey The public key for the keychain.
     *
     * @param mixed $privateKey The private key for the keychain.
     *
     * @param string|null $privateKeyPassPhrase The passphrase for the private key.
     *
     * @param string $algorithm The algorithm to use.
     *
     * @return KeyChainInterface The generated KeyChainInterface instance.
     *
     * @throws \Studip\KeyringException In case no keychain can be generated.
     */
    public function create(
        string $identifier,
        string $keySetName,
        $publicKey,
        $privateKey = null,
        ?string $privateKeyPassPhrase = null,
        string $algorithm = KeyInterface::ALG_RS256
    ) : KeyChainInterface
    {
        $keyring = null;
        if (!$publicKey && !$privateKey) {
            $keyring = \Keyring::generate($identifier, 'global', $privateKeyPassPhrase, $algorithm);
        } else {
            $keyring = \Keyring::findOneBySQL('range_id = :id', ['id' => $identifier]);
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
