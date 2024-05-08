<?php
final class CaptchaChallenge extends SimpleORMap
{
    public const ALGORITHM = 'SHA-256';
    public const CHALLENGE_EXPIRATION = 5 * 60;

    protected static function configure($config = [])
    {
        $config['db_table'] = 'captcha_challenges';

        parent::configure($config);
    }

    protected static function getKey(): string
    {
        $key = Config::get()->CAPTCHA_KEY;
        if ($key === '') {
            $key = bin2hex(random_bytes(32));
            Config::get()->store('CAPTCHA_KEY', $key);
        }
        return $key;
    }

    public static function createChallenge(string $salt, int $number): array
    {
        $algorithm = 'sha256';
        $challenge = hash($algorithm, $salt . $number);
        $signature = hash_hmac($algorithm, $challenge, self::getKey());

        return [
            'algorithm' => self::ALGORITHM,
            'challenge' => $challenge,
            'salt'      => $salt,
            'signature' => $signature,
        ];
    }

    public static function createNewChallenge(): array
    {
        do {
            $salt = time() . '-' . bin2hex(random_bytes(12));
            $number = random_int(1e3, 1e5);
        } while (self::countBySql('salt = ? AND number = ?', [$salt, $number]) > 0);

        return self::createChallenge($salt, $number);
    }

    public static function decodePayload(string $payload): array|null
    {
        return json_decode(base64_decode($payload), true);
    }

    public static function validatePayload(string $payload): string|bool
    {
        $json = self::decodePayload($payload);

        if ($json === null) {
            return _('Sie haben nicht bestätigt, dass Sie kein Roboter sind');
        }

        $time = explode('-', $json['salt'])[0];
        if ($time < time() - self::CHALLENGE_EXPIRATION) {
            return _('Die Challenge ist abgelaufen');
        }

        // Replay?
        if (\CaptchaChallenge::countBySql('salt = ? AND number = ?', [$json['salt'], $json['number']]) > 0) {
            return _('Nicht schummeln!');
        }

        $check = self::createChallenge($json['salt'], $json['number']);

        if (
            $json['algorithm'] !== $check['algorithm']
            || $json['challenge'] !== $check['challenge']
            || $json['signature'] !== $check['signature']
        ) {
            return _('Sie sind scheinbar ein Roboter...');
        }

        return true;
    }

    public static function gc(): void
    {
        self::deleteBySQL("mkdate < UNIX_TIMESTAMP() - ?", [self::CHALLENGE_EXPIRATION]);
    }
}
