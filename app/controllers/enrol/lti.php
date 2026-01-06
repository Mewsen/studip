<?php

use Studip\Cache\Factory;
use Studip\Cache\MemoryCache;
use Studip\LTI13a\ToolManager;
use Studip\LTI13a\RegistrationManager;
use OAT\Library\Lti1p3Core\Security\Oidc\OidcInitiator;
use OAT\Library\Lti1p3Core\Security\Nonce\NonceRepository;
use OAT\Library\Lti1p3Core\Security\Key\KeyChainRepository;
use OAT\Library\Lti1p3Core\Security\Jwks\Exporter\JwksExporter;
use OAT\Library\Lti1p3Core\Security\Jwks\Server\JwksRequestHandler;
use OAT\Library\Lti1p3Core\Message\Launch\Validator\Tool\ToolLaunchValidator;
use OAT\Library\Lti1p3Core\Security\Oidc\Server\OidcInitiationRequestHandler;
use Studip\OAuth2\NegotiatesWithPsr7;
use Trails\Dispatcher;

class Enrol_LtiController extends StudipController
{
    use NegotiatesWithPsr7;

    public function __construct(Dispatcher $dispatcher)
    {
        $action = basename(get_route());
        if ($action === 'jwks') {
            $this->allow_nobody = true;
            $this->with_session = false;
        }

        parent::__construct($dispatcher);
    }

    public function jwks_action(): void
    {
        $keyChainRepo = new KeyChainRepository();
        $toolKeyring = ToolManager::getKeyring();

        $keyChainRepo->addKeyChain($toolKeyring->toKeyChain());
        $handler = new JwksRequestHandler(new JwksExporter($keyChainRepo));
        $this->renderPsrResponse($handler->handle($toolKeyring->range_id));
    }

    public function auth_init_action(): void
    {
        $oidcInitHandler = new OidcInitiationRequestHandler(
            new OidcInitiator(
                new RegistrationManager()
            )
        );

        $response = $oidcInitHandler->handle($this->getPsrRequest());
        $this->renderPsrResponse($response);
    }

    public function launch_action(): void
    {
        $validator = new ToolLaunchValidator(
            new RegistrationManager(),
            new NonceRepository(Factory::getCache())
        );

        $result = $validator->validatePlatformOriginatingLaunch($this->getPsrRequest());
        if ($result->hasError()) {
            dd($result);
        }

        $result2 = [
            'registration' => [
                'identifier' => $result->getRegistration()->getIdentifier(),
            ],
            'payload' => [
                'version' => $result->getPayload()->getVersion(),
                'context' => [
                    'identifier' => $result->getPayload()->getContext()->getIdentifier(),
                ],
                'userIdentity' => $result->getPayload()->getUserIdentity(),
            ],
            'state' => [
                'token' => $result->getState()->getToken()->toString(),
                'claims' => [
                    'jti' => $result->getState()->getToken()->getClaims()->get('jti'),
                ],
            ],
            'custom' => $result->getPayload()->getCustom(),
            'successes' => [],
        ];

        foreach ($result->getSuccesses() as $success) {
            $result2['successes'][] = $success;
        }

        dd($result2);
    }

    public function launch_deeplink_action(): void
    {
        dd('launch_deeplink_action: ', Request::getInstance());
    }
}
