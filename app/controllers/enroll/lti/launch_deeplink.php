<?php
require_once __DIR__ . '/LtiBaseController.php';

use Lti\Deployment;
use Ramsey\Uuid\Uuid;
use Trails\Dispatcher;
use Studip\LTIException;
use LTI\LtiBaseController;
use Lti\UserIdentityMapping;
use Studip\Lti\LTI1p3\RoleMapper;
use Studip\Lti\LTI1p3\UserManager;
use Studip\Lti\Enum\UserProvisioningMode;
use Studip\Lti\Enum\UserIdentityMappingContext;
use OAT\Library\Lti1p3Core\Resource\ResourceCollection;
use OAT\Library\Lti1p3Core\Message\Launch\Validator\Tool\ToolLaunchValidatorInterface;
use OAT\Library\Lti1p3DeepLinking\Message\Launch\Builder\DeepLinkingLaunchResponseBuilder;
use OAT\Library\Lti1p3Core\Message\Launch\Validator\Result\LaunchValidationResultInterface;

final class Enroll_Lti_LaunchDeeplinkController extends LtiBaseController
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

        $localRoles = RoleMapper::toLocal($request->getPayload()->getRoles());

        if(!in_array($localRoles['course'], ['dozent', 'tutor'])) {
            throw new AccessDeniedException();
        }

        $this->resolveDeeplinkProvisioningMode($request);
    }

    public function callback_action(): void
    {
        $this->allow_nobody = false;
        CSRFProtection::verifyUnsafeRequest();

        $callbackId = Request::get('callback_id');
        $callbackData = $this->validateCallbackData($callbackId);
        if ($callbackData['action'] !== 'deeplink_callback') {
            throw new AccessDeniedException('Invalid callback action.');
        }

        if (count(Request::getArray('courses_id')) === 0) {
            PageLayout::postError(_('Sie haben keinen Inhalt ausgewählt.'));
            $this->redirect('enroll/lti/contents?callback_id=' . $callbackId);
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
                ->redirect('enroll/lti/contents?callback_id=' . $callbackId);
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
                ->redirect('enroll/lti/contents?callback_id=' . $callbackId);
            return;
        }

        $_SESSION['redirect_after_login'] = URLHelper::getLink('dispatch.php/enroll/lti/contents?callback_id=' . $callbackId);
        $this->redirect('enroll/lti/provisioning_modes?callback_id=' . $callbackId);
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
