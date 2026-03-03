<?php

use Trails\Dispatcher;
use Studip\Cache\Factory;
use Studip\Lti\LTI1p3\KeyManager;
use Studip\OAuth2\Bridge\ScopeEntity;
use Studip\OAuth2\NegotiatesWithPsr7;
use Studip\Lti\LTI1p3\PlatformManager;
use Studip\Lti\LTI1p3\RegistrationManager;
use OAT\Library\Lti1p3Core\Security\OAuth2\Repository\ScopeRepository;
use OAT\Library\Lti1p3Core\Security\OAuth2\Repository\ClientRepository;
use OAT\Library\Lti1p3Core\Security\OAuth2\Repository\AccessTokenRepository;
use OAT\Library\Lti1p3Core\Security\OAuth2\Factory\AuthorizationServerFactory;
use OAT\Library\Lti1p3Core\Security\Oidc\Server\OidcAuthenticationRequestHandler;
use OAT\Library\Lti1p3Core\Security\OAuth2\Generator\AccessTokenResponseGenerator;

final class Lti_1p3_TokenController extends AuthenticatedController
{
    protected $allow_nobody = true;
    protected $with_session = false;
    use NegotiatesWithPsr7;

    public function __construct(
        protected Dispatcher $dispatcher,
        protected OidcAuthenticationRequestHandler $oidcLoginHandler
    )
    {
        parent::__construct($dispatcher);
    }

    public function index_action(): void
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
