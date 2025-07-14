<div class="file_uploader">
    <div class="file_upload_window">
        <div class="errorbox" style="display: none;">
            <?= MessageBox::error('<span class="errormessage"></span>')?>
        </div>
        <ul class="filenames clean"></ul>
        <div class="uploadbar uploadbar-outer">
            <div class="uploadbar uploadbar-inner">
                <?= Icon::create('upload', Icon::ROLE_INFO_ALT)->asSvg(30) ?>
                <?= Icon::create('ufo', Icon::ROLE_INFO_ALT)->asSvg(30, ['class' => 'ufo']) ?>
            </div>

            <?= Icon::create('upload')->asSvg(30) ?>

            <span class="upload-progress"></span>
        </div>
    </div>
</div>
