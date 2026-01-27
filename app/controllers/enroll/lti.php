<?php

use Lti\Publication;
use Lti\PublicationUser;
use Ramsey\Uuid\Uuid;
use Trails\Dispatcher;
use Studip\Cache\Factory;
use Studip\LTI13a\RoleMapper;
use Studip\LTI13a\ToolManager;
use Studip\LTI13a\UserEnrollment;
use Lti\Enum\UserProvisioningMode;
use Studip\OAuth2\NegotiatesWithPsr7;
use Studip\LTI13a\PublicationValidator;
use Studip\LTI13a\RegistrationManager;
use OAT\Library\Lti1p3Core\Exception\LtiException;
use OAT\Library\Lti1p3Core\Security\Oidc\OidcInitiator;
use OAT\Library\Lti1p3Core\Security\Nonce\NonceRepository;
use OAT\Library\Lti1p3Core\Security\Key\KeyChainRepository;
use OAT\Library\Lti1p3Core\Exception\LtiExceptionInterface;
use OAT\Library\Lti1p3Core\Security\Jwks\Exporter\JwksExporter;
use OAT\Library\Lti1p3Core\Security\Jwks\Server\JwksRequestHandler;
use OAT\Library\Lti1p3Core\Message\Payload\LtiMessagePayloadInterface;
use OAT\Library\Lti1p3Core\Message\Launch\Validator\Tool\ToolLaunchValidator;
use OAT\Library\Lti1p3Core\Security\Oidc\Server\OidcInitiationRequestHandler;
use OAT\Library\Lti1p3Core\Message\Launch\Validator\Result\LaunchValidationResultInterface;

class Enroll_LtiController extends AuthenticatedController
{
    use NegotiatesWithPsr7;
    protected $allow_nobody = true;
    protected $with_session = true;

    public function __construct(Dispatcher $dispatcher)
    {
        $action = basename(get_route());
        if (in_array($action, ['jwks', 'auth_init'])) {
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

    /**
     * @throws LtiExceptionInterface
     */
    public function launch_action(): void
    {
        $validator = new ToolLaunchValidator(
            new RegistrationManager(),
            new NonceRepository(Factory::getCache())
        );

        $request = $validator->validatePlatformOriginatingLaunch($this->getPsrRequest());

        if ($request->hasError()) {
            throw new LtiException($request->getError());
        }

        $publication = $this->getPublication($request->getPayload());

        if($publication === null) {
            throw new LtiException('Missing or invalid custom ID');
        }

        (new PublicationValidator($publication))->validateLaunch();

        $this->resolveProvisioningMode($request, $publication);
    }


    public function launch_deeplink_action(): void
    {

    }

    public function provisioning_modes_action(): void
    {
        PageLayout::setTitle(_('Bereitstellungsmodus'));

        $this->callbackId = Request::get('callback_id');

        if (empty($_SESSION['callbacks'][$this->callbackId])) {
            throw new AccessDeniedException('Missing or invalid callback ID');
        }

        $callbackData = $_SESSION['callbacks'][$this->callbackId];
        if (
            $callbackData['context'] !== 'lti'
            || !isset($callbackData['provisioning_mode'])
            || $callbackData['expires_at'] < time()
        ) {
            throw new AccessDeniedException('Invalid or expired callback data');
        }

        $this->provisioningMode = (int) $callbackData['provisioning_mode'];

        PageLayout::disableSidebar();
        PageLayout::setBodyElementId('lti');
    }

    public function create_new_account_action(): void
    {
        CSRFProtection::verifyUnsafeRequest();

        $callbackId = Request::get('callback_id');
        if (empty($_SESSION['callbacks'][$callbackId])) {
            throw new AccessDeniedException('Missing or invalid callback ID');
        }

        $callbackData = $_SESSION['callbacks'][$callbackId];
        if (
            $callbackData['context'] !== 'lti'
            || $callbackData['action'] !== 'enroll_user'
            || $callbackData['expires_at'] < time()
        ) {
            throw new AccessDeniedException('Invalid or expired callback data');
        }

        $publication = Publication::find($callbackData['publication_id']);
        $userEnrollment = new UserEnrollment($publication, $callbackData['local_roles'], $callbackData['registration_id']);
        $userEnrollment
            ->enroll($callbackData['user_identity'])
            ->authenticate();

        unset($_SESSION['callbacks'][$callbackId]);

        $this->redirect('course/overview?cid='.$publication->range->id);
    }

    /**
     * @throws LtiExceptionInterface
     */
    private function resolveProvisioningMode(LaunchValidationResultInterface $request, Publication $publication): void
    {
        $authUser = User::findCurrent();
        if ($authUser) {
            $this->redirect('course/overview?cid='.$publication->range->id);
            return;
        }

        $payload = $request->getPayload();
        $localRoles = RoleMapper::toLocal($payload->getRoles());
        $userEnrollment = new UserEnrollment($publication, $localRoles, $request->getRegistration()->getIdentifier());

        $publicationUser = PublicationUser::findOneBySQL(
            "external_email = :external_email AND external_user_id = :external_user_id AND registration_id = :registration_id",
            [
                'external_email' => $payload->getUserIdentity()->getEmail(),
                'external_user_id' => $payload->getUserIdentity()->getIdentifier(),
                'registration_id' => $request->getRegistration()->getIdentifier()
            ]
        );

        if ($publicationUser) {
            $userEnrollment
                ->setUserIdentity($payload->getUserIdentity())
                ->syncUser($publicationUser->user)
                ->syncRangeMember()
                ->authenticate();

            $this->redirect('course/overview?cid='.$publication->range->id);
            return;
        }

        // First launch:
        $publicationConfigs = $publication->getConfigValues();
        $provisioningMode = match ($localRoles['course'] ?? null) {
            'dozent' => (int) $publicationConfigs['provisioning_mode_instructor'],
            'autor' => (int) $publicationConfigs['provisioning_mode_student'],
            default => throw new LtiException('Unsupported LTI role.')
        };

        if ($provisioningMode === UserProvisioningMode::NewAccountsOnly->value) {
            $userEnrollment
                ->enroll($payload->getUserIdentity())
                ->authenticate();

            $this->redirect('course/overview?cid='.$publication->range->id);
            return;
        }

        $callbackId = Uuid::uuid4()->toString();
        $_SESSION['callbacks'][$callbackId] = [
            'user_identity' => $payload->getUserIdentity(),
            'registration_id' => $request->getRegistration()->getIdentifier(),
            'publication_id' => $publication->id,
            'local_roles' => $localRoles,
            'provisioning_mode' => $provisioningMode,
            'context' => 'lti',
            'action' => 'enroll_user',
            'expires_at' => time() + 1800
        ];
        $_SESSION['redirect_after_login'] = URLHelper::getLink('dispatch.php/course/overview?cid='.$publication->range->id);

        $this->redirect('enroll/lti/provisioning_modes?callback_id=' . $callbackId);
    }


    private function getPublication(LtiMessagePayloadInterface $payload): ?Publication
    {
        if (empty($payload->getCustom()['id'])) {
            return null;
        }

        return Publication::findOneBySQL("publication_key = ?", [$payload->getCustom()['id']]);
    }
}
