<?php

require_once __DIR__.'/../courseware_controller.php';

use Courseware\StructuralElement;
use Courseware\Unit;

/**
 * @property ?string $entry_element_id
 * @property int $last_visitdate
 * @property mixed $course_id
 * @property mixed $courseware_progress_data
 * @property mixed $courseware_chapter_counter
 */
class Course_CoursewareController extends CoursewareController
{
    protected $_autobind = true;

    public function before_filter(&$action, &$args): void
    {
        parent::before_filter($action, $args);

        if (!Context::get()) {
            throw new CheckObjectException(_('Sie haben kein Objekt gewählt.'));
        }
        PageLayout::setTitle(Context::get()->getFullname() . ' - ' . _('Courseware'));
        PageLayout::setHelpKeyword('Basis.Courseware');

        checkObject();
        if (!Context::isCourse()) {
            throw new CheckObjectException(_('Es wurde keine passende Veranstaltung gefunden.'));
        }
        $this->studip_module = checkObjectModule('CoursewareModule', true);
        object_set_visit_module($this->studip_module->getPluginId());
        $this->last_visitdate = object_get_visit(Context::getId(), $this->studip_module->getPluginId());
        $this->licenses = $this->getLicenses();
        $this->oer_enabled = Config::get()->OERCAMPUS_ENABLED && $GLOBALS['perm']->have_perm(Config::get()->OER_PUBLIC_STATUS);
        $this->unitsNotFound = Unit::countBySql('range_id = ?', [Context::getId()]) === 0;
    }

    public function index_action(): void
    {
        $this->isTeacher = $GLOBALS['perm']->have_studip_perm(
            'tutor',
            Context::getId(),
            $GLOBALS['user']->id
        );
        Navigation::activateItem('course/courseware/shelf');
        $this->setIndexSidebar();
    }

    public function courseware_action($unit_id = null):  void
    {
        Navigation::activateItem('course/courseware/unit');
        if ($this->unitsNotFound) {
            PageLayout::postMessage(MessageBox::info(_('Es wurde kein Lernmaterial gefunden.')));
            return;
        }
        $user = User::findCurrent();
        $this->setCoursewareSidebar();

        /** @var array<mixed> $last */
        $last = UserConfig::get($user->id)->getValue('COURSEWARE_LAST_ELEMENT');
        $lastStructuralElement = \Courseware\StructuralElement::findOneById($last);

        if ($unit_id === null) {
            if (isset($lastStructuralElement) && $lastStructuralElement->canVisit($user)) {
                $this->redirectToFirstUnit('course', Context::getId(), $last);
            } else {
                $this->redirectToFirstUnit('course', Context::getId(), []);
            }
            return;
        }

        $this->entry_element_id = null;
        $this->unit_id = null;
        $unit = Unit::find($unit_id);
        if (isset($unit)) {
            if (isset($lastStructuralElement) && $lastStructuralElement->canVisit($user)) {
                $this->setEntryElement('course', $unit, $last, Context::getId());
            } else {
                $rootElement = [Context::getId() => $unit->structural_element->id];
                $this->setEntryElement('course', $unit, $rootElement, Context::getId());
            }
        }
    }

    public function tasks_action(): void
    {
        $this->isTeacher = $GLOBALS['perm']->have_studip_perm(
            'tutor',
            Context::getId(),
            $GLOBALS['user']->id
        );
        Navigation::activateItem('course/courseware/tasks');
        $this->setTasksSidebar();
    }

    public function activities_action(): void
    {
        Navigation::activateItem('course/courseware/activities');
        $this->setActivitiesSidebar();
    }

    public function pdf_export_action($element_id, $with_children): void
    {
        $element = \Courseware\StructuralElement::findOneById($element_id);
        $user = User::find($GLOBALS['user']->id);
        $this->render_pdf($element->pdfExport($user, $with_children), trim($element->title).'.pdf');
    }

    private function setIndexSidebar(): void
    {
        $sidebar = Sidebar::Get();
        $sidebar->addWidget(new VueWidget('courseware-action-widget'));
        SkipLinks::addIndex(_('Aktionen'), 'courseware-action-widget', 21);
        $sidebar->addWidget(new VueWidget('courseware-import-widget'));
    }

    private function setTasksSidebar(): void
    {
        $sidebar = Sidebar::Get();
        $sidebar->addWidget(new VueWidget('courseware-action-widget'));
        SkipLinks::addIndex(_('Aktionen'), 'courseware-action-widget', 21);
    }

    private function setActivitiesSidebar(): void
    {
        $sidebar = Sidebar::Get();
        $sidebar->addWidget(new VueWidget('courseware-activities-widget-filter-type'));
        $sidebar->addWidget(new VueWidget('courseware-activities-widget-filter-unit'));
    }
}
