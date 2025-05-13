<?= Studip\VueApp::create('courseware/ActivitiesApp')
                 ->withPlugin('CoursewareActivitiesApp', 'courseware-activities')
                 ->withVuexStore('courseware/courseware.module', 'courseware', [
                     'coursewareContext' => ['id' => (string) Context::getId(), 'type' => 'courses'],
                 ])
                 ->withVuexStore('courseware/structure.module', 'courseware-structure')
                 ->withVuexStore('courseware/courseware-activities.module', 'courseware-activities') ?>
