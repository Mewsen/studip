<?
/**
 * @var bool $lti_sharing_enabled Whether sharing via LTI is enabled (true) or not (false).
 */
?>
<? if ($lti_sharing_enabled) : ?>
    <?= MessageBox::info(_('Die Veranstaltung ist als LTI-Tool freigegeben.')) ?>
<? else : ?>
    <?= MessageBox::info(_('Die Veranstaltung ist nicht als LTI-Tool freigegeben.')) ?>
<? endif ?>
