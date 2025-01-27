<?php
/**
 * @var array $courses
 */
?>
<? if (count($courses)) : ?>
    <table class="default width-1200">
        <caption>
            <?= _('Bitte wählen Sie den Kurs, in dem Sie die Klausur schreiben möchten:') ?>
        </caption>

        <thead>
            <tr>
                <th style="width: 5%;"></th>
                <th style="width: 65%;"><?= _('Name') ?></th>
                <th style="width: 30%;"><?= _('Inhalt') ?></th>
            </tr>
        </thead>

        <tbody>
            <? foreach ($courses as $course_id => $course_name) : ?>
                <? $nav = VipsModule::$instance->getIconNavigation($course_id, null, null) ?>
                <? if ($nav): ?>
                    <tr>
                        <td>
                            <?= CourseAvatar::getAvatar($course_id)->getImageTag(Avatar::SMALL) ?>
                        </td>
                        <td>
                            <a href="<?= URLHelper::getLink($nav->getURL(), ['cid' => $course_id]) ?>">
                                <?= htmlReady($course_name) ?>
                            </a>
                        </td>
                        <td>
                            <a href="<?= URLHelper::getLink($nav->getURL(), ['cid' => $course_id]) ?>">
                                <?= $nav->getImage()->asImg($nav->getLinkAttributes()) ?>
                            </a>
                        </td>
                    </tr>
                <? endif ?>
            <? endforeach ?>
        </tbody>
    </table>
<? else : ?>
    <? /* this should never be shown, but can be reached directly by URL */ ?>
    <?= MessageBox::info(_('Zur Zeit laufen keine Klausuren.')) ?>
<? endif ?>
