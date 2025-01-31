<label>
    <input name="perm_read" type="checkbox" value="1" <? if ($folder->isReadable()) echo 'checked'; ?>>
    <?= _('Lesen (Dateien können heruntergeladen werden)') ?>
</label>
<label>
    <input name="perm_write" type="checkbox" value="1" <? if ($folder->isWritable()) echo 'checked'; ?>>
    <?= _('Schreiben (Dateien können hochgeladen werden)') ?>
</label>
<label>
    <input name="perm_visible" type="checkbox" value="1" <? if ($folder->isVisible()) echo 'checked'; ?>>
    <?= _('Sichtbarkeit (Ordner wird angezeigt)') ?>
</label>
