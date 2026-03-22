<? if (!$unitsNotFound): ?>
    <?= Studip\VueApp::create('courseware/IndexApp')
                     ->withPlugin('CoursewareIndexApp', 'courseware-index')
                     ->withPlugin('StockImagesPlugin', 'stock-images')
                     ->withVuexStore('courseware/courseware.module', 'courseware', [
                         'coursewareContextSet' => ['id' => (string) Context::getId(), 'type' => 'courses', 'unit' => "$unit_id"],
                         'coursewareCurrentElementSet' => "$entry_element_id",
                         'licensesSet' => json_decode($licenses),
                         'setFeedbackSettings' => json_decode($feedback_settings),
                     ])
                     ->withVuexStore('courseware/structure.module', 'courseware-structure')
                     ->withVuexStore('file-chooser', 'file-chooser')
                     ->withVuexStore('courseware/courseware-tasks.module', 'tasks') ?>
<? endif; ?>
