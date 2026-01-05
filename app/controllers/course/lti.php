<?php

use Lti\Deployment;
use Lti\Grade;
use Lti\Registration;
use Lti\ResourceLink;
use OAT\Library\Lti1p3Core\Message\Launch\Builder\LtiResourceLinkLaunchRequestBuilder;
use OAT\Library\Lti1p3Core\Message\Launch\Validator\Platform\PlatformLaunchValidator;
use OAT\Library\Lti1p3Core\Message\Payload\Claim\AgsClaim;
use OAT\Library\Lti1p3Core\Message\Payload\Claim\ContextClaim;
use OAT\Library\Lti1p3Core\Message\Payload\Claim\LaunchPresentationClaim;
use OAT\Library\Lti1p3Core\Resource\LtiResourceLink\LtiResourceLinkInterface;
use OAT\Library\Lti1p3Core\Security\Nonce\NonceRepository;
use OAT\Library\Lti1p3DeepLinking\Factory\ResourceCollectionFactory;
use OAT\Library\Lti1p3DeepLinking\Message\Launch\Builder\DeepLinkingLaunchRequestBuilder;
use Studip\LTI13a\PlatformManager;
use Studip\LTI13a\RegistrationManager;
use Studip\LTI13a\RegistrationRepository;
use Studip\OAuth2\NegotiatesWithPsr7;
use Trails\Dispatcher;

/**
 * course/lti.php - LTI consumer API for Stud.IP
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Elmar Ludwig
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 */

class Course_LtiController extends StudipController
{
    use NegotiatesWithPsr7;

    public function __construct(Dispatcher $dispatcher)
    {
        // these actions do not require session authentication
        $action = basename(get_route());
        if (!in_array($action, ['profile', 'outcome'])) {
            $this->with_session = true;
            $this->allow_nobody = false;
        }
        parent::__construct($dispatcher);
    }

