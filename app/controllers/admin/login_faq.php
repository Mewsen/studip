<?php
/**
 * login_faq.php - controller class for administrating FAQ showing on login screen
 *
 * @author    Michaela Brückner <brueckner@data-quest.de>
 * @license   GPL2 or any later version
 * @category  Stud.IP
 * @package   admin
 * @since     5.5
 */
class Admin_LoginFaqController extends AuthenticatedController
{
    protected $_autobind = true;
    public function before_filter(&$action, &$args)
    {
        if ($action === 'add') {
            $action = 'edit';
        }

        parent::before_filter($action, $args);
        $GLOBALS['perm']->check('admin');
        PageLayout::setTitle(_('Hinweise zum Login'));
        Navigation::activateItem('/admin/locations/login_faq');
    }

    public function index_action()
    {
        $this->setupSidebar();
        $this->faq_entries = LoginFaq::findBySql('1');
    }


    public function edit_action(LoginFaq $entry = null)
    {
        PageLayout::setTitle(
            $entry->isNew() ? _('Hilfetext hinzufügen') : _('Hilfetext bearbeiten')
        );
    }

    public function store_action(LoginFaq $entry = null)
    {
        CSRFProtection::verifyRequest();
        $entry->setData([
            'title' => Request::get('title'),
            'description' => Request::get('description'),
        ]);

        if ($entry->store()) {
            PageLayout::postSuccess(_('Hilfetext wurde gespeichert.'));
        }
        $this->redirect($this->indexURL());
    }

    public function delete_action($faq_entry_id)
    {
        CSRFProtection::verifyRequest();

        if (LoginFaq::deleteBySQL('faq_id = ?', [$faq_entry_id])) {
            PageLayout::postSuccess(_("Der Hilfetext wurde gelöscht."));
        }
        $this->relocate($this->indexURL());
    }

    protected function setupSidebar()
    {
        $actions = new ActionsWidget();
        $actions->addLink(
            _('Hilfetext hinzufügen'),
            $this->edit_URL(),
            Icon::create('add')
        )->asDialog();
        Sidebar::get()->addWidget($actions);
    }
}
