<?= Studip\VueApp::create('courseware/BookmarksApp')
                 ->withVuexStore('courseware/courseware.module', 'courseware', [
                     'coursewareContextSet' => ['id' => (string) $user_id, 'type' => 'users'],
                 ]) ?>
