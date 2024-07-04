<?php
/**
 * @var Admin_CoursesController $controller
 * @var int $count_courses
 * @var Semester $semester
 * @var array $fields
 * @var array $activated_fields
 * @var string $sortby
 * @var string $sortflag
 * @var array $activeSidebarElements
 * @var int $max_show_courses
 * @var array $store_data
 */

$unsortable_fields = [
    'avatar',
    'room_time',
    'contents'
];
?>
<? if (empty($insts)): ?>
    <?= MessageBox::info(sprintf(_('Sie wurden noch keinen Einrichtungen zugeordnet. Bitte wenden Sie sich an einen der zuständigen %sAdministratoren%s.'), '<a href="' . URLHelper::getLink('dispatch.php/siteinfo/show') . '">', '</a>')) ?>
<? else: ?>
    <?= Studip\VueApp::create('AdminCourses')
            ->withProps([
                'show-complete' => (bool) Config::get()->ADMIN_COURSES_SHOW_COMPLETE,
                'fields' => $fields,
                'unsortable-fields' => $unsortable_fields,
                'max-courses' => (int) $max_show_courses,
                'sort-by' => $sortby,
                'sort-flag' => $sortflag,
            ])
            ->withStore('AdminCoursesStore', 'admincourses', $store_data) ?>

<? endif; ?>
