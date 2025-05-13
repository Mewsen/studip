<? if (!$unitsNotFound): ?>
    <?= Studip\VueApp::create('courseware/IndexApp')
                     ->withPlugin('CoursewareIndexApp', 'courseware-index')
                     ->withPlugin('StockImagesPlugin', 'stock-images')
                     ->withVuexStore('courseware/courseware.module', 'courseware', [
                         'coursewareContextSet' => ['id' => "$user_id", 'type' => 'users', 'unit' => "$unit_id"],
                         'coursewareCurrentElementSet' => "$entry_element_id",
                         'licensesSet' => json_decode($licenses),
                     ])
                     ->withVuexStore('courseware/structure.module', 'courseware-structure')
                     ->withVuexStore('file-chooser', 'file-chooser')
                     ->withVuexStore('courseware/courseware-tasks.module', 'tasks') ?>
<? endif; ?>
