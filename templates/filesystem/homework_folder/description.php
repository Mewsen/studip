<div style="font-style: italic">
    <?=_("Dieser Ordner ist ein Hausaufgabenordner. Es können nur Dateien eingestellt werden.")?>
</div>

<? if ($folderdata['description']) : ?>
<hr>
    <div>
        <?= formatReady($folderdata['description']) ?>
    </div>
<? endif ?>
