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
        parent::before_filter($action, $args);
        $GLOBALS['perm']->check('admin');
        PageLayout::setTitle(_('Hilfetexte zum Login'));
        Navigation::activateItem('/admin/locations/login_faq');
    }

    public function index_action()
    {
        $actions = new ActionsWidget();
        $actions->addLink(
            _('Eintrag hinzufügen'),
            $this->url_for('admin/login_faq/add'),
            Icon::create('add')
        )->asDialog('size=auto');
        Sidebar::get()->addWidget($actions);

        //load all ContentTermsOfUse entries:
        $this->faq_entries = LoginFaq::findBySql('1');

    }

    public function add_action()
    {

    }
}
