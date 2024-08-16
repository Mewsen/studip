<?php
use League\OAuth2\Client\Provider\GenericProvider;

/**
 * StudipAuthOAuth2.php - Stud.IP authentication using OAuth2
 *
 * @copyright 2024 Jan-Hendrik Willms <tleilax@gmail.com>
 * @license GPL2 or any later version
 * @since Stud.IP 6.0
 */
final class StudipAuthOAuth2 extends StudipAuthSSO
{
    protected string $client_id;
    protected string $client_secret;
    protected string $redirect_uri;

    protected string $url_authorize;
    protected string $url_access_token;
    protected string $url_resource_owner_details;

    private GenericProvider $oauth2_provider;

    private ?array $user_data = null;

    public function __construct($config = [])
    {
        parent::__construct($config);

        if (!isset($this->plugin_fullname)) {
            $this->plugin_fullname = _('OAuth2');
        }

        if (Request::option('sso') === $this->plugin_name) {
            $options = [
                'clientId' => $this->client_id,
                'clientSecret' => $this->client_secret,
                'redirectUri' => $this->redirect_uri,
                'urlAuthorize' => $this->url_authorize,
                'urlAccessToken' => $this->url_access_token,
                'urlResourceOwnerDetails' => $this->url_resource_owner_details,
            ];

            if (Config::get()->getValue('HTTP_PROXY')) {
                $options['proxy'] = Config::get()->getValue('HTTP_PROXY');
                $options['verify'] = false;
            }

            $this->oauth2_provider = new GenericProvider($options);
        }
    }

    public function getUser()
    {
        return $this->getUserData($this->getUsernameKey());
    }

    public function verifyUsername($username)
    {
        if (isset($this->user_data)) {
            return parent::verifyUsername($this->getUser());
        }

        if (!Request::get('code')) {
            $authorizationUrl = $this->oauth2_provider->getAuthorizationUrl(['scope' => 'profile email']);

            $_SESSION[self::class] = [
                'state' => $this->oauth2_provider->getState(),
                'redirect' => Request::url(),
            ];

            page_close();
            header('Location: ' . $authorizationUrl);
            die;
        } elseif (
            !Request::get('state')
            || empty($_SESSION[self::class]['state'])
            || Request::get('state') !== $_SESSION[self::class]['state']
        ) {
            if (isset($_SESSION[self::class])) {
                unset($_SESSION[self::class]);
            }
        } else {
            $accessToken = $this->oauth2_provider->getAccessToken('authorization_code', [
                'code' => Request::get('code'),
            ]);

            $resourceOwner = $this->oauth2_provider->getResourceOwner($accessToken);

            $this->user_data = $resourceOwner->toArray();

            return parent::verifyUsername($this->getUser());
        }

        return null;
    }

    /**
     * Callback that can be used in user_data_mapping array.
     */
    public function getUserData(string $key): ?string
    {
        return $this->user_data[$key];
    }

    /**
     * Returns the key used to store the username from user_data_mapping if
     * present. Defaults to 'nickname'.
     */
    private function getUsernameKey(): string
    {
        return $this->user_data_mapping['map_args']['auth_user_md5.username'] ?? 'nickname';
    }
}
