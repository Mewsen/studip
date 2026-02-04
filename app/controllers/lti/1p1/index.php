<?php

use Lti\Grade;
use Lti\Registration;
use Lti\ResourceLink;
use Trails\Dispatcher;
use Studip\OAuth2\NegotiatesWithPsr7;
use Studip\Lti\Trait\RegistrationValidationTrait;

final class Lti_1p1_IndexController extends AuthenticatedController
{
    use NegotiatesWithPsr7;
    use RegistrationValidationTrait;
    protected COURSE | Institute $context;

    public function __construct(Dispatcher $dispatcher)
    {
        $action = basename(get_route());
        if (in_array($action, ['profile', 'outcome'])) {
            $this->with_session = false;
        }

        parent::__construct($dispatcher);
    }

    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        if (in_array($action, ['profile', 'outcome'])) {
            return;
        }

        $this->context = Context::get();
        $this->isModerator = LtiToolModule::isModerator($this->context->id);

        PageLayout::disableSidebar();
        PageLayout::disableHeader();
        PageLayout::disableFooter();
        PageLayout::setBodyElementId('lti');
    }

    public function launch_action(ResourceLink $resourceLink): void
    {
        if (!$this->validateRegistrationStatus($resourceLink) || !$this->validateUserConsent($resourceLink)) {
            return;
        }

        $registration = $resourceLink->deployment->registration;
        $ltiLink = $this->getLtiLink($resourceLink, $registration);
        $this->launchUrl = $registration->config_values['launch_url'];
        $this->launchData = $ltiLink->getBasicLaunchData();
        $this->signature = $ltiLink->getLaunchSignature($this->launchData);

        $this->set_layout(null);
    }

    /**
     * Proceeds after the select_link action by switching to the LTI tool for
     * selecting the items from the deep-linked tool that shall be available in the Stud.IP course.
     */
    public function process_select_link_action(ResourceLink $resourceLink): void
    {
        $registration = $resourceLink->deployment->registration;
        $registrationConfigs = $registration->getConfigValues();
        $custom_parameters = explode("\n", $registrationConfigs['custom_parameters']);
        $content_item_return_url = $this->url_for('lti/1p1/index/save_link/' . $this->link->id);

        // set up ContentItemSelectionRequest
        $lti_link = new LtiLink($registrationConfigs['launch_url'], $registrationConfigs['consumer_key'], $registrationConfigs['consumer_secret'], $registrationConfigs['oauth_signature_method']);
        $lti_link->setUser(User::findCurrent(), 'Instructor', $registrationConfigs['send_lis_person']);
        $lti_link->setCourse($this->context->id);
        $lti_link->addLaunchParameters([
            'lti_message_type' => 'ContentItemSelectionRequest',
            'accept_media_types' => 'application/vnd.ims.lti.v1.ltilink',
            'accept_presentation_document_targets' => 'iframe,window',
            'content_item_return_url' => $content_item_return_url,
            'launch_presentation_locale' => str_replace('_', '-', $_SESSION['_language']),
            'launch_presentation_document_target' => 'window'
        ]);

        foreach ($custom_parameters as $param) {
            if (strpos($param, '=') !== false) {
                [$key, $value] = explode('=', $param, 2);
                $lti_link->addCustomParameter(trim($key), trim($value));
            }
        }

        $this->launch_url = $lti_link->getLaunchURL();
        $this->launch_data = $lti_link->getBasicLaunchData();
        $this->signature = $lti_link->getLaunchSignature($this->launch_data);

        $this->set_layout(null);
        $this->render_action('iframe');
    }

    /**
     * Handles the jump back from the LTI tool into Stud.IP and finishes the integration
     * of a deep-linked LTI tool into a Stud.IP course.
     */
    public function save_link_action(ResourceLink $resourceLink): void
    {
        $registration = $resourceLink->deployment->registration;

        $lti_msg = Request::get('lti_msg');
        $lti_errormsg = Request::get('lti_errormsg');
        $content_items = Request::get('content_items');
        $content_items = json_decode($content_items, true);

        if (!Studip\OAuth1::verifyRequest($this->getPsrRequest(), $registration->config_vakues['consumer_secret'], '')) {
            throw new Exception('Could not verify request.');
        }

        if (is_array($content_items) && count($content_items['@graph'])) {
            // we only support selecting a single content item
            $item = $content_items['@graph'][0];

            $lti_data = new ResourceLink();
            $lti_data->title = (string) $item['title'];
            $lti_data->description = Studip\Markup::purifyHtml(Studip\Markup::markAsHtml($item['text']));
            $lti_data->deployment_id = $registration->getDefaultDeployment()->id;
            $lti_data->launch_url = (string) ($item['url'] ?? '');
            $options = [];
            if (is_array($item['custom'])) {
                $custom_parameters = '';
                foreach ($item['custom'] as $key => $value) {
                    $custom_parameters .= $key . '=' . $value . "\n";
                }
                $options['custom_parameters'] = $custom_parameters;
            }

            if (isset($item['placementAdvice']['presentationDocumentTarget'])) {
                $options['document_target'] = $item['placementAdvice']['presentationDocumentTarget'];
            }

            $lti_data->options = $options;
            $lti_data->store();

            PageLayout::postSuccess($lti_msg ?: _('Der Link wurde als neuer Abschnitt hinzugefügt.'));
        }

        if (!empty($lti_errormsg)) {
            PageLayout::postError($lti_errormsg);
        }

        $this->redirect('course/lti');
    }

    /**
     * Return the LTI consumer profile in standard JSON format.
     */
    public function profile_action(ResourceLink $resourceLink): void
    {
        $profile = [
            '@context' => ['http://purl.imsglobal.org/ctx/lti/v2/ToolConsumerProfile'],
            '@type' => 'ToolConsumerProfile',
            'lti_version' => 'LTI-1p0',
            'guid' => md5(Config::get()->STUDIP_INSTALLATION_ID),
            'product_instance' => [
                'guid' => Config::get()->STUDIP_INSTALLATION_ID,
                'product_info' => [
                    'product_name' => ['default_value' => 'Stud.IP'],
                    'product_version' => $GLOBALS['SOFTWARE_VERSION'],
                    'product_family' => [
                        'code' => 'studip',
                        'vendor' => [
                            'code' => 'studip.de',
                            'vendor_name' => ['default_value' => 'Stud.IP e.V.'],
                            'website' => 'https://www.studip.de/',
                            'timestamp' => date('c')
                        ]
                    ]
                ],
                'service_owner' => [
                    'service_owner_name' => ['default_value' => Config::get()->UNI_NAME_CLEAN],
                    'description' => ['default_value' => $GLOBALS['UNI_INFO']],
                    'support' => ['email' => $GLOBALS['UNI_CONTACT']],
                    'timestamp' => date('c')
                ]
            ],
            'capability_offered' => [
                'basic-lti-launch-request',
                'ContentItemSelectionRequest',
                'Context.id',
                'Context.label',
                'Context.title',
                'Context.type',
                'CourseSection.courseNumber',
                'CourseSection.credits',
                'CourseSection.dept',
                'CourseSection.label',
                'CourseSection.longDescription',
                'CourseSection.maxNumberofStudents',
                'CourseSection.numberofStudents',
                'CourseSection.shortDescription',
                'CourseSection.sourcedId',
                'CourseSection.title',
                'Person.email.primary',
                'Person.name.family',
                'Person.name.full',
                'Person.name.given',
                'Person.name.prefix',
                'Person.name.suffix',
                'Person.sourcedId',
                'Person.webaddress',
                'ResourceLink.description',
                'ResourceLink.id',
                'ResourceLink.title',
                'ToolConsumerProfile.url',
                'User.id',
                'User.image',
                'User.username'
            ],
            'service_offered' => [
                '@type' => 'RestService',
                '@id' => 'tcp:Outcomes.LTI1',
                'endpoint' => URLHelper::getLink('dispatch.php/lti/1p1/index/outcome/' . $resourceLink->id),
                'format' => ['application/vnd.ims.lti.v1.outcome+xml'],
                'action' => ['POST']
            ]
        ];

        $this->set_content_type('application/vnd.ims.lti.v2.toolconsumerprofile+json');
        $this->render_text(json_encode($profile));
    }

    /**
     * Handle outcome service callback request by the LTI tool.
     *
     * @param ResourceLink $resourceLink
     */
    public function outcome_action(ResourceLink $resourceLink): void
    {
        $registrationConfigs = $resourceLink->deployment->registration->getConfigValues();

        if (!Studip\OAuth1::verifyRequest($this->getPsrRequest(), $registrationConfigs['consumer_secret'], '')) {
            throw new Exception('Could not verify request.');
        }

        // fetch and parse POST data
        $message = file_get_contents('php://input');
        $envelope = new SimpleXMLElement($message);
        $header = current($envelope->imsx_POXHeader->children());
        $body = current($envelope->imsx_POXBody->children());

        $message_id = trim($header->imsx_messageIdentifier);
        $operation = $body->getName();
        $user_id = trim($body->resultRecord->sourcedGUID->sourcedId);
        $grade = new Grade([$resourceLink->id, $user_id]);

        $this->message_id = uniqid();
        $this->message_ref = $message_id;
        $this->status_severity = 'status';
        $this->status_code = 'success';
        $this->operation = $operation;

        if (!CourseMember::exists([$resourceLink->course_id, $user_id])) {
            $this->status_severity = 'error';
            $this->status_code = 'failure';
            $this->description = 'incorrect sourcedId: ' . $user_id;
        } else if ($operation === 'readResultRequest') {
            if ($grade->isNew()) {
                $this->status_severity = 'error';
                $this->status_code = 'failure';
                $this->description = 'no score found for: ' . $user_id;
            } else {
                $this->score = $grade->score;
                $this->description = 'score has been read';
            }
        } else if ($operation === 'replaceResultRequest') {
            $grade->score = (float) $body->resultRecord->result->resultScore->textString;
            $grade->store();
            $this->description = 'score has been updated';
        } else if ($operation === 'deleteResultRequest') {
            $grade->delete();
            $this->description = 'score has been deleted';
        } else {
            $this->status_severity = 'error';
            $this->status_code = 'unsupported';
            $this->description = 'operation not supported: ' . $operation;
        }

        $this->set_content_type('text/xml; charset=UTF-8');
        $this->set_layout(null);
    }

    /**
     * Return an LtiLink object for the configured LTI content block.
     *
     * @param ResourceLink $resourceLink
     * @param Registration $registration
     * @return LtiLink LTI link representation
     */
    private function getLtiLink(ResourceLink $resourceLink, Registration $registration): LtiLink
    {
        $registrationConfigs = $registration->getConfigValues();
        $role = $this->isModerator ? 'Instructor' : 'Learner';
        $customParameters = explode("\n", $resourceLink->getCustomParameters());
        $lisOutcomeServiceUrl = $this->url_for('lti/1p1/index/outcome/' . $resourceLink->id, ['cid' => null]);
        $tcProfileUrl = $this->url_for('lti/1p1/index/profile/' . $resourceLink->id, ['cid' => null]);

        // set up launch request
        $ltiLink = new LtiLink(
            $registrationConfigs['launch_url'],
            $registrationConfigs['consumer_key'],
            $registrationConfigs['consumer_secret'],
            $registrationConfigs['oauth_signature_method']
        );
        $ltiLink->setResource($resourceLink->id, $resourceLink->title, kill_format($resourceLink->description));
        $ltiLink->setUser(User::findCurrent(), $role, $registrationConfigs['send_lis_person']);
        $ltiLink->setCourse($resourceLink->course_id);
        $ltiLink->addVariable('ToolConsumerProfile.url', $tcProfileUrl);
        $ltiLink->addLaunchParameters([
            'launch_presentation_locale' => str_replace('_', '-', $_SESSION['_language']),
            'launch_presentation_document_target' => $resourceLink->options['document_target'] ?? 'window',
            'lis_outcome_service_url' => $lisOutcomeServiceUrl,
            'lis_result_sourcedid' => User::findCurrent()->id
        ]);

        foreach ($customParameters as $param) {
            if (strpos($param, '=') !== false) {
                [$key, $value] = explode('=', $param, 2);
                $ltiLink->addCustomParameter(trim($key), trim($value));
            }
        }

        return $ltiLink;
    }
}
