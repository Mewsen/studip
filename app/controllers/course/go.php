<?php
/**
 * dispatch.php/course/go - startpage for course controller, formerly known as seminar_main.php
 *
 * @author  André Noack <noack@data-quest.de>
 * @license GPL2 or any later version
 * @since   6.0
 */

class Course_GoController extends AuthenticatedController
{
    protected $allow_nobody = true;

    public function __construct(\Trails\Dispatcher $dispatcher)
    {
        if (Request::option('to')) {
            Request::set('cid', Request::option('to'));
        }
        parent::__construct($dispatcher);
    }

    public function index_action()
    {
        $course_id = Context::getId();

        if (!$course_id && Request::get('cid')) {
            $archive_id = Request::get('cid');
            $archived = ArchivedCourse::find($archive_id);
            if ($archived) {
                $this->redirect(URLHelper::getURL('dispatch.php/search/archive', [
                    'criteria' => $archived->name,
                ]));
                return;
            }
        }

        if (!$course_id) {
            throw new CheckObjectException(_('Sie haben kein Objekt gewählt.'));
        }

        //set visitdate for course, when coming from my_courses
        if (Request::get('to')) {
            object_set_visit($course_id, 0);
        }


        // gibt es eine Anweisung zur Umleitung?
        $redirect_to = Request::get('redirect_to');
        if ($redirect_to) {
            if (!is_internal_url($redirect_to)) {
                throw new Exception('Invalid redirection');
            }
            if (str_starts_with($redirect_to, '#')) {
                $redirect_to = 'dispatch.php/course/go' . $redirect_to;
            }
            $this->redirect(URLHelper::getURL($redirect_to, ['cid' => $course_id]));
            return;
        }

        // der Nutzer zum ersten
        //Reiter der Veranstaltung weiter geleitet.
        if (Navigation::hasItem("/course")) {
            foreach (Navigation::getItem("/course")->getSubNavigation() as $index => $navigation) {
                if ($index !== 'admin') {
                    $this->redirect(URLHelper::getURL($navigation->getURL()));
                    return;
                }
            }
        }
    }
}
