<?= Studip\VueApp::create('courseware/ShelfApp')
                 ->withPlugin('CoursewareShelfApp', 'courseware-shelf')
                 ->withPlugin('StockImagesPlugin', 'stock-images')
                 ->withVuexStore('courseware/courseware-shelf.module', 'courseware-shelf', [
                     'setContext' => ['id' => "$user_id", 'type' => 'users'],
                     'setLicenses' => json_decode($licenses),
                 ])
?>
