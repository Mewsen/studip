<?php

use Studip\Cache\Factory;
use Studip\Lti\LTI1p3\KeyManager;
use Studip\OAuth2\Bridge\ScopeEntity;
use Studip\OAuth2\NegotiatesWithPsr7;
use Studip\Lti\LTI1p3\PlatformManager;
use Studip\Lti\LTI1p3\UserAuthenticator;
use Studip\Lti\LTI1p3\RegistrationManager;
use OAT\Library\Lti1p3Core\Security\Key\KeyChainRepository;
use OAT\Library\Lti1p3Core\Security\Oidc\OidcAuthenticator;
use OAT\Library\Lti1p3Core\Security\Jwks\Exporter\JwksExporter;
use OAT\Library\Lti1p3Core\Security\Jwks\Server\JwksRequestHandler;
use OAT\Library\Lti1p3Core\Security\OAuth2\Repository\ScopeRepository;
use OAT\Library\Lti1p3Core\Security\OAuth2\Repository\ClientRepository;
use OAT\Library\Lti1p3Core\Security\OAuth2\Repository\AccessTokenRepository;
use OAT\Library\Lti1p3Core\Security\OAuth2\Factory\AuthorizationServerFactory;
use OAT\Library\Lti1p3Core\Security\OAuth2\Generator\AccessTokenResponseGenerator;
use OAT\Library\Lti1p3Core\Security\Oidc\Server\OidcAuthenticationRequestHandler;

class Lti_1p3_AuthController extends AuthenticatedController
{
    protected $allow_nobody = true;
    protected $with_session = false;
    use NegotiatesWithPsr7;

    public function login_action(): void
    {
        $oidcLoginHandler = new OidcAuthenticationRequestHandler(
            new OidcAuthenticator(
                new RegistrationManager(),
                new UserAuthenticator()
            )
        );

        $response = $oidcLoginHandler->handle($this->getPsrRequest());
        $this->renderPsrResponse($response);
    }

    public function jwks_action(): void
    {
        $keyChainRepo = new KeyChainRepository();
        $platformKeyring = PlatformManager::getKeyring();

        $keyChainRepo->addKeyChain($platformKeyring->toKeyChain());
        $handler = new JwksRequestHandler(new JwksExporter($keyChainRepo));
        $this->renderPsrResponse($handler->handle($platformKeyring->range_id));
    }

    public function token_action(): void
    {
        $platformEncryptionKey = PlatformManager::getPrivateKey()->getContent();
        $responseGenerator = new AccessTokenResponseGenerator(
            new KeyManager(),
            new AuthorizationServerFactory(
                new ClientRepository(new RegistrationManager()),
                new AccessTokenRepository(Factory::getCache()),
                new ScopeRepository(
                    [
                        new ScopeEntity('https://purl.imsglobal.org/spec/lti-ags/scope/lineitem'),
                        new ScopeEntity('https://purl.imsglobal.org/spec/lti-ags/scope/result.readonly'),
                        new ScopeEntity('https://purl.imsglobal.org/spec/lti-ags/scope/score')
                    ]
                ),
                $platformEncryptionKey
            )
        );

        $response = $responseGenerator->generate(
            $this->getPsrRequest(),
            $this->getPsrResponse(),
            '1'
        );

        $this->renderPsrResponse($response);
    }
}
