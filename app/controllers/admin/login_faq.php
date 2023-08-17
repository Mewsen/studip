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
    public function before_filter(&$action, &$args)
    {
        if ($action === 'add') {
            $action = 'edit';
        }

        parent::before_filter($action, $args);
        $GLOBALS['perm']->check('admin');
        PageLayout::setTitle(_('Hilfetexte zum Login'));
        Navigation::activateItem('/admin/locations/login_faq');
    }

    public function index_action()
    {
        $this->setupSidebar();
        $this->faq_entries = LoginFaq::findBySql('1');
    }


    public function edit_action()
    {
        $id = Request::get('entry_id') ?: null;
        $this->entry = new LoginFaq($id);

        PageLayout::setTitle(
            $this->entry->isNew() ? _('Hilfetext hinzufügen') : _('Hilfetext bearbeiten')
        );


    }

    public function store_action()
    {
        if (Request::isPost()) {
            CSRFProtection::verifyRequest();
            $id = Request::get('id') ?: null; // Convert possible empty string to null
            $entry = new LoginFaq($id);
            $entry->id = Request::get('id');
            $entry->title = Request::get('title');
            $entry->description = Request::get('description');

            if ($entry->store()) {
                PageLayout::postSuccess(_('Hilfetext wurde gespeichert.'));
                $this->redirect('admin/login_faq/index');

            }

        }

    }

    public function delete_action()
    {
        $this->faq_entry = new LoginFaq(Request::get("id"));
        if (Request::isPost()) {
            $this->faq_entry->delete();
            PageLayout::postSuccess(sprintf(
                _("Der Hilfetext wurde gelöscht."), htmlReady(Request::get("id"))
            ));
        } else {
            throw new AccessDeniedException();
        }
        $this->redirect("admin/login_faq/index");
    }

    protected function setupSidebar()
    {
        $actions = new ActionsWidget();
        $actions->addLink(
            _('Hilfetext hinzufügen'),
            $this->url_for('admin/login_faq/add'),
            Icon::create('add')
        )->asDialog();
        Sidebar::get()->addWidget($actions);

    }
}
