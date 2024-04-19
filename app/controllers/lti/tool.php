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

        if (in_array($action, ['add', 'edit', 'delete'])) {
            $this->range_id = $args[0];
            $this->tool_id  = $args[1];
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
                    $this->render_nothing();
                    return;
                }
                $this->tool = LtiTool::find($this->tool_id);
                if (!$this->tool) {
                    PageLayout::postError(_('Das angegebene LTI-Tool wurde nicht gefunden.'));
                }
            }
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
        $this->deployment = null;
        if ($this->tool->isNew()) {
            if ($this->range_id !== 'global') {
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
                $this->tool->name = $this->deployment->title;
            }
            $this->tool->oidc_init_url     = trim(Request::get('oidc_init_url'));
            $this->tool->jwks_url          = trim(Request::get('jwks_url'));
            $this->tool->jwks_key_id       = trim(Request::get('jwks_key_id'));
            $this->tool->deep_linking_url  = trim(Request::get('deep_linking_url'));
            $this->tool->consumer_key      = trim(Request::get('consumer_key'));
            $this->tool->consumer_secret   = trim(Request::get('consumer_secret'));
            $this->tool->send_lis_person   = Request::int('send_lis_person', 0);
            $this->tool->oauth_signature_method = Request::get('oauth_signature_method', 'sha1');
            $this->tool->lti_version       = Request::get('lti_version', '1.3a');
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
            if (!$tool_public_key && !$this->tool->jwks_url) {
                PageLayout::postError(
                    _('Es wurde weder ein öffentlicher Schlüssel noch eine JWKS-URL zum Tool angegeben.')
                );
                return;
            }
            if ($this->tool->store()) {
                if ($this->deployment) {
                    $this->deployment->tool_id = $this->tool->id;
                }
            } else {
                PageLayout::postError(_('Das LTI-Tool konnte nicht gespeichert werden.'));
                return;
            }
            if ($this->deployment && $this->deployment->isDirty()) {
                $this->deployment->store();
            }
            if ($tool_public_key) {
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

    public function delete_action($tool_id, $range_id)
    {
        //NOTE: The parameters are checked and processed in the before_filter.
        if (Request::isPost()) {
            CSRFProtection::verifyUnsafeRequest();
            $tool_name = $this->tool->name;
            if ($this->tool->delete()) {
                PageLayout::postSuccess(sprintf(_('Das LTI-Tool „%s“ wurde gelöscht.'), $tool_name));
            } else {
                PageLayout::postError(_('Das LTI-Tool „%s“ konnte nicht gelöscht werden.'), $tool_name);
            }
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
