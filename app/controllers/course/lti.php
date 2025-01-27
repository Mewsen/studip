<?php

use OAT\Library\Lti1p3Core\Message\Launch\Builder\LtiResourceLinkLaunchRequestBuilder;
use OAT\Library\Lti1p3Core\Message\Launch\Validator\Platform\PlatformLaunchValidator;
use OAT\Library\Lti1p3Core\Message\Payload\Claim\AgsClaim;
use OAT\Library\Lti1p3Core\Message\Payload\Claim\ContextClaim;
use OAT\Library\Lti1p3Core\Resource\LtiResourceLink\LtiResourceLink;
use OAT\Library\Lti1p3Core\Resource\LtiResourceLink\LtiResourceLinkInterface;
use OAT\Library\Lti1p3Core\Security\Nonce\NonceRepository;
use OAT\Library\Lti1p3DeepLinking\Factory\ResourceCollectionFactory;
use OAT\Library\Lti1p3DeepLinking\Message\Launch\Builder\DeepLinkingLaunchRequestBuilder;
use Studip\LTI13a\PlatformManager;
use Studip\LTI13a\Registration;
use Studip\LTI13a\RegistrationManager;

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
    use Studip\OAuth2\NegotiatesWithPsr7;

    public function __construct(\Trails\Dispatcher $dispatcher)
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
        $this->course_id = Context::getId();
        $this->course = Course::find($this->course_id);

        if (in_array($action, ['select_tool', 'add_link']) && !$this->course) {
            throw new AccessDeniedException();
        }

        $this->edit_perm = $GLOBALS['perm']->have_studip_perm('tutor', $this->course_id);
        if (!in_array($action, ['index', 'iframe', 'grades', 'consent']) && !$this->edit_perm) {
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
        $this->lti_data_array = [];
        if ($this->edit_perm) {
            $this->lti_data_array = LtiDeployment::findByCourse_id($this->course_id, 'ORDER BY position');
        } else {
            //Only load those deployments that are fully configured:
            $this->lti_data_array = LtiDeployment::findBySQL(
                "`course_id` = :course_id AND (`options` IS NULL OR `options` NOT LIKE '%unfinished_deep_linking%')
                ORDER BY `position`",
                ['course_id' => $this->course_id]
            );
        }

        if ($this->edit_perm) {
            $widget = Sidebar::get()->addWidget(new ActionsWidget());
            $widget->addLink(
                _('Einstellungen'),
                $this->url_for('course/lti/config'),
                Icon::create('admin')
            )->asDialog('size=auto');
            $global_tools_available = LtiTool::countBySQL("`range_id` = 'global'") > 0;
            if (Config::get()->LTI_ALLOW_TOOL_CONFIG_IN_COURSE || $global_tools_available) {
                $widget->addLink(
                    _('LTI-Tool hinzufügen'),
                    $this->url_for('course/lti/select_tool'),
                    Icon::create('add')
                )->asDialog('size=auto');
            }

            $global_deep_linking_tools_exist = LtiTool::countBySQL("`deep_linking` = 1 AND `range_id` = 'global'") > 0;
            if ($global_deep_linking_tools_exist) {
                $widget->addLink(
                    _('Tool mittels LTI Deep Linking hinzufügen'),
                    $this->url_for('course/lti/add_link'),
                    Icon::create('network2')
                )->asDialog('size=auto');
            }
        }

        Helpbar::get()->addPlainText('', _('Auf dieser Seite können Sie externe Anwendungen einbinden, sofern diese den LTI-Standard (Version 1.x) unterstützen.'));
    }

    public function select_tool_action()
    {
        //The permission check is done in the before filter.

        $this->global_tools = LtiTool::findBySQL("`lti_version` = '1.3a' AND `range_id` = 'global' ORDER BY `name` ASC");

        if (!$this->global_tools) {
            if (!Config::get()->LTI_ALLOW_TOOL_CONFIG_IN_COURSE) {
                PageLayout::postError(_('Es sind keine globalen LTI-Tools konfiguriert, die in dieser Veranstaltung eingebunden werden können.'));
                return;
            }
            //Redirect to the page to configure an LTI tool for the course:
            $this->redirect('lti/tool/add/' . $this->course->id);
        }

        $this->selected_tool_id = '';
        if (count($this->global_tools) >= 1) {
            //Preselect the first tool:
            $this->selected_tool_id = $this->global_tools[0]->id;
        }
    }

    public function select_tool_redirect_action()
    {
        if (Request::isPost()) {
            CSRFProtection::verifyUnsafeRequest();
            $selected_tool_id = Request::get('selected_tool_id');
            if ($selected_tool_id === 'new') {
                //Redirect to the page to configure an LTI tool for the course:
                $this->redirect('lti/tool/add/' . $this->course->id);
            } else {
                //Load the selected tool and check if it can be used in the course.
                $selected_tool = LtiTool::find($selected_tool_id);
                if (!$selected_tool || $selected_tool->range_id !== 'global') {
                    PageLayout::postError(_('Das ausgewählte LTI-Tool kann nicht genutzt werden.'));
                    $this->redirect('course/lti/select_tool');
                    return;
                }
                $this->redirect('lti/tool/add/' . $this->course->id . '/' . $selected_tool->id);
            }
        } else {
            $this->redirect('course/lti/select_tool');
        }
    }

    public function consent_action(string $deployment_id)
    {
        $this->deployment = LtiDeployment::find($deployment_id);
        if (!$this->deployment) {
            PageLayout::postError(_('Die Einbindung eines LTI-Tools ist ungültig.'));
            return;
        }

        $this->privacy_settings = LtiToolPrivacySettings::findOneBySQL(
            'tool_id = :tool_id AND user_id = :user_id',
            ['tool_id' => $this->deployment->tool_id, 'user_id' => $GLOBALS['user']->id]
        );
        if (!$this->privacy_settings) {
            $this->privacy_settings = new LtiToolPrivacySettings();
            $this->privacy_settings->tool_id = $this->deployment->tool_id;
            $this->privacy_settings->user_id = $GLOBALS['user']->id;
        }

        if (Request::isPost()) {
            CSRFProtection::verifyUnsafeRequest();
            if (Request::submitted('save')) {
                if (!Request::get('confirmed')) {
                    PageLayout::postError(_('Ohne die aktive Zustimmung zur Weitergabe Ihrer personenbezogenen Daten können Sie das LTI-Tool nicht nutzen!'));
                    return;
                }
                //Save the privacy settings and redirect to the tool:
                $this->privacy_settings->accepted = '1';

                //Check which optional fields are allowed to be transmitted to the tool:
                $optional_field_list = Request::getArray('submit_optional_field', []);
                $optional_fields = [];
                if (array_key_exists('lang', $optional_field_list)) {
                    $optional_fields[] = 'lang';
                }
                if (array_key_exists('avatar_url', $optional_field_list)) {
                    $optional_fields[] = 'avatar_url';
                }
                $this->privacy_settings->allowed_optional_fields = implode(',', $optional_fields);
                //Store the privacy settings:
                $this->privacy_settings->store();
            }
            if (Request::isDialog()) {
                //Close the dialog:
                $this->response->add_header('X-Dialog-Close', '1');
            } elseif (Request::submitted('redirect_to_tool') && Request::submitted('save')) {
                //Redirect to the tool launch action, but only after the privacy settings have been saved:
                $this->redirect('course/lti/iframe/' . $this->deployment->id);
            } else {
                //Redirect to the LTI tool page of the course:
                $this->redirect('course/lti/index');
            }
        }
    }

    /**
     * Display the launch form for a tool as an iframe.
     */
    public function iframe_action(string $deployment_id)
    {
        $this->deployment = LtiDeployment::find($deployment_id);
        $this->show_data_protection_info = !LtiToolPrivacySettings::countBySQL(
            "`tool_id` = :tool_id AND `user_id` = :user_id AND `accepted` = 1",
            ['tool_id' => $this->deployment->tool_id, 'user_id' => $GLOBALS['user']->id]
        );
        if ($this->show_data_protection_info) {
            $this->redirect('course/lti/consent/' . $deployment_id, ['redirect_to_tool' => '1']);
            return;
        }

        if (!$this->show_data_protection_info) {
            //Redirect to the tool.
            $this->lti13a_mode = false;
            $lti_version = $this->deployment->getToolLtiVersion();
            if ($lti_version === '1.3a') {
                //LTI 1.3a
                $this->lti13a_mode = true;

                $lti_resource_link = new LtiResourceLink(
                    $this->deployment->tool_id . '_' . $this->deployment->id . '_' . $this->course_id,
                    [
                        'url' => $this->deployment->getLaunchURL(),
                        'title' => $this->deployment->title
                    ]
                );

                $registration = new Registration($this->deployment->tool);
                $builder = new LtiResourceLinkLaunchRequestBuilder();

                //The AGS URLs need several parameters:
                $ags_url_parameters = [
                    'cid'           => $this->course_id,
                    'tool_id'       => $this->deployment->tool_id,
                    'deployment_id' => $this->deployment->id,
                    'cancel_login'  => '1'
                ];

                //Build the message:
                $this->message = $builder->buildLtiResourceLinkLaunchRequest(
                    $lti_resource_link,
                    $registration,
                    $GLOBALS['user']->id,
                    $this->deployment->id,
                    [
                        PlatformManager::getLtiRoleClaimForStudipRole($GLOBALS['perm']->get_studip_perm($this->course_id))
                    ],
                    array_merge(
                        [
                            new ContextClaim(
                                $this->course_id,
                                ['http://purl.imsglobal.org/vocab/lis/v2/course#CourseOffering'],
                                $this->course->veranstaltungsnummer ?? '',
                                $this->course?->getFullName() ?? ''
                            ),
                            new AgsClaim(
                                [
                                    'https://purl.imsglobal.org/spec/lti-ags/scope/lineitem',
                                    'https://purl.imsglobal.org/spec/lti-ags/scope/result.readonly',
                                    'https://purl.imsglobal.org/spec/lti-ags/scope/score'
                                ],
                                $this->url_for('lti/ags/line_items', $ags_url_parameters),
                                $this->url_for('lti/ags/line_item', $ags_url_parameters)
                            )
                        ],
                        $this->deployment->getCustomLtiParameterArray(),
                    )
                );
            } else {
                //LTI 1.0/1.1
                $lti_link = $this->getLtiLink($this->deployment);
                $this->launch_url = $this->deployment->getLaunchURL();
                $this->launch_data = $lti_link->getBasicLaunchData();
                $this->signature = $lti_link->getLaunchSignature($this->launch_data);
            }
            $this->set_layout(null);
        }
    }

    /**
     * Edit the course settings.
     */
    public function config_action()
    {
        $course_config = CourseConfig::get($this->course_id);
        $this->personal_data_warning = $course_config->LTI_DATA_PROTECTION_COURSE_WARNING;
        if (empty($this->personal_data_warning)) {
            $this->personal_data_warning = Config::get()->LTI_DATA_PROTECTION_DEFAULT_WARNING;
        }
    }

    /**
     * Save the course settings.
     */
    public function save_config_action()
    {
        CSRFProtection::verifyUnsafeRequest();

        $course_config = CourseConfig::get($this->course_id);

        if (Request::bool('reset_warning')) {
            $course_config->delete('LTI_DATA_PROTECTION_COURSE_WARNING');
        } else {
            $course_config->store(
                'LTI_DATA_PROTECTION_COURSE_WARNING',
                trim(Request::get('personal_data_warning'))
            );
        }

        PageLayout::postSuccess(_('Die Einstellungen wurden gespeichert.'));
        $this->redirect('course/lti');
    }

    /**
     * Moves an LTI deployment in a course either up or down.
     *
     * @param string $deployment_id The ID of the deployment to be moved.
     *
     * @param string $direction 'up' for moving the deployment upwards or 'down' for downwards.
     */
    public function move_action(string $deployment_id, string $direction)
    {
        CSRFProtection::verifyUnsafeRequest();

        $deployment = LtiDeployment::find($deployment_id);
        if (!$deployment) {
            //Redirect and do nothing:
            $this->redirect('course/lti');
            return;
        }

        $new_position = 0;

        if ($direction === 'up') {
            $new_position = $deployment->position - 1;
        } else {
            $new_position = $deployment->position + 1;
        }

        //Find the deployment with the new position:
        $other_deployment = LtiDeployment::findByCourseAndPosition($this->course_id, $new_position);
        if ($other_deployment) {
            $other_deployment->position = $deployment->position;
            $other_deployment->store();
        }
        $deployment->position = $new_position;
        $deployment->store();

        $this->redirect('course/lti');
    }

    /**
     * Delete an LTI content block.
     *
     * @param   int $position   block position
     */
    public function delete_action($position)
    {
        CSRFProtection::verifyUnsafeRequest();

        $deployment = LtiDeployment::findByCourseAndPosition($this->course_id, $position);
        $deployment->delete();

        PageLayout::postSuccess(_('Der Abschnitt wurde gelöscht.'));
        $this->redirect('course/lti');
    }

    /**
     * Select a tool for adding a block via ContentItemSelectionRequest.
     */
    public function add_link_action()
    {
        //The permission check is done in the before filter.

        $this->tools = LtiTool::findBySQL("`deep_linking` = '1' AND `range_id` = 'global' ORDER BY `name` ASC");
        if (!$this->tools) {
            PageLayout::postError(_('Es sind keine globalen LTI-Tools konfiguriert.'));
            return;
        }
    }

    /**
     * Dispatch a ContentItemSelectionRequest to a specified LTI tool.
     */
    public function select_link_action($deployment_id = '')
    {
        $this->deployment = null;
        if ($deployment_id) {
            $this->deployment = LtiDeployment::find($deployment_id);
            if (!$this->deployment) {
                PageLayout::postError(_('Die Einbindung des LTI-Tools wurde nicht gefunden!'));
                return;
            }
            if ($this->deployment->course_id !== $this->course_id) {
                PageLayout::postError(_('Die Einbindung des LTI-Tools ist nicht für diese Veranstaltung bestimmt.'));
                return;
            }
            if (empty($this->deployment->options['unfinished_deep_linking'])) {
                PageLayout::postError(_('Die Einbindung des LTI-Tools ist bereits abgeschlossen.'));
                return;
            }
        }

        $this->tool = LtiTool::find(Request::int('tool_id'));
        if (!$this->tool) {
            PageLayout::postError(_('Das ausgewählte LTI-Tool wurde nicht gefunden.'));
            $this->redirect('course/lti/add_link');
            return;
        }
        if (!$this->tool->deep_linking) {
            PageLayout::postError(_('Das ausgewählte LTI-Tool unterstützt kein Deep Linking.'));
            $this->redirect('course/lti/add_link');
            return;
        }
    }

    public function process_select_link_action($deployment_id = '')
    {
        CSRFProtection::verifyUnsafeRequest();

        $this->deployment = null;
        if ($deployment_id) {
            $this->deployment = LtiDeployment::find($deployment_id);
            if (!$this->deployment) {
                PageLayout::postError(_('Die Einbindung des LTI-Tools wurde nicht gefunden!'));
                return;
            }
            if ($this->deployment->course_id !== $this->course_id) {
                PageLayout::postError(_('Die Einbindung des LTI-Tools ist nicht für diese Veranstaltung bestimmt.'));
                return;
            }
            if (empty($this->deployment->options['unfinished_deep_linking'])) {
                PageLayout::postError(_('Die Einbindung des LTI-Tools ist bereits abgeschlossen.'));
                return;
            }
        }

        $this->tool = null;
        if ($this->deployment) {
            $this->tool = $this->deployment->tool;
        } else {
            $this->tool = LtiTool::find(Request::int('tool_id'));
        }
        if (!$this->tool) {
            PageLayout::postError(_('Das ausgewählte LTI-Tool wurde nicht gefunden.'));
            $this->redirect('course/lti/add_link');
            return;
        }
        if (!$this->tool->deep_linking) {
            PageLayout::postError(_('Das ausgewählte LTI-Tool unterstützt kein Deep Linking.'));
            $this->redirect('course/lti/add_link');
            return;
        }

        if ($this->tool->lti_version === '1.3a') {
            //LTI 1.3a
            if ($this->deployment) {
                $builder = new DeepLinkingLaunchRequestBuilder();
                $message = $builder->buildDeepLinkingLaunchRequest(
                    PlatformManager::getDeepLinkingConfiguration($this->tool->id),
                    new Registration($this->deployment->tool),
                    $GLOBALS['user']->id,
                    null,
                    $this->deployment->id,
                    [PlatformManager::getLtiRoleClaimForStudipRole($GLOBALS['perm']->get_studip_perm($this->course_id))]
                );
                $this->render_text($message->toHtmlRedirectForm());
            } else {
                //Build an LTI deployment object and mark it as not configured
                //so that it can be displayed differently in the UI.
                $this->deployment = new LtiDeployment();
                $this->deployment->tool_id = $this->tool->id;
                $this->deployment->course_id = $this->course_id;
                $this->deployment->title = $this->tool->name;
                $this->deployment->options = ['unfinished_deep_linking' => true];
                if ($this->deployment->store() !== false) {
                    //Display the tool deployment data so that the user can enter
                    //them in the LTI tool.
                    PageLayout::postInfo(
                        _('Bitte tragen Sie die Daten zur Einbindung im LTI-Tool ein bevor sie fortfahren.')
                    );
                }
            }
        } else {
            //LTI 1.0/1.1
            $custom_parameters = explode("\n", $this->tool->custom_parameters);
            $content_item_return_url = $this->url_for('course/lti/save_link/' . $this->tool->id);

            // set up ContentItemSelectionRequest
            $lti_link = new LtiLink($this->tool->launch_url, $this->tool->consumer_key, $this->tool->consumer_secret, $this->tool->oauth_signature_method);
            $lti_link->setUser($GLOBALS['user']->id, 'Instructor', $this->tool->send_lis_person);
            $lti_link->setCourse($this->course_id);
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
                    list($key, $value) = explode('=', $param, 2);
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
     * Create a new LTI content block for the specified tool id.
     *
     * @param   int $tool_id    tool id
     */
    public function save_link_action($tool_id)
    {
        $tool = LtiTool::find($tool_id);

        if (!$tool) {
            PageLayout::postError(_('Das ausgewählte LTI-Tool wurde nicht gefunden.'));
            $this->redirect('course/lti/add_link');
            return;
        }
        if (!$tool->deep_linking) {
            PageLayout::postError(_('Das ausgewählte LTI-Tool unterstützt kein Deep Linking.'));
            $this->redirect('course/lti/add_link');
            return;
        }

        if ($tool->lti_version === '1.3a') {
            //LTI 1.3a

            $validator = new PlatformLaunchValidator(
                new RegistrationManager(),
                new NonceRepository(Studip\Cache\Factory::getCache())
            );
            $result = $validator->validateToolOriginatingLaunch($this->getPsrRequest());
            if ($result->hasError()) {
                PageLayout::postError($result->getError());
                $this->redirect('course/lti/add_link');
                return;
            }
            $all_lti_resources = (new ResourceCollectionFactory())->createFromClaim(
                $result->getPayload()->getDeepLinkingContentItems()
            );

            $lti_resource_links = $all_lti_resources->getByType(LtiResourceLinkInterface::TYPE);
            if (count($lti_resource_links) > 0) {
                $use_first_link = true;
                foreach ($lti_resource_links as $lti_resource_link) {
                    $deployment = null;
                    if ($use_first_link) {
                        //Recycle the deployment that has been created before
                        //for the course.
                        $deployment = LtiDeployment::findOneBySQL(
                            "`tool_id` = :tool_id AND `course_id` = :course_id
                            AND `options` LIKE '%unfinished_deep_linking%=%true'"
                        );
                        $use_first_link = false;
                    }
                    if (!$deployment) {
                        //If this is the first link, the deployment has been removed.
                        //In that case and if it is not the first link, a new deployment
                        //has to be created.
                        $deployment = new LtiDeployment();
                        $deployment->tool_id   = $tool->id;
                        $deployment->title     = $tool->name;
                        $deployment->course_id = $this->course_id;
                    }
                    $deployment->launch_url = $lti_resource_link->getUrl();
                    if (!empty($deployment->options['unfinished_deep_linking'])) {
                        unset($deployment->options['unfinished_deep_linking']);
                    }
                    $deployment->store();
                }
            }
        } else {
            $lti_msg = Request::get('lti_msg');
            $lti_errormsg = Request::get('lti_errormsg');
            $content_items = Request::get('content_items');
            $content_items = json_decode($content_items, true);

        if (!Studip\OAuth1::verifyRequest($this->getPsrRequest(), $tool->consumer_secret, '')) {
            throw new Exception('Could not verify request.');
        }

            if (is_array($content_items) && count($content_items['@graph'])) {
                // we only support selecting a single content item
                $item = $content_items['@graph'][0];

                $lti_data = new LtiDeployment();
                $lti_data->course_id = $this->course_id;
                $lti_data->position = LtiDeployment::countBySQL('course_id = ?', [$this->course_id]);
                $lti_data->title = (string) $item['title'];
                $lti_data->description = Studip\Markup::purifyHtml(Studip\Markup::markAsHtml($item['text']));
                $lti_data->tool_id = $tool_id;
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
        }

        if ($lti_errormsg) {
            PageLayout::postError($lti_errormsg);
        }

        $this->redirect('course/lti');
    }

    /**
     * Return an LtiLink object for the configured LTI content block.
     *
     * @param   LtiDeployment $lti_data data of LTI content block
     *
     * @return  LtiLink  LTI link representation
     */
    public function getLtiLink($lti_data)
    {
        $launch_url = $lti_data->getLaunchURL();
        $consumer_key = $lti_data->getConsumerKey();
        $consumer_secret = $lti_data->getConsumerSecret();
        $oauth_signature_method = $lti_data->getOauthSignatureMethod();

        $roles = $this->edit_perm ? 'Instructor' : 'Learner';
        $custom_parameters = explode("\n", $lti_data->getCustomParameters());
        $description = kill_format($lti_data->description);
        $lis_outcome_service_url = $this->url_for('course/lti/outcome/' . $lti_data->id, ['cid' => null]);
        $tc_profile_url = $this->url_for('course/lti/profile/' . $lti_data->id, ['cid' => null]);

        // set up launch request
        $lti_link = new LtiLink($launch_url, $consumer_key, $consumer_secret, $oauth_signature_method);
        $lti_link->setResource($lti_data->id, $lti_data->title, $description);
        $lti_link->setUser($GLOBALS['user']->id, $roles, $lti_data->getSendLisPerson());
        $lti_link->setCourse($lti_data->course_id);
        $lti_link->addVariable('ToolConsumerProfile.url', $tc_profile_url);
        $lti_link->addLaunchParameters([
            'launch_presentation_locale' => str_replace('_', '-', $_SESSION['_language']),
            'launch_presentation_document_target' => $lti_data->options['document_target'],
            'lis_outcome_service_url' => $lis_outcome_service_url,
            'lis_result_sourcedid' => $GLOBALS['user']->id
        ]);

        foreach ($custom_parameters as $param) {
            if (strpos($param, '=') !== false) {
                list($key, $value) = explode('=', $param, 2);
                $lti_link->addCustomParameter(trim($key), trim($value));
            }
        }

        return $lti_link;
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
     * @param   int $id    link id
     */
    public function outcome_action($id)
    {
        $lti_data = LtiDeployment::find($id);

        if (!Studip\OAuth1::verifyRequest($this->getPsrRequest(), $lti_data->getConsumerSecret(), '')) {
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
        $grade = new LtiGrade([$id, $user_id]);

        $this->message_id = uniqid();
        $this->message_ref = $message_id;
        $this->status_severity = 'status';
        $this->status_code = 'success';
        $this->operation = $operation;

        if (!CourseMember::exists([$lti_data->course_id, $user_id])) {
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
            $this->lti_data_array = LtiDeployment::findByCourse_id($this->course_id, 'ORDER BY position');
        } else {
            //Only load those deployments that are fully configured:
            $this->lti_data_array = LtiDeployment::findBySQL(
                "`course_id` = :course_id AND (`options` IS NULL OR `options` NOT LIKE '%unfinished_deep_linking%')
                ORDER BY `position`",
                ['course_id' => $this->course_id]
            );
        }

        if ($this->edit_perm) {
            $this->desc = Request::int('desc');
            $this->members = CourseMember::findByCourseAndStatus($this->course_id, 'autor');

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
            $lti_data_array = LtiDeployment::findByCourse_id($this->course_id, 'ORDER BY position');
        } else {
            //Only load those deployments that are fully configured:
            $lti_data_array = LtiDeployment::findBySQL(
                "`course_id` = :course_id AND (`options` IS NULL OR `options` NOT LIKE '%unfinished_deep_linking%')
                ORDER BY `position`",
                ['course_id' => $this->course_id]
            );
        }

        $columns = [_('Nachname'), _('Vorname')];

        // add one column for each LTI tool block
        foreach ($lti_data_array as $lti_data) {
            $columns[] = $lti_data->title;
        }

        $data = [$columns];
        setlocale(LC_NUMERIC, $_SESSION['_language'] . '.UTF-8');

        foreach (CourseMember::findByCourseAndStatus($this->course_id, 'autor') as $member) {
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
}
