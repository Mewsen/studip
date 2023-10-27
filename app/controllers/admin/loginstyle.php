<?php
/**
 * loginstyle.php - controller class for administration of login background pics
 *
 * @author    Thomas Hackl <thomas.hackl@uni-passau.de>
 * @license   GPL2 or any later version
 * @category  Stud.IP
 * @package   admin
 * @since     4.0
 */

class Admin_LoginStyleController extends AuthenticatedController
{
    /**
     * common tasks for all actions
     *
     * @param String $action Action that has been called
     * @param Array  $args   List of arguments
     */
    public function before_filter(&$action, &$args)
    {

        if ($action === 'add_faq') {
            $action = 'edit_faq';
        }

        parent::before_filter($action, $args);

        // user must have root permission
        $GLOBALS['perm']->check('root');

        //setting title and navigation
        PageLayout::setTitle(_('Hintergrundbilder für den Startbildschirm'));
        Navigation::activateItem('/admin/locations/loginstyle');

        $sidebar = Sidebar::get();
        $views = new ViewsWidget();
        $views->addLink(
            _('Bilder'),
            $this->url_for('admin/loginstyle')
        )->setActive($action === 'index');

        $views->addLink(
            _('Hinweise zum Login'),
            $this->url_for('admin/loginstyle/login_faq')
        )->setActive($action === 'login_faq');

        $sidebar->addWidget($views);


    }

    /**
     * Display all available background pictures
     */
    public function index_action()
    {
        // Setup sidebar
        $this->setSidebar('index');
        $this->pictures = LoginBackground::findBySQL("1 ORDER BY `background_id`");
    }

    /**
     * Provides a form for uploading a new picture.
     */
    public function newpic_action()
    {
    }

    /**
     * Adds a new picture ass possible login background.
     */
    public function add_pic_action()
    {
        CSRFProtection::verifyRequest();
        $success = 0;
        foreach ($_FILES['pictures']['name'] as $index => $filename) {
            if ($_FILES['pictures']['error'][$index] !== UPLOAD_ERR_OK) {
                continue;
            }

            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $extension = strtolower($extension);
            if (!in_array($extension, ['gif', 'jpeg', 'jpg', 'png'])) {
                continue;
            }

            $entry = new LoginBackground();
            $entry->filename = $filename;
            $entry->desktop = Request::int('desktop', 0);
            $entry->mobile = Request::int('mobile', 0);
            if ($entry->store()) {
                $destination = LoginBackground::getPictureDirectory() . DIRECTORY_SEPARATOR
                             . $entry->id . '.' . $extension;
                if (move_uploaded_file($_FILES['pictures']['tmp_name'][$index], $destination)) {
                    $success++;
                } else {
                    $entry->delete();
                }
            }
        }

        if ($success > 0) {
            PageLayout::postSuccess(sprintf(ngettext(
                'Ein Bild wurde hochgeladen.',
                '%u Bilder wurden hochgeladen',
                $success
            ), $success));
        }

        $fail = count($_FILES['pictures']['name']) - $success;
        if ($fail > 0) {
            PageLayout::postError(sprintf(ngettext(
                'Ein Bild konnte nicht hochgeladen werden.',
                '%u Bilder konnten nicht hochgeladen werden.',
                $fail
            ), $fail));
        }
        $this->relocate('admin/loginstyle');
    }

    /**
     * Deletes the given picture.
     * @param $id the picture to delete
     */
    public function delete_pic_action($id)
    {
        $pic = LoginBackground::find($id);
        if ($pic->in_release) {
            PageLayout::postError(_('Dieses Bild wird vom System mitgeliefert und kann daher nicht gelöscht werden.'));
        } elseif ($pic->delete()) {
            PageLayout::postSuccess(_('Das Bild wurde gelöscht.'));
        } else {
            PageLayout::postError(_('Das Bild konnte nicht gelöscht werden.'));
        }

        $this->relocate('admin/loginstyle');
    }

    /**
     * (De-)activate the given picture for given view.
     * @param $id the picture to change activation for
     * @param $view one of 'desktop', 'mobile', view to (de-) activate picture for
     * @param $newStatus new activation status for given view.
     */
    public function activation_action($id, $view, $newStatus)
    {
        $pic = LoginBackground::find($id);
        $pic->$view = $newStatus;
        if ($pic->store()) {
            PageLayout::postSuccess(_('Der Aktivierungsstatus wurde gespeichert.'));
        } else {
            PageLayout::postSuccess(_('Der Aktivierungsstatus konnte nicht gespeichert werden.'));
        }
        $this->relocate('admin/loginstyle');
    }


    /**
     * FAQ part of login page
     */
    public function login_faq_action()
    {

        PageLayout::setTitle(_('Hinweise zum Login für den Startbildschirm'));

        $this->setSidebar('login_faq');
        $this->faq_entries = LoginFaq::findBySql('1');
    }

    public function edit_faq_action()
    {
        $id = Request::get('entry_id') ?: null;
        $this->entry = new LoginFaq($id);

        PageLayout::setTitle(
            $this->entry->isNew() ? _('Hilfetext hinzufügen') : _('Hilfetext bearbeiten')
        );

    }

    public function store_faq_action()
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
                $this->relocate('admin/loginstyle/login_faq');
            }
        }
    }

    public function delete_faq_action($faq_entry_id)
    {
        CSRFProtection::verifyRequest();

        LoginFaq::deleteBySQL('faq_id = ?', [$faq_entry_id]);
        PageLayout::postSuccess(sprintf(
            _("Der Hilfetext wurde gelöscht."), htmlReady(Request::get("id"))
        ));

        $redirect_url = $this->url_for('admin/loginstyle/login_faq');
        $this->relocate($redirect_url);
    }

    /**
     * Adds the content to sidebar
     */
    protected function setSidebar($action)
    {
        $sidebar = Sidebar::get();

        $links = new ActionsWidget();

        if ($action === 'index') {
            $links->addLink(
                _('Bild hinzufügen'),
                $this->url_for('admin/loginstyle/newpic'),
                Icon::create('add', 'clickable')
            )->asDialog('size=auto');
        } else if ($action === 'login_faq') {
            $links->addLink(
                _('Hilfetext hinzufügen'),
                $this->url_for('admin/loginstyle/add_faq'),
                Icon::create('add')
            )->asDialog();

        }




        $sidebar->addWidget($links);

    }
}
