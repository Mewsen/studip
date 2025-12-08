<?php

use Lti\Registration;

require_once __DIR__ . '/AdminBaseController.php';

class Admin_Lti_ToolsController extends LTI\AdminBaseController
{
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        Helpbar::get()->addPlainText('', _('Hier können Sie LTI-Tools konfigurieren. Diese müssen den LTI-Standard in Version 1.0/1.1 oder 1.3A unterstützen.'));
    }


    public function index_action()
    {
        $this->registrations = Registration::findBySQL("`role`='tool' AND `range_id` = 'global' ORDER BY `mkdate`, `name`");
    }
}
