<?php

class Lti_ToolController extends AuthenticatedController
{
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        $this->tool       = null;
        $this->deployment = null;
        $this->tool_id    = '';
        $this->range_id   = '';

        if (in_array($action, ['index', 'add', 'edit', 'delete'])) {
            $this->range_id = $args[0];
            $this->tool_id  = $args[1] ?? '';
            if (!$this->range_id || ($this->range_id === 'global' && !$GLOBALS['perm']->have_perm('root'))) {
                throw new AccessDeniedException();
            }
            if ($this->range_id !== 'global' && !$GLOBALS['perm']->have_studip_perm('tutor', $this->range_id)) {
                throw new AccessDeniedException();
            }
            if ($action === 'add' && !$this->tool_id) {
                $this->tool = new LtiTool();
            } else {
                if (!$this->tool_id) {
                    PageLayout::postError(_('Es wurde kein LTI-Tool angegeben.'));
                    return;
                }
                $this->tool = LtiTool::find($this->tool_id);
                if (!$this->tool) {
                    PageLayout::postError(_('Das angegebene LTI-Tool wurde nicht gefunden.'));
                }
            }
        }
    }

    public function index_action($range_id, $tool_id)
    {
        //$this->tool is created in the before-filter.
        if ($this->range_id !== 'global') {
            $this->deployment = LtiDeployment::findOneBySQL(
                '`tool_id` = :tool_id AND `course_id` = :range_id',
                ['tool_id' => $this->tool->id, 'range_id' => $this->range_id]
            );
        }
    }

    public function add_action($range_id, $tool_id = '')
    {
        //NOTE: The parameters are checked and processed in the before_filter.
        $this->addEditHandler('add');
    }

    public function edit_action($range_id, $tool_id)
    {
        //NOTE: The parameters are checked and processed in the before_filter.
        $this->addEditHandler('edit');
    }

    protected function addEditHandler($mode)
    {
        if (!$this->tool) {
            return;
        }
        $this->deployment = null;
        if ($this->tool->isNew()) {
            if ($this->range_id === 'global') {
                $this->tool->is_global = '1';
            } else {
                $this->deployment = new LtiDeployment();
                $this->deployment->course_id = $this->range_id;
            }
        } elseif ($this->range_id !== 'global') {
            $this->deployment = LtiDeployment::findOneBySQL(
                '`tool_id` = :tool_id AND `course_id` = :range_id',
                ['tool_id' => $this->tool->id, 'range_id' => $this->range_id]
            );
            if (!$this->deployment) {
                //Create a new deployment:
                $this->deployment = new LtiDeployment();
                $this->deployment->tool_id   = $this->tool->id;
                $this->deployment->course_id = $this->range_id;
            }
        }

        if (Request::isPost()) {
            CSRFProtection::verifyUnsafeRequest();
            if ($this->range_id === 'global') {
                //The admin page for editing global tools.
                $this->tool->name = trim(Request::get('name'));
                $this->tool->launch_url        = trim(Request::get('launch_url'));
            } else {
                //The page for editing tools configured in courses.
                $this->deployment->title       = trim(Request::get('name'));
                $this->deployment->description = trim(Request::get('description'));
                $this->deployment->launch_url  = trim(Request::get('launch_url'));
                $document_target = trim(Request::get('document_target'));
                if ($document_target === 'iframe') {
                    if (!is_array($this->deployment->options)) {
                        $this->deployment->options = [];
                    }
                    $this->deployment->options['document_target'] = $document_target;
                }
            }

            //If a deployment is present, the tool is not used in the global context.
            //If a tool is not used in the global context and the is_global flag is not set,
            //it is a tool that is only used for one course.
            if (!$this->deployment || ($this->deployment && $this->tool->is_global === '0')) {
                $this->tool->name = trim(Request::get('name'));
                $this->tool->launch_url = trim(Request::get('launch_url'));
                $this->tool->lti_version = Request::get('lti_version', '1.3a');
                if ($this->tool->lti_version === '1.3a') {
                    $this->tool->oauth_signature_method = 'sha256';
                    $this->tool->oidc_init_url = trim(Request::get('oidc_init_url'));
                    $this->tool->jwks_url = trim(Request::get('jwks_url'));
                    $this->tool->jwks_key_id = trim(Request::get('jwks_key_id'));
                    $this->tool->deep_linking_url = trim(Request::get('deep_linking_url'));
                } else {
                    //LTI 1.0/1.1:
                    $this->tool->oauth_signature_method = 'sha1';
                    $this->tool->consumer_key = trim(Request::get('consumer_key'));
                    $this->tool->consumer_secret = trim(Request::get('consumer_secret'));
                }
                $this->tool->send_lis_person = Request::int('send_lis_person', 0);

                $this->tool->custom_parameters = trim(Request::get('custom_parameters'));
                $tool_public_key = trim(Request::get('tool_public_key'));
                $errors = $this->tool->validate();
                if ($errors) {
                    PageLayout::postError(
                        _('Die folgenden Daten zum LTI-Tool sind fehlerhaft:'),
                        $errors
                    );
                    return;
                }
                if ($this->tool->lti_version === '1.3a' && !$tool_public_key && !$this->tool->jwks_url) {
                    PageLayout::postError(
                        _('Es wurde weder ein öffentlicher Schlüssel noch eine JWKS-URL zum Tool angegeben.')
                    );
                    return;
                }
                if ($this->tool->store() !== false) {
                    if ($this->deployment) {
                        $this->deployment->tool_id = $this->tool->id;
                    }
                } else {
                    PageLayout::postError(_('Das LTI-Tool konnte nicht gespeichert werden.'));
                    return;
                }
            }
            if ($this->deployment) {
                $this->deployment->store();
            }
            if ($this->tool->lti_version === '1.3a' && $tool_public_key) {
                if (!$this->tool->updatePublicKey($tool_public_key)) {
                    PageLayout::postError(
                        _('Der öffentliche Schlüssel des LTI-Tools konnte nicht gespeichert werden.')
                    );
                }
            }

            PageLayout::postSuccess(_('Das LTI-Tool wurde gespeichert.'));
            if (Request::isDialog()) {
                $this->response->add_header('X-Dialog-Close', '1');
                $this->render_nothing();
            } else {
                if ($this->range_id === 'global') {
                    $this->redirect('admin/lti');
                } else {
                    $this->redirect('course/lti');
                }
            }
        }
    }

    public function delete_action($range_id, $tool_id)
    {
        //NOTE: The parameters are checked and processed in the before_filter.
        if (Request::isPost()) {
            CSRFProtection::verifyUnsafeRequest();
            $deleted = false;
            $tool_name = $this->tool->name;
            if ($this->tool->is_global) {
                if ($range_id === 'global') {
                    $deleted = $this->tool->delete();
                } else {
                    //A tool shall be deleted from a course: Delete the deployment instead.
                    $deployment = LtiDeployment::findOneBySQL(
                        "`tool_id` = :tool_id AND `course_id` = :course_id",
                        ['tool_id' => $this->tool->id, 'course_id' => $range_id]
                    );
                    if ($deployment) {
                        $tool_name = $deployment->title;
                        $deleted = $deployment->delete();
                    } else {
                        PageLayout::postError(sprintf(_('Das LTI-Tool „%s“ ist in dieser Veranstaltung nicht vorhanden.'), $this->tool->name));
                        return;
                    }
                }
            } else {
                //Delete the tool directly:
                $deleted = $this->tool->delete();
            }
            if ($deleted !== false) {
                PageLayout::postSuccess(sprintf(_('Das LTI-Tool „%s“ wurde gelöscht.'), $tool_name));
            } else {
                PageLayout::postError(_('Das LTI-Tool „%s“ konnte nicht gelöscht werden.'), $tool_name);
            }
            if ($range_id === 'global') {
                //Redirect to the admin overview page.
                $this->redirect('admin/lti');
            } elseif (Course::exists($range_id)) {
                //Redirect to the LTI module of the course:
                $this->redirect('course/lti', ['cid' => $range_id]);
            }
        }
    }
}
