<?php

use Lti\Publication;
use OAT\Library\Lti1p3Core\Exception\LtiException;
use OAT\Library\Lti1p3Core\Exception\LtiExceptionInterface;
use OAT\Library\Lti1p3Core\Message\Launch\Validator\Result\LaunchValidationResultInterface;
use Studip\Authentication\Manager as AuthenticationManager;
use Studip\Cache\Factory;
use Studip\LTI13a\PublicationValidator;
use Studip\LTI13a\ToolManager;
use Studip\LTI13a\RegistrationManager;
use OAT\Library\Lti1p3Core\Security\Oidc\OidcInitiator;
use OAT\Library\Lti1p3Core\Security\Nonce\NonceRepository;
use OAT\Library\Lti1p3Core\Security\Key\KeyChainRepository;
use OAT\Library\Lti1p3Core\Security\Jwks\Exporter\JwksExporter;
use OAT\Library\Lti1p3Core\Security\Jwks\Server\JwksRequestHandler;
use OAT\Library\Lti1p3Core\Message\Launch\Validator\Tool\ToolLaunchValidator;
use OAT\Library\Lti1p3Core\Security\Oidc\Server\OidcInitiationRequestHandler;
use Studip\LTI13a\UserEnrollment;
use Studip\OAuth2\NegotiatesWithPsr7;
use Trails\Dispatcher;

class Enrol_LtiController extends AuthenticatedController
{
    use NegotiatesWithPsr7;
    protected $allow_nobody = true;
    protected $with_session = false;

    public function __construct(Dispatcher $dispatcher)
    {
        $action = basename(get_route());
        if (in_array($action, ['launch'])) {
            $this->with_session = true;
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
        try {
            $validator = new ToolLaunchValidator(
                new RegistrationManager(),
                new NonceRepository(Factory::getCache())
            );

            $request = $validator->validatePlatformOriginatingLaunch($this->getPsrRequest());

            if ($request->hasError()) {
                throw new LtiException($request->getError());
            }

            $publication = $this->getPublication($request);

            (new PublicationValidator($publication))->validateLaunch();

            $user = (new UserEnrollment($request, $publication))->enroll();

            auth()->setAuthenticatedUser($user);
            Metrics::increment('core.login.succeeded');
            sess()->regenerateId(AuthenticationManager::DEFAULT_KEPT_SESSION_VARIABLES);

            $this->redirect('course/overview?cid='.$publication->range->id);
        } catch (Throwable $exception) {
            $this->messages = [
                [
                    'type' => 'error',
                    'text' => $exception->getMessage()
                ]
            ];

            $this->set_layout($GLOBALS['template_factory']->open('lti/layout'));
        }
    }

    public function launch_deeplink_action(): void
    {
        dd('launch_deeplink_action: ', Request::getInstance());
    }

    /**
     * @throws LtiExceptionInterface
     */
    private function getPublication(LaunchValidationResultInterface $request): Publication
    {
        $customId = $request->getPayload()->getCustom()['id'] ?? null;
        if (!$customId) {
            throw new LtiException('Missing custom ID');
        }

        $publication = Publication::findOneBySQL("publication_key = ?", [$customId]);
        if (!$publication) {
            throw new LtiException('Invalid custom ID');
        }

        return $publication;
    }

    private function showMessages(array $messages): void
    {
        $this->messages = $messages;
        $this->set_layout($GLOBALS['template_factory']->open('lti/layout'));
    }
}
