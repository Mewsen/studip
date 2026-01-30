<?php

use Lti\Registration;
use Lti\ResourceLink;
use Studip\Cache\Factory;
use Studip\Lti\Enum\RegistrationStatus;
use Studip\Lti\Trait\RegistrationValidationTrait;
use Studip\LTI13a\RoleMapper;
use Studip\LTI13a\PlatformManager;
use Lti\RegistrationPrivacySettings;
use Studip\OAuth2\NegotiatesWithPsr7;
use Studip\LTI13a\RegistrationManager;
use OAT\Library\Lti1p3Core\Message\Payload\Claim\AgsClaim;
use OAT\Library\Lti1p3Core\Security\Nonce\NonceRepository;
use OAT\Library\Lti1p3Core\Message\Payload\Claim\ContextClaim;
use OAT\Library\Lti1p3DeepLinking\Factory\ResourceCollectionFactory;
use OAT\Library\Lti1p3Core\Message\Payload\Claim\LaunchPresentationClaim;
use OAT\Library\Lti1p3Core\Message\Launch\Validator\Platform\PlatformLaunchValidator;
use OAT\Library\Lti1p3Core\Message\Launch\Builder\LtiResourceLinkLaunchRequestBuilder;
use OAT\Library\Lti1p3DeepLinking\Message\Launch\Builder\DeepLinkingLaunchRequestBuilder;

final class Lti_13a_IndexController extends AuthenticatedController
{
    use NegotiatesWithPsr7;
    use RegistrationValidationTrait;
    protected COURSE | Institute $context;

    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        $this->context = Context::get();

        PageLayout::disableSidebar();
        PageLayout::setBodyElementId('lti');
    }

    public function launch_action(ResourceLink $resourceLink): void
    {
        $deployment = $resourceLink->deployment;
        $registration = $deployment->registration;

        if (!$this->validateRegistrationStatus($registration) || !$this->validateUserConsent($registration)) {
            return;
        }

        $registrationConfigs = $registration->getConfigValues();
        $launchContainer = $resourceLink->launch_container ?? $registrationConfigs['launch_container'];

        //The AGS URLs need several parameters:
        $agsUrlParameters = [
            'cid' => $this->context->getId(),
            'registration_id' => $registration->id,
            'deployment_id' => $deployment->deployment_key,
            'cancel_login' => '1'
        ];

        //Build the message:
        $resourceLinkRepo = $resourceLink->toLti1p3ResourceLink();

        $message = (new LtiResourceLinkLaunchRequestBuilder())->buildLtiResourceLinkLaunchRequest(
            $resourceLinkRepo,
            $registration->toLti1p3Registration(),
            User::findCurrent()->id,
            $deployment->deployment_key,
            RoleMapper::fromLocal($GLOBALS['perm']->get_studip_perm($this->context->getId())),
            [
                'https://purl.imsglobal.org/spec/lti/claim/custom' => $resourceLinkRepo->getCustom(),
                new ContextClaim(
                    $this->context->getId(),
                    ['http://purl.imsglobal.org/vocab/lis/v2/course#CourseOffering'],
                    $this->context->veranstaltungsnummer ?? '',
                    $this->context->getFullName() ?? ''
                ),
                new LaunchPresentationClaim(
                    $launchContainer,
                    null,
                    null,
                    URLHelper::getURL('dispatch.php/course/lti', ['deployment_id' => $deployment->deployment_key]),
                    str_replace('_', '-', $_SESSION['_language'])
                ),
                new AgsClaim(
                    [
                        'https://purl.imsglobal.org/spec/lti-ags/scope/lineitem',
                        'https://purl.imsglobal.org/spec/lti-ags/scope/result.readonly',
                        'https://purl.imsglobal.org/spec/lti-ags/scope/score'
                    ],
                    $this->url_for('lti/ags/line_items', $agsUrlParameters),
                    $this->url_for('lti/ags/line_item', $agsUrlParameters)
                )
            ]
        );

        $this->render_text($message->toHtmlRedirectForm());
    }

    public function select_contents_action(Registration $registration): void
    {
        $registrationConfigs = $registration->getConfigValues();

        if (empty($registrationConfigs['deep_linking_url'])) {
            PageLayout::postError(_('Der ausgewählte LTI-Registrierung unterstützt kein Deep Linking.'));
            $this->redirect('course/lti');
            return;
        }

        $builder = new DeepLinkingLaunchRequestBuilder();
        $message = $builder->buildDeepLinkingLaunchRequest(
            PlatformManager::getDeepLinkingConfiguration(),
            $registration->toLti1p3Registration(),
            User::findCurrent()->id,
            null,
            $registration->getDefaultDeployment()->deployment_key,
            RoleMapper::fromLocal($GLOBALS['perm']->get_studip_perm($this->context->id))
        );

        $this->render_text($message->toHtmlRedirectForm());
    }

    public function store_contents_action(): void
    {
        $validator = new PlatformLaunchValidator(
            new RegistrationManager(),
            new NonceRepository(Factory::getCache())
        );

        $result = $validator->validateToolOriginatingLaunch($this->getPsrRequest());
        if ($result->hasError()) {
            PageLayout::postError($result->getError());
            $this->redirect('course/lti');
            return;
        }

        $this->ltiResources = (new ResourceCollectionFactory())->createFromClaim(
            $result->getPayload()->getDeepLinkingContentItems()
        );

        $this->set_layout(null);
    }
}
