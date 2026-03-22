<?= Studip\VueApp::create('courseware/IndexApp')
                 ->withProps(['oer-title' => Config::get()->OER_TITLE])
                 ->withPlugin('CoursewareIndexApp', 'courseware-index')
                 ->withPlugin('StockImagesPlugin', 'stock-images')
                 ->withVuexStore('courseware/courseware.module', 'courseware', [
                     'coursewareContextSet' => ['id' => "$entry_element_id", 'type' => 'sharedusers'],
                     'coursewareCurrentElementSet' => "$entry_element_id",
                     'licensesSet' => json_decode($licenses),
                 ])
                 ->withVuexStore('courseware/structure.module', 'courseware-structure')
                 ->withVuexStore('file-chooser', 'file-chooser')
                 ->withVuexStore('courseware/courseware-tasks.module', 'tasks') ?>
