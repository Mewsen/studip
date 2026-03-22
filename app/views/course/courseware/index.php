<?= Studip\VueApp::create('courseware/ShelfApp')
                 ->withPlugin('CoursewareShelfApp', 'courseware-shelf')
                 ->withPlugin('StockImagesPlugin', 'stock-images')
                 ->withVuexStore('courseware/courseware-shelf.module', 'courseware-shelf', [
                     'setContext' => ['id' => (string) Context::getId(), 'type' => 'courses'],
                     'setLicenses' => json_decode($licenses),
                     'setFeedbackSettings' => json_decode($feedback_settings),
                     'setUserIsTeacher' => (boolean) $isTeacher,
                 ]) ?>
