<?php
require_once __DIR__ . '/LtiBaseController.php';

use Lti\Publication;
use Ramsey\Uuid\Uuid;
use Trails\Dispatcher;
use Studip\LTIException;
use LTI\LtiBaseController;
use Lti\UserIdentityMapping;
use Studip\Lti\LTI1p3\RoleMapper;
use Studip\Lti\LTI1p3\UserManager;
use Studip\Lti\Enum\UserProvisioningMode;
use Studip\Lti\LTI1p3\PublicationValidator;
use Studip\Lti\Enum\UserIdentityMappingContext;
use OAT\Library\Lti1p3Core\Message\Payload\LtiMessagePayloadInterface;
use OAT\Library\Lti1p3Core\Message\Launch\Validator\Tool\ToolLaunchValidatorInterface;
use OAT\Library\Lti1p3Core\Message\Launch\Validator\Result\LaunchValidationResultInterface;

final class Enroll_Lti_LaunchController extends LtiBaseController
{
    public function __construct(
        protected Dispatcher $dispatcher,
        protected ToolLaunchValidatorInterface $launchValidator
    )
    {
        parent::__construct($dispatcher);
    }

    public function index_action(): void
    {
        $request = $this->launchValidator->validatePlatformOriginatingLaunch($this->getPsrRequest());

        if ($request->hasError()) {
            throw new LtiException($request->getError());
        }

        $publication = $this->getPublication($request->getPayload());

        (new PublicationValidator($publication))->validateLaunch();

        $this->resolveProvisioningMode($request, $publication);
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

    private function getPublication(LtiMessagePayloadInterface $payload): ?Publication
    {
        if (empty($payload->getCustom()['id'])) {
            throw new LtiException('Missing or invalid custom ID');
        }

        return Publication::findOneBySQL("publication_key = ?", [$payload->getCustom()['id']]);
    }
}
