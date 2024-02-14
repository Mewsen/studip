<?php

/**
 * TODO: doc
 */
class Keyring extends SimpleORMap
{
    const ALGORTIHM_RS256 = 'RSA-OAEP-256';
    protected static function configure($config = [])
    {
        $config['db_table'] = 'keyrings';
        parent::configure($config);
    }

    /**
     * TODO
     *
     * @param string $range_id
     *
     * @param string $range_type
     *
     * @param string $passphrase
     *
     * @param string $algorithm
     *
     * @return Keyring
     *
     * @throws StudipException
     */
    public static function generate(
        string $range_id,
        string $range_type,
        string $passphrase = '',
        string $algorithm = self::ALGORTIHM_RS256
    ) : Keyring
    {
        if ($algorithm === self::ALGORTIHM_RS256) {
            $private_key = phpseclib3\Crypt\RSA::createKey(4096);
            if ($passphrase) {
                $private_key = $private_key->withPassword($passphrase);
            }
            //Explicitly set OAEP as padding method:
            $private_key->withPadding(\phpseclib\Crypt\RSA::ENCRYPTION_OAEP);
            //Explicitly set sha256:
            $private_key->withHash('sha256');

            $public_key = $private_key->getPublicKey();
            $keyring = new Keyring();
            $keyring->range_id = $range_id;
            $keyring->range_type = $range_type;
            $keyring->private_key = $private_key;
            $keyring->public_key = $public_key;
            if ($passphrase) {
                $hasher = UserManagement::getPwdHasher();
                $keyring->passphrase = $hasher->HashPassword($passphrase);
            }
            if ($keyring->store()) {
                return $keyring;
            }
            //TODO: improve exception after the „bald-rapunzel“ StEP is merged.
            throw new StudipException(_('Es konnte kein Schlüsselpaar erzeugt werden.'));
        } else {
            //TODO: improve exception after the „bald-rapunzel“ StEP is merged.
            throw new StudipException(
                sprintf(
                    _('Der Schlüsselalgorithmus %s wird nicht unterstützt.'),
                    $algorithm
                )
            );
        }
    }

    /**
     * Converts the keyring to a KeyChain instance of the Lti1p3Core library.
     *
     * @return \OAT\Library\Lti1p3Core\Security\Key\KeyChain A KeyChain representation
     *     of the keyring.
     */
    public function toKeyChain() : \OAT\Library\Lti1p3Core\Security\Key\KeyChain
    {
        $public_key = new \OAT\Library\Lti1p3Core\Security\Key\Key(
            $this->public_key
        );

        //A private key is optional.
        if ($this->private_key) {
            $private_key = new \OAT\Library\Lti1p3Core\Security\Key\Key(
                $this->private_key,
                $this->passphrase ?? null
            );
        }

        $keychain = new \OAT\Library\Lti1p3Core\Security\Key\KeyChain(
            $this->id,
            'studip-key', //TODO: better name
            $public_key,
            $private_key
        );
        return $keychain;
    }
}
