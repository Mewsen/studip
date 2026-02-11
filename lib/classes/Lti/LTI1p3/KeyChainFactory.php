<?php
namespace Studip\Lti\LTI1p3;

use Keyring;
use Studip\KeyringException;
use OAT\Library\Lti1p3Core\Security\Key\KeyInterface;
use OAT\Library\Lti1p3Core\Security\Key\KeyChainInterface;
use OAT\Library\Lti1p3Core\Security\Key\KeyChainFactoryInterface;

final class KeyChainFactory implements KeyChainFactoryInterface
{
    /**
     * @throws KeyringException In case no keychain can be generated.
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
        if (!$publicKey && !$privateKey) {
            return Keyring::generate(
                $identifier,
                'global',
                $privateKeyPassPhrase,
                $algorithm
            )->toKeyChain();
        }

        $keyring = Keyring::findOneBySQL('range_id = :id', ['id' => $identifier]);
        if (!$keyring) {
            throw new KeyringException(
                'Keyring not found.',
                KeyringException::NOT_FOUND
            );
        }

        return $keyring->toKeyChain();
    }
}
