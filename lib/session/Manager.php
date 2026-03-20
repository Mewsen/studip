<?php
/**
 * Session manager for Stud.IP
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      André Noack <noack@data-quest.de>
 */
namespace Studip\Session;

class Manager
{
    public const STATE_UNKNOWN = false;
    public const STATE_AUTHENTICATED = 'authenticated';
    public const STATE_NOBODY = 'nobody';

    protected array $options = [
        'name' => 'Studip_Session',
        'lifetime' => 7200,
        'path' => null,
        'domain' => null,
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Lax',
        'cache_limiter' => 'nocache'
    ];

    protected string|false|null $current_session_state = null;

    public function __construct(
        protected \SessionHandlerInterface $handler,
        array $session_options = []
    ) {
        $keys = array_keys($this->options);
        foreach ($keys as $key) {
            if (array_key_exists($key, $session_options)) {
                $this->options[$key] = $session_options[$key];
                if ($key === 'path') {
                    $this->options[$key]  = implode('/', array_map('rawurlencode', explode('/', $this->options[$key] )));
                }
            }
        }
    }

    /**
     * @return void
     */
    public function start(): void
    {
        if (!$this->isStarted()) {
            ini_set('session.use_strict_mode', 1);

            session_set_cookie_params([
                'lifetime' => 0,
                'path'     => $this->getCookieParam('path'),
                'domain'   => $this->getCookieParam('domain'),
                'secure'   => (bool) $this->getCookieParam('secure', false),
                'samesite' => $this->getCookieParam('samesite'),
                'httponly' => (bool) $this->getCookieParam('httponly', false),
            ]);

            session_name($this->options['name']);
            session_cache_limiter('nocache');
            session_set_save_handler($this->handler, true);

            session_start([
                'gc_maxlifetime' => (int) $this->getCookieParam('lifetime'),
            ]);
        }
    }

    public function isStarted(): bool
    {
        return session_status() === PHP_SESSION_ACTIVE;
    }

    public function regenerateId(array $keep_session_vars = []): void
    {
        if (!$this->isStarted()) {
            return;
        }

        $keep = [];
        if (is_array($_SESSION)) {
            foreach (array_keys($_SESSION) as $k) {
                if (in_array($k, $keep_session_vars)) {
                    $keep[$k] = $_SESSION[$k];
                }
            }
            $_SESSION = [];
        }
//        session_regenerate_id(true);

        foreach ($keep_session_vars as $k) {
            $_SESSION[$k] = $keep[$k] ?? null;
        }
    }

    public function getName(): string
    {
        return $this->options['name'];
    }

    /**
     * Returns the value for the given cookie parameter. The value is taken
     * from the configured options array (or from the current session
     * configuration in php).
     *
     * If no value is found, null is retuned.
     */
    public function getCookieParam(string $key, bool $from_config = true): mixed
    {
        $value = $this->options[$key] ?? null;

        if ($from_config) {
            $current = session_get_cookie_params();
            $value = $value ?: $current[$key] ?? null;
        }

        return $value;
    }

    public function destroy(): void
    {
        if (!$this->isStarted()) {
            return;
        }

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                $this->getName(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        $_COOKIE[$this->getName()] = '';
        session_unset();
        session_destroy();
    }

    public function save() : void
    {
        session_write_close();
    }

    /**
     * Returns true, if the current session is valid and belongs to an
     * authenticated user. Does not start a session.
     */
    public function isCurrentSessionAuthenticated(): bool
    {
        return $this->getCurrentSessionState() === self::STATE_AUTHENTICATED;
    }

    /**
     * Returns the state of the current session. Does not start a session.
     * possible return values:
     * 'authenticated' - session is valid and user is authenticated
     * 'nobody' - session is valid, but user is not authenticated
     * false - no valid session
     */
    public function getCurrentSessionState(): false|string|null
    {

        if ($this->current_session_state !== null) {
            return $this->current_session_state;
        }
        $state = self::STATE_UNKNOWN;
        if (isset($GLOBALS['user']) && is_object($GLOBALS['user'])) {
            $state = $GLOBALS['user']->id === 'nobody' ? self::STATE_NOBODY : self::STATE_AUTHENTICATED;
        } else {
            $sid = $this->getSessionIdFromCookie();
            if ($sid) {
                $session_vars = $this->getSessionVars($sid);
                $session_auth = $session_vars['auth'];
                if ($session_auth['uid'] && $session_auth['uid'] !== 'nobody') {
                    $state = self::STATE_AUTHENTICATED;
                } else {
                    $state = $session_auth['uid'] === 'nobody' ? self::STATE_NOBODY : self::STATE_UNKNOWN;
                }
            }
        }
        return ($this->current_session_state = $state);
    }

    /**
     * returns a SessionDecoder object containing the session variables
     * for the given session id
     */
    public function getSessionVars(string $sid): \SessionDecoder
    {
        $data = $this->handler->read($sid);
        return new \SessionDecoder($data);
    }

    public function getSessionIdFromCookie(): string
    {
        return $_COOKIE[$this->getName()] ?? '';
    }

    /**
     * force garbage collect
     *
     * @return void
     */
    public function doGarbageCollect(): void
    {
        $this->handler->gc($this->options['lifetime']);
    }
}
