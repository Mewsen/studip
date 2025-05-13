<?= Studip\VueApp::create('courseware/TasksApp')
                 ->withPlugin('CoursewareTasksApp', 'courseware-tasks')
                 ->withVuexStore('courseware/courseware.module', 'courseware', [
                     'coursewareContextSet' => ['id' => (string) Context::getId(), 'type' => 'courses'],
                     'setUserIsTeacherInCourse' => (boolean) $isTeacher,
                 ])
                 ->withVuexStore('courseware/courseware-tasks.module', 'tasks')
                 ->withVuexStore('courseware/structure.module', 'courseware-structure') ?>
