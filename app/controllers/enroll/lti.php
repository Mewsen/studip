<?php

use Lti\Deployment;
use Lti\Publication;
use Trails\Dispatcher;
use Ramsey\Uuid\Uuid;
use Studip\Cache\Factory;
use Lti\UserIdentityMapping;
use Studip\Lti\LTI1p3\RoleMapper;
use Studip\Lti\LTI1p3\ToolManager;
use Studip\Lti\LTI1p3\UserManager;
use Studip\OAuth2\NegotiatesWithPsr7;
use Studip\Lti\Enum\UserProvisioningMode;
use Studip\Lti\LTI1p3\PublicationValidator;
use Studip\Lti\LTI1p3\RegistrationManager;
use Studip\Lti\Enum\UserIdentityMappingContext;
use OAT\Library\Lti1p3Core\Exception\LtiException;
use OAT\Library\Lti1p3Core\Resource\ResourceCollection;
use OAT\Library\Lti1p3Core\Security\Oidc\OidcInitiator;
use OAT\Library\Lti1p3Core\Security\Nonce\NonceRepository;
use OAT\Library\Lti1p3Core\Security\Key\KeyChainRepository;
use OAT\Library\Lti1p3Core\Security\Jwks\Exporter\JwksExporter;
use OAT\Library\Lti1p3Core\Security\Jwks\Server\JwksRequestHandler;
use OAT\Library\Lti1p3Core\Message\Payload\LtiMessagePayloadInterface;
use OAT\Library\Lti1p3Core\Message\Launch\Validator\Tool\ToolLaunchValidator;
use OAT\Library\Lti1p3Core\Security\Oidc\Server\OidcInitiationRequestHandler;
use OAT\Library\Lti1p3DeepLinking\Message\Launch\Builder\DeepLinkingLaunchResponseBuilder;
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

        if (in_array($action, ['select_contents', 'deeplink_callback', 'reset_account_mapping'])) {
            $this->allow_nobody = false;
        }

        parent::__construct($dispatcher);
    }

    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        if (!LtiToolModule::isToolSharingEnabled()) {
            throw new AccessDeniedException();
        }

        PageLayout::disableSidebar();
        PageLayout::setBodyElementId('lti');
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
        $validator = new ToolLaunchValidator(
            new RegistrationManager(),
            new NonceRepository(Factory::getCache())
        );

        $request = $validator->validatePlatformOriginatingLaunch($this->getPsrRequest());

        if ($request->hasError()) {
            throw new LtiException($request->getError());
        }

        $localRoles = RoleMapper::toLocal($request->getPayload()->getRoles());

        if(!in_array($localRoles['course'], ['dozent', 'tutor'])) {
            throw new AccessDeniedException();
        }

        $this->resolveDeeplinkProvisioningMode($request);
    }

    public function select_contents_action(): void
    {
        PageLayout::setTitle(_('Inhalt auswählen'));
        PageLayout::disableHeader();
        PageLayout::disableFooter();

        $this->callbackId = Request::get('callback_id');

        $callbackData = $this->validateCallbackData($this->callbackId);
        if ($callbackData['action'] !== 'deeplink_callback') {
            throw new AccessDeniedException('Invalid callback action.');
        }

        if (!$GLOBALS['perm']->have_perm('tutor')) {
            $this->errors[] = _('Sie haben nicht die Berechtigung, diese Aktion auszuführen.');
            return;
        }

        $this->courses = Course::findBySQL(
            "JOIN seminar_user USING(Seminar_id)
                WHERE user_id = :user_id AND seminar_user.status IN ('dozent', 'tutor')
                ORDER BY mkdate DESC, Name",
            [
                'user_id' => User::findCurrent()->id
            ]
        );
    }

    public function deeplink_callback_action(): void
    {
        CSRFProtection::verifyUnsafeRequest();

        $callbackId = Request::get('callback_id');
        $callbackData = $this->validateCallbackData($callbackId);
        if ($callbackData['action'] !== 'deeplink_callback') {
            throw new AccessDeniedException('Invalid callback action.');
        }

        if (count(Request::getArray('courses_id')) === 0) {
            PageLayout::postError(_('Sie haben keinen Inhalt ausgewählt.'));
            $this->redirect('enroll/lti/select_contents?callback_id=' . $callbackId);
            return;
        }

        $deployment = Deployment::findOneBySQL("deployment_key = ?", [$callbackData['deployment_key']]);
        $registration = $deployment->registration;

        $resourceCollection = new ResourceCollection();
        foreach ($this->extractCoursesFromRequest() as $c) {
            $course = Course::find($c['id']);
            if ($course === null) {
                continue;
            }

            $resourceCollection->add(
                $course->toLti1p3ResourceLink($registration->name, $c['with_grading'])
            );
        }

        $deepLinkingSettingsClaim = $callbackData['settings_claim'];

        $message = (new DeepLinkingLaunchResponseBuilder())->buildDeepLinkingLaunchResponse(
            $resourceCollection,
            $registration->toLti1p3Registration($deployment),
            $deepLinkingSettingsClaim->getDeepLinkingReturnUrl(),
            $deployment->deployment_key,
            $deepLinkingSettingsClaim->getData()
        );

        $this->render_text($message->toHtmlRedirectForm());
    }

    public function provisioning_modes_action(): void
    {
        PageLayout::setTitle(_('Bereitstellungsmodus'));

        $this->callbackId = Request::get('callback_id');
        $callbackData = $this->validateCallbackData($this->callbackId);
        if (!isset($callbackData['provisioning_mode'])) {
            throw new AccessDeniedException('Invalid callback data');
        }

        $this->provisioningMode = (int) $callbackData['provisioning_mode'];
    }

    public function create_new_account_action(): void
    {
        CSRFProtection::verifyUnsafeRequest();

        $callbackId = Request::get('callback_id');
        $callbackData = $this->validateCallbackData($callbackId);
        if ($callbackData['action'] !== 'enroll_user') {
            throw new AccessDeniedException('Invalid callback action');
        }

        $publication = Publication::find($callbackData['publication_id']);
        $userManager = new UserManager();
        $userManager
            ->setUserIdentity($callbackData['user_identity'])
            ->enroll($publication, $callbackData['local_roles'], $callbackData['registration_id'])
            ->authenticate();

        unset($_SESSION['callbacks'][$callbackId]);

        $this->redirect('course/overview?cid='.$publication->range->id);
    }

    public function reset_account_mapping_action(): void
    {
        CSRFProtection::verifyUnsafeRequest();

        $callbackId = Request::get('callback_id');
        $callbackData = $this->validateCallbackData($callbackId);
        if ($callbackData['action'] !== 'deeplink_callback') {
            throw new AccessDeniedException('Invalid callback action.');
        }

        UserIdentityMapping::deleteBySQL(
            "user_id = :user_id AND context = :context",
            [
                'user_id' => User::findCurrent()->id,
                'context' => UserIdentityMappingContext::DeepLink->value
            ]
        );

        sess()->destroy();
        sess()->start();
        $_SESSION['callbacks'][$callbackId] = $callbackData;
        $_SESSION['redirect_after_login'] = URLHelper::getLink('dispatch.php/enroll/lti/select_contents?callback_id=' . $callbackId);

        $this->redirect('login?callback_id=' . $callbackId);
    }

    private function resolveProvisioningMode(LaunchValidationResultInterface $request, Publication $publication): void
    {
        $userLocale = $request->getPayload()->getLaunchPresentation()?->getLocale();
        $userIdentityMapping = UserIdentityMapping::findOneBySQL(
            "user_id = :user_id AND context = :context",
            [
                'user_id' => User::findCurrent()?->id,
                'context' => UserIdentityMappingContext::ResourceLink->value
            ]
        );

        if ($userIdentityMapping) {
            $this
                ->storeUserLocale($userIdentityMapping->user, $userLocale)
                ->redirect('course/overview?cid='.$publication->range->id);
            return;
        }


        $payload = $request->getPayload();
        $localRoles = RoleMapper::toLocal($payload->getRoles());
        $userManager = new UserManager();

        $userIdentityMapping = UserIdentityMapping::findOneBySQL(
            "context = :context AND external_email = :external_email AND external_user_id = :external_user_id AND registration_id = :registration_id",
            [
                'context' => UserIdentityMappingContext::ResourceLink->value,
                'external_email' => $payload->getUserIdentity()->getEmail(),
                'external_user_id' => $payload->getUserIdentity()->getIdentifier(),
                'registration_id' => $request->getRegistration()->getIdentifier()
            ]
        );

        if ($userIdentityMapping) {
            $userManager
                ->setUserIdentity($payload->getUserIdentity())
                ->setUser($userIdentityMapping->user)
                ->enroll($publication, $localRoles, $request->getRegistration()->getIdentifier())
                ->authenticate();

            $this
                ->storeUserLocale($userIdentityMapping->user, $userLocale)
                ->redirect('course/overview?cid='.$publication->range->id);
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
            $userManager
                ->setUserIdentity($payload->getUserIdentity())
                ->enroll($publication, $localRoles, $request->getRegistration()->getIdentifier())
                ->authenticate();

            $this
                ->storeUserLocale($userIdentityMapping->user, $userLocale)
                ->redirect('course/overview?cid='.$publication->range->id);
            return;
        }

        $callbackId = Uuid::uuid4()->toString();
        $_SESSION['callbacks'][$callbackId] = [
            'user_identity' => $payload->getUserIdentity(),
            'deployment_key' => $request->getPayload()->getDeploymentId(),
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

    private function resolveDeeplinkProvisioningMode(LaunchValidationResultInterface $request): void
    {
        $userLocale = $request->getPayload()->getLaunchPresentation()?->getLocale();

        $callbackId = Uuid::uuid4()->toString();
        $_SESSION['callbacks'][$callbackId] = [
            'user_identity' => $request->getPayload()->getUserIdentity(),
            'deployment_key' => $request->getPayload()->getDeploymentId(),
            'registration_id' => $request->getRegistration()->getIdentifier(),
            'settings_claim' => $request->getPayload()->getDeepLinkingSettings(),
            'provisioning_mode' => UserProvisioningMode::ExistingAccountsOnly->value,
            'context' => 'lti',
            'action' => 'deeplink_callback',
            'expires_at' => time() + 1800
        ];

        $userIdentityMapping = UserIdentityMapping::findOneBySQL(
            "user_id = :user_id AND context = :context",
            [
                'user_id' => User::findCurrent()->id,
                'context' => UserIdentityMappingContext::DeepLink->value
            ]
        );

        if ($userIdentityMapping) {
            $this
                ->storeUserLocale($userIdentityMapping->user, $userLocale)
                ->redirect('enroll/lti/select_contents?callback_id=' . $callbackId);
            return;
        }

        $payload = $request->getPayload();

        $userIdentityMapping = UserIdentityMapping::findOneBySQL(
            "context = :context AND external_email = :external_email AND external_user_id = :external_user_id AND registration_id = :registration_id",
            [
                'context' => UserIdentityMappingContext::DeepLink->value,
                'external_email' => $payload->getUserIdentity()->getEmail(),
                'external_user_id' => $payload->getUserIdentity()->getIdentifier(),
                'registration_id' => $request->getRegistration()->getIdentifier()
            ]
        );

        if ($userIdentityMapping) {
            (new UserManager())
                ->setUser($userIdentityMapping->user)
                ->authenticate();

            $this
                ->storeUserLocale($userIdentityMapping->user, $userLocale)
                ->redirect('enroll/lti/select_contents?callback_id=' . $callbackId);
            return;
        }

        $_SESSION['redirect_after_login'] = URLHelper::getLink('dispatch.php/enroll/lti/select_contents?callback_id=' . $callbackId);
        $this->redirect('enroll/lti/provisioning_modes?callback_id=' . $callbackId);
    }

    private function getPublication(LtiMessagePayloadInterface $payload): ?Publication
    {
        if (empty($payload->getCustom()['id'])) {
            return null;
        }

        return Publication::findOneBySQL("publication_key = ?", [$payload->getCustom()['id']]);
    }

    private function validateCallbackData(string $callbackId): array
    {
        if (empty($_SESSION['callbacks'][$callbackId])) {
            throw new AccessDeniedException('Missing or invalid callback ID');
        }

        $callbackData = $_SESSION['callbacks'][$callbackId];
        if (
            $callbackData['context'] !== 'lti'
            || $callbackData['expires_at'] < time()
        ) {
            throw new AccessDeniedException('Invalid or expired callback data');
        }

        return $callbackData;
    }

    private function storeUserLocale(User $user, ?string $locale): self
    {
        if ($locale && str_starts_with($locale, 'en')) {
            $_SESSION['_language'] = 'en_GB';
            $user->preferred_language = 'en_GB';
            $user->store();
        }

        return $this;
    }

    private function extractCoursesFromRequest(): array
    {
        $courses = [];
        for ($index = 0; $index < count(Request::getArray('courses_id')); $index++) {
            $courses[] = [
                'id' => Request::getArray('courses_id', $index),
                'with_grading' => (bool) Request::getArray('with_gradings', $index)
            ];
        }

        return $courses;
    }
}
