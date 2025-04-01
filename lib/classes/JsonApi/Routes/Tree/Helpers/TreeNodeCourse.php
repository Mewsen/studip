<?php
namespace JsonApi\Routes\Tree\Helpers;

use Config;
use Course;
use CourseMember;
use Icon;

final class TreeNodeCourse
{
    private Course $course;

    public function __construct(Course $course)
    {
        $this->course = $course;
    }

    public function getCourse(): Course
    {
        return $this->course;
    }

    public function getId(): string
    {
        return $this->course->id;
    }

    public function getSemesterText(): string
    {
        return $this->course->semester_text;
    }

    public function getDates(): string
    {
        return $this->course->getAllDatesInSemester()->toHtml(false, true);
    }

    public function getLecturers(): array
    {
        return $this->course->getMembersWithStatus('dozent', true)
            ->orderBy('position, nachname, vorname')
            ->map(function (CourseMember $member): array {
                return [
                    'id'       => $member->user_id,
                    'username' => $member->username,
                    'name'     => $member->getUserFullname()
                ];
            });
    }

    public function getAdmissionState(): ?array
    {
        if (!Config::get()->getValue('COURSE_SEARCH_SHOW_ADMISSION_STATE')) {
            return null;
        }

        switch (\GlobalSearchCourses::getStatusCourseAdmission($this->course->id, $this->course->admission_prelim)) {
            case 1:
                return [
                    'icon' => 'decline-circle',
                    'role' => Icon::ROLE_STATUS_YELLOW,
                    'info' => _('Eingeschränkter Zugang')
                ];
            case 2:
                return [
                    'icon' => 'decline-circle',
                    'role' => Icon::ROLE_STATUS_RED,
                    'info' => _('Kein Zugang')
                ];
            default:
                return [
                    'icon' => 'check-circle',
                    'role' => Icon::ROLE_STATUS_GREEN,
                    'info' => _('Uneingeschränkter Zugang')
                ];
        }
    }
}