    /**
     * Callback function being called before an action is executed.
     */
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);
        //The profile and outcome actions do not require all the other
        //stuff that is going on in this method:
        if (in_array($action, ['profile', 'outcome'])) {
            return;
        }
        $this->range_id = Context::getId();
        $this->course = Course::find($this->range_id);

        $this->edit_perm = $GLOBALS['perm']->have_studip_perm('tutor', $this->range_id);
        if (!in_array($action, ['index', 'launch', 'grades', 'consent']) && !$this->edit_perm) {
            throw new AccessDeniedException();
        }

        if (
            !in_array($action, ['admin', 'grades'])
            && Navigation::hasItem('/course/lti/index')
        ) {
            Navigation::activateItem('/course/lti/index');
        }
    }

    /**
     * Display the list of LTI content blocks.
     */
    public function index_action()
    {
        Helpbar::get()->addPlainText('', _('Auf dieser Seite können Sie externe Anwendungen einbinden, sofern diese den LTI-Standard (Version 1.x order 1.3a) unterstützen.'));

        $this->links = [];
        if ($this->edit_perm) {
            $this->links = LtiResourceLink::findByCourse_id($this->range_id, 'ORDER BY `position`');
        } else {
            //Only load those LTI resource links that are fully configured:
            $this->links = LtiResourceLink::findBySQL(
                "JOIN `lti_deployments`
                ON `lti_deployments`.`id` = `lti_resource_links`.`deployment_id`
                WHERE `lti_resource_links`.`course_id` = :course_id
                AND (`lti_resource_links`.`options` IS NULL OR `lti_resource_links`.`options` NOT LIKE '%unfinished_deep_linking%')
                ORDER BY `lti_resource_links`.`position`",
                ['course_id' => $this->range_id]
            );
        }

        if ($this->edit_perm) {
            $widget = Sidebar::get()->addWidget(new ActionsWidget());

            $widget->addLink(
                _('LTI-Ressource hinzufügen'),
                $this->url_for('admin/lti/resources/create'),
                Icon::create('add')
            )->asDialog('width=700;height=700');
        }

        //Check for error messages:
        if (Request::get('deployment_id') && (Request::submitted('lti_msg') || Request::submitted('lti_errormsg'))) {
            $deployment = Deployment::findOneBySQL("deployment_id = ?", [Request::get('deployment_id')]);
            if ($deployment) {
                //Get the resource link for the deployment and display the messages:
                $resourceLink = ResourceLink::findOneBySQL(
                    "`deployment_id` = :deployment_id AND `course_id` = :course_id",
                    [
                        'deployment_id' => $deployment->id,
                        'course_id' => $this->range_id
                    ]
                );

                if ($resourceLink) {
                    if (Request::get('lti_msg')) {
                        PageLayout::postInfo(htmlReady($resourceLink->title . ': ' . Request::get('lti_msg')));
                    }
                    if (Request::get('lti_errormsg')) {
                        PageLayout::postError(htmlReady($resourceLink->title . ': ' . Request::get('lti_errormsg')));
                    }
                }
            }
        }

        $resources = ResourceLink::findBySQL(
            "course_id = :course_id ORDER BY `position`",
            [
                'course_id' => $this->range_id
            ]
        );

        $this->render_vue_app(
            Studip\VueApp::create('lti/resources/Index')
                ->withProps([
                    'resources' => array_map(fn ($r) => $r->transformData(['registration', 'deployment']), $resources)
                ])
        );
    }

    public function consent_action(ResourceLink $resourceLink): void
    {
        if (!$resourceLink) {
            PageLayout::postError(_('Die Einbindung eines LTI-Tools ist ungültig.'));
            return;
        }

        $registrationId = $resourceLink->deployment->registration_id;
        $privacySettings = LtiToolPrivacySettings::findOneBySQL(
            'registration_id = :registration_id AND user_id = :user_id',
            [
                'registration_id' => $registrationId,
                'user_id' => User::findCurrent()->id
            ]
        );

        if (!$privacySettings) {
            $privacySettings = new LtiToolPrivacySettings();
            $privacySettings->registration_id = $registrationId;
            $privacySettings->user_id = User::findCurrent()->id;
        }

        if (Request::isPost()) {
            CSRFProtection::verifyUnsafeRequest();

            if (Request::submitted('save')) {
                if (!Request::get('confirmed')) {
                    PageLayout::postError(_('Ohne die aktive Zustimmung zur Weitergabe Ihrer personenbezogenen Daten können Sie das LTI-Tool nicht nutzen!'));
                    return;
                }

                $privacySettings->accepted = 1;

                //Check which optional fields are allowed to be transmitted to the tool:
                $optionalFieldList = Request::getArray('submit_optional_field');
                $optionalFields = [];
                if (array_key_exists('lang', $optionalFieldList)) {
                    $optionalFields[] = 'lang';
                }
                if (array_key_exists('avatar_url', $optionalFieldList)) {
                    $optionalFields[] = 'avatar_url';
                }

                $privacySettings->allowed_optional_fields = implode(',', $optionalFields);

                $privacySettings->store();

                if (Request::get('redirect') === 'launch') {
                    $this->redirect('course/lti/launch/' . $resourceLink->id);
                    return;
                }
            }
            if (Request::isDialog()) {
                //Close the dialog:
                $this->response->add_header('X-Dialog-Close', '1');
                return;
            } else {
                //Redirect to the LTI tool page of the course:
                $this->redirect('course/lti/index');
            }
        }

        $this->resourceLink = $resourceLink;
        $this->privacySettings = $privacySettings;

        if (Request::int('launch_container') === 2) {
            $this->set_layout($GLOBALS['template_factory']->open('lti/layout'));
        }
    }

    /**
     * Launch an LTI resource.
     */
    public function launch_action(LtiResourceLink $resourceLink): void
    {
        $deployment = $resourceLink->deployment;
        $registration = $deployment->registration;
        $registrationConfigs = $registration->getConfigValues();
        $dataProtectionConsent = LtiToolPrivacySettings::countBySQL(
            "`registration_id` = :registration_id AND `user_id` = :user_id AND `accepted` = 1",
            [
                'registration_id' => $registration->id,
                'user_id' => User::findCurrent()->id
            ]
        );

        $launchContainer = $resourceLink->launch_container ?? $registrationConfigs['launch_container'];

        if (!$dataProtectionConsent) {
            $this->redirect('course/lti/consent/' . $resourceLink->id, ['redirect' => 'launch', 'launch_container' => $launchContainer]);
            return;
        }

        //Redirect to the tool:
        $this->ltiVersion = $registration->version;

        if ($this->ltiVersion === '1.3a') {
            $locale = str_replace('_', '-', $_SESSION['_language']);
            $returnUrl = URLHelper::getURL($GLOBALS['ABSOLUTE_URI_STUDIP'] . 'dispatch.php/course/lti', ['deployment_id' => $deployment->deployment_id]);
            $documentTarget = 'window';
            if (!empty($resourceLink->options['document_target'])) {
                $returnUrl = $resourceLink->options['document_target'];
                $documentTarget = 'iframe';
            }

            //The AGS URLs need several parameters:
            $agsUrlParameters = [
                'cid' => $this->range_id,
                'registration_id' => $registration->id,
                'deployment_id' => $deployment->deployment_id,
                'cancel_login' => '1'
            ];

            //Build the message:
            $this->message = (new LtiResourceLinkLaunchRequestBuilder())->buildLtiResourceLinkLaunchRequest(
                $resourceLink,
                $registration->toLti1p3Registration(),
                User::findCurrent()->id,
                $deployment->deployment_id,
                [
                    PlatformManager::getLtiRoleClaimForStudipRole($GLOBALS['perm']->get_studip_perm($this->range_id))
                ],
                [
                    ...$resourceLink->getCustomLtiParameterArray(),
                    new ContextClaim(
                        $this->range_id,
                        ['http://purl.imsglobal.org/vocab/lis/v2/course#CourseOffering'],
                        $this->course->veranstaltungsnummer ?? '',
                        $this->course?->getFullName() ?? ''
                    ),
                    new LaunchPresentationClaim(
                        $documentTarget,
                        null,
                        null,
                        $returnUrl,
                        $locale
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
        } else {
            //LTI 1.0/1.1
            $this->resourceLink = $resourceLink;
            $this->deployment = $deployment;
            $ltiLink = $this->getLtiLink($this->deployment, $registration);
            $this->launchUrl = $registration->config_values['launch_url'];
            $this->launchData = $ltiLink->getBasicLaunchData();
            $this->signature = $ltiLink->getLaunchSignature($this->launchData);
        }

        $this->set_layout(null);
    }

    /**
     * Proceeds after the select_link action by switching to the LTI tool for
     * selecting the items from the deep-linked tool that shall be available in the Stud.IP course.
     */
    public function process_select_link_action(ResourceLink $resourceLink): void
    {
        $registration = $resourceLink->deployment->registration ?? null;

        if ($resourceLink->launch_type !== 'deep_linking') {
            PageLayout::postError(_('Der ausgewählte LTI-Ressource unterstützt kein Deep Linking.'));
            $this->redirect('course/lti');
            return;
        }

        if ($registration->version === '1.3a') {
            //LTI 1.3a
            $builder = new DeepLinkingLaunchRequestBuilder();
            $message = $builder->buildDeepLinkingLaunchRequest(
                PlatformManager::getDeepLinkingConfiguration($resourceLink->id, $this->range_id),
                $registration->toLti1p3Registration(),
                User::findCurrent()->id,
                null,
                $resourceLink->deployment->deployment_id,
                [PlatformManager::getLtiRoleClaimForStudipRole($GLOBALS['perm']->get_studip_perm($this->range_id))]
            );

            $this->render_text($message->toHtmlRedirectForm());
        } else {
            //LTI 1.0/1.1
            $registrationConfigs = $registration->getConfigValues();
            $custom_parameters = explode("\n", $registrationConfigs['custom_parameters']);
            $content_item_return_url = $this->url_for('course/lti/save_link/' . $this->link->id);

            // set up ContentItemSelectionRequest
            $lti_link = new LtiLink($registrationConfigs['launch_url'], $registrationConfigs['consumer_key'], $registrationConfigs['consumer_secret'], $registrationConfigs['oauth_signature_method']);
            $lti_link->setUser($GLOBALS['user']->id, 'Instructor', $registrationConfigs['send_lis_person']);
            $lti_link->setCourse($this->range_id);
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
    }

    /**
     * Handles the jump back from the LTI tool into Stud.IP and finishes the integration
     * of a deep-linked LTI tool into a Stud.IP course.
     */
    public function save_link_action(ResourceLink $resourceLink): void
    {
        $registration = $resourceLink->deployment->registration ?? null;

        if ($registration->version === '1.3a') {
            //LTI 1.3a
            $validator = new PlatformLaunchValidator(
                new RegistrationManager(),
                new NonceRepository(Studip\Cache\Factory::getCache())
            );

            $result = $validator->validateToolOriginatingLaunch($this->getPsrRequest());
            if ($result->hasError()) {
                PageLayout::postError($result->getError());
                $this->redirect('course/lti');
                return;
            }

            $ltiResources = (new ResourceCollectionFactory())->createFromClaim(
                $result->getPayload()->getDeepLinkingContentItems()
            );

            $links = $ltiResources->getByType(LtiResourceLinkInterface::TYPE);
            if (count($links) > 0) {
                foreach ($links as $link) {
                    $custom_parameters = '';
                    foreach ($link->getCustom() as $key => $value) {
                        $custom_parameters .= $key . '=' . $value . "\n";
                    }

                    ResourceLink::create([
                        'title' => $link->getTitle(),
                        'launch_url' => $link->getUrl(),
                        'custom_parameters' => $custom_parameters,
                        'launch_container' => $link->getProperties()->get('presentation')['documentTarget'] ?? $resourceLink->launch_container,
                        'deployment_id' => $resourceLink->deployment_id,
                        'course_id' => $resourceLink->course_id,
                        'description' => $resourceLink->description,
                        'position' => $resourceLink->position,
                        'color' => $resourceLink->color,
                        'icon' => $resourceLink->icon
                    ]);
                }

                $resourceLink->delete();
            }
        } else {
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

                $lti_data = new Deployment();
                $lti_data->title = (string) $item['title'];
                $lti_data->description = Studip\Markup::purifyHtml(Studip\Markup::markAsHtml($item['text']));
                $lti_data->registration_id = $registration->id;
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
                $link = new \LtiResourceLink();
                $link->deployment_id = $lti_data->id;
                $link->course_id     = $this->range_id;
                $link->position      = \LtiResourceLink::countBySQL('course_id = ?', [$this->range_id]);
                $link->store();
                PageLayout::postSuccess($lti_msg ?: _('Der Link wurde als neuer Abschnitt hinzugefügt.'));
            }
        }

        if (!empty($lti_errormsg)) {
            PageLayout::postError($lti_errormsg);
        }

        $this->redirect('course/lti');
    }

    /**
     * Return the LTI consumer profile in standard JSON format.
     *
     * @param   int $id    link id
     */
    public function profile_action($id)
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
                'endpoint' => $this->url_for('course/lti/outcome/' . $id),
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
     * @param LtiResourceLink $resourceLink
     */
    public function outcome_action(LtiResourceLink $resourceLink)
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
     * Display the (simple) LTI grade book.
     */
    public function grades_action()
    {
        Navigation::activateItem('/course/lti/grades');

        if ($this->edit_perm) {
            $this->lti_data_array = \LtiResourceLink::findBySQL(
                "`course_id` = :course_id
                ORDER BY `position`",
                ['course_id' => $this->range_id]
            );
        } else {
            //Only load those deployments that are fully configured:
            $this->lti_data_array = \LtiResourceLink::findBySQL(
                "`course_id` = :course_id
                AND (`options` IS NULL OR `options` NOT LIKE '%unfinished_deep_linking%')
                ORDER BY `position`",
                ['course_id' => $this->range_id]
            );
        }

        if ($this->edit_perm) {
            $this->desc = Request::int('desc');
            $this->members = CourseMember::findByCourseAndStatus($this->range_id, 'autor');

            if ($this->desc) {
                $this->members = array_reverse($this->members);
            }

            $widget = Sidebar::get()->addWidget(new ExportWidget());
            $widget->addLink(
                _('Ergebnisse exportieren'),
                $this->url_for('course/lti/export_grades'),
                Icon::create('download')
            );
        } else {
            $this->render_action('grades_user');
        }

        Helpbar::get()->addPlainText('', _('Auf dieser Seite können Sie die Ergebnisse sehen, die von LTI-Tools zurückgemeldet wurden.'));
    }

    /**
     * Export grades from the gradebook in CSV format.
     */
    public function export_grades_action()
    {
        if ($this->edit_perm) {
            $lti_data_array = \LtiResourceLink::findByCourse_id($this->range_id, 'ORDER BY position');
        } else {
            //Only load those deployments that are fully configured:
            $lti_data_array = \LtiResourceLink::findBySQL(
                "`course_id` = :course_id AND (`options` IS NULL OR `options` NOT LIKE '%unfinished_deep_linking%')
                ORDER BY `position`",
                ['course_id' => $this->range_id]
            );
        }

        $columns = [_('Nachname'), _('Vorname')];

        // add one column for each LTI tool block
        foreach ($lti_data_array as $lti_data) {
            $columns[] = $lti_data->title;
        }

        $data = [$columns];

        foreach (CourseMember::findByCourseAndStatus($this->range_id, 'autor') as $member) {
            $row = [$member->nachname, $member->vorname];

            foreach ($lti_data_array as $lti_data) {
                if ($grade = $lti_data->grades->findOneBy('user_id', $member->user_id)) {
                    $row[] = (float) $grade->score;
                } else {
                    $row[] = '';
                }
            }

            $data[] = $row;
        }

        $filename = Context::get()->name . ' - ' . _('Ergebnisse') . '.csv';
        $this->render_csv($data, $filename);
    }

    /**
     * Return an LtiLink object for the configured LTI content block.
     *
     * @param Deployment $deployment data of LTI content block
     * @param Registration $registration
     * @return LtiLink LTI link representation
     */
    protected function getLtiLink(Deployment $deployment, Registration $registration): LtiLink
    {
        $registrationConfigs = $registration->getConfigValues();

        $roles = $this->edit_perm ? 'Instructor' : 'Learner';
        $custom_parameters = explode("\n", $deployment->getCustomParameters());
        $description = kill_format($deployment->description);
        $lis_outcome_service_url = $this->url_for('course/lti/outcome/' . $deployment->id, ['cid' => null]);
        $tc_profile_url = $this->url_for('course/lti/profile/' . $deployment->id, ['cid' => null]);

        // set up launch request
        $ltiLink = new LtiLink(
            $registrationConfigs['launch_url'],
            $registrationConfigs['consumer_key'],
            $registrationConfigs['consumer_secret'],
            $registrationConfigs['oauth_signature_method']
        );
        $ltiLink->setResource($deployment->id, $deployment->title, $description);
        $ltiLink->setUser(User::findCurrent()->id, $roles, $registrationConfigs['send_lis_person']);
        $ltiLink->setCourse($deployment->course_id);
        $ltiLink->addVariable('ToolConsumerProfile.url', $tc_profile_url);
        $ltiLink->addLaunchParameters([
            'launch_presentation_locale' => str_replace('_', '-', $_SESSION['_language']),
            'launch_presentation_document_target' => $deployment->options['document_target'],
            'lis_outcome_service_url' => $lis_outcome_service_url,
            'lis_result_sourcedid' => User::findCurrent()->id
        ]);

        foreach ($custom_parameters as $param) {
            if (strpos($param, '=') !== false) {
                [$key, $value] = explode('=', $param, 2);
                $ltiLink->addCustomParameter(trim($key), trim($value));
            }
        }

        return $ltiLink;
    }
}
