<?php
/**
 * Keyring.php
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Moritz Strohm <strohm@data-quest.de>
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @since       6.0
 */

/**
 * The Keyring class stores cryptographic keyrings in the database.
 *
 * @property string id database column
 * @property string range_id database column
 * @property string range_type database column
 * @property string public_key database column
 * @property string private_key database column
 * @property string passphrase database column
 * @property string mkdate database column
 * @property string chdate database column
 */
class Keyring extends SimpleORMap
{
    private const ALGORTIHM_RS256 = 'RSA-OAEP-256';
    protected static function configure($config = [])
    {
        $config['db_table'] = 'keyrings';
        parent::configure($config);
    }

    /**
     * This method generates a new keyring.
     *
     * @param string $range_id The ID of the range for which to generate the keyring.
     *
     * @param string $range_type The type of range for which to generate the keyring.
     *
     * @param string $passphrase An optional passphrase for the keyring. This should not be stored
     *     as plain text in here, but instead in a cryptographically hashed form.
     *
     * @param string $algorithm The algorithm to use for the new keyring.
     *
     * @return Keyring The generated keyring.
     *
     * @throws \Studip\KeyringException In case no keyring can be generated, a KeyringException is thrown.
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
            $keyring = new self();
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
            throw new \Studip\KeyringException(
                _('Es konnte kein Schlüsselpaar erzeugt werden.'),
                \Studip\KeyringException::CREATION_FAILED
            );
        } else {
            throw new \Studip\KeyringException(
                sprintf(
                    _('Der Schlüsselalgorithmus %s wird nicht unterstützt.'),
                    $algorithm
                ),
                \Studip\KeyringException::UNSUPPORTED_KEY_ALGORITHM
            );
        }
    }

    /**
     * Generates a new keyring from a public key.
     * This method will not attempt to re-create the private key from the public key.
     * Instead, the public key is the only part of the key that is stored in a new
     * keyring instance.
     *
     * @param \OAT\Library\Lti1p3Core\Security\Key\KeyInterface|string $key The public key.
     *
     * @param string $range_type The range type for the keyring.
     *
     * @param string $range_id The range-ID for the keyring.
     *
     * @return Keyring|null A keyring, if it can be created or null in case of failure to do so.
     */
    public static function createFromPublicKey(
        \OAT\Library\Lti1p3Core\Security\Key\KeyInterface|string $key,
        string $range_type,
        string $range_id
    ) : ?Keyring
    {
        $keyring = new self();
        $keyring->range_type = $range_type;
        $keyring->range_id = $range_id;
        $keyring->private_key = '';

        if (is_string($key)) {
            $keyring->public_key = $key;
        } else {
            //Instance of KeyInterface:
            $content = $key->getContent();
            if (!$content || empty($content['n']) || empty($content['e'])) {
                //No key present or base or exponent missing.
                return null;
            }

            $loaded_key = \phpseclib3\Crypt\RSA::loadPublicKey([
                'e' => new \phpseclib3\Math\BigInteger(base64_decode(strtr($content['e'], '-_', '+/'))),
                'n' => new \phpseclib3\Math\BigInteger(base64_decode(strtr($content['n'], '-_', '+/'))),
            ]);
            $keyring->public_key = $loaded_key->toString('PKCS8');
        }
        return $keyring;
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
        $private_key = null;
        if ($this->private_key) {
            $private_key = new \OAT\Library\Lti1p3Core\Security\Key\Key(
                $this->private_key,
                $this->passphrase ?? null
            );
        }

        return new \OAT\Library\Lti1p3Core\Security\Key\KeyChain(
            $this->id,
            $this->range_id,
            $public_key,
            $private_key
        );
    }
}
