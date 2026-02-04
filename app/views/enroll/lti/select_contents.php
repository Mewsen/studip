<?php
/**
 * @var Enroll_LtiController $controller
 * @var ?SimpleORMapCollection<Course> $courses
 * @var string $callbackId
 * @var ?array $errors
 */
?>


<div class="lti">
    <? if(empty($errors)): ?>
        <? if (count($courses) > 0): ?>
        <div class="lti-resources">
            <form action="<?= $controller->link_for('enroll/lti/deeplink_callback') ?>" method="POST" class="default">
                <?= CSRFProtection::tokenTag() ?>
                <input type="hidden" name="callback_id" value="<?= $callbackId ?>" />
                <table class="default sortable-table">
                    <caption><?= _('Veröffentlichte Inhalte') ?></caption>
                    <thead>
                        <tr class="sortable">
                            <th scope="col" data-sort="text"><?= _('Name') ?></th>
                            <th scope="col"><?= _('Zum GradeBook hinzufügen') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <? foreach ($courses as $course) : ?>
                        <tr>

                            <td data-text="<?= htmlReady($course->getFullName()) ?>">
                                <input
                                    aria-label="<?= sprintf(_('"%s" auswählen'), htmlReady($course->getFullName())) ?>"
                                    type="checkbox"
                                    name="courses_id[]"
                                    value="<?= $course->id ?>"
                                />
                                <?= htmlReady($course->getFullName()) ?>
                            </td>

                            <td>
                                <input
                                    aria-label="<?= _('Zum GradeBook hinzufügen') ?>"
                                    type="checkbox"
                                    name="with_gradings[]"
                                    value="<?= $course->id ?>"
                                />
                            </td>
                        </tr>
                        <? endforeach ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3">
                                <button type="submit" class="button add">
                                    <?= _('Ausgewählte Inhalte hinzufügen') ?>
                                </button>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </form>
        </div>
        <? else: ?>
            <?= MessageBox::info(_('Es wurden keine Inhalte gefunden.')) ?>
        <? endif ?>
    <? else: ?>
        <?= $this->render_partial('enroll/lti/_errors', ['errors' => $errors]); ?>
    <? endif ?>
    <? if (empty($courses)|| !empty($errors)): ?>
        <form action="<?= $controller->link_for('enroll/lti/reset_account_mapping') ?>" method="POST" class="default use-utility-classes">
            <?= CSRFProtection::tokenTag() ?>
            <input type="hidden" name="callback_id" value="<?= $callbackId ?>" />
            <?= MessageBox::info(_('Sie können sich abmelden und es erneut mit einem anderen Konto versuchen.')) ?>
            <button type="submit" class="button flex items-center gap-5">
                <?= Icon::create('door-leave', Icon::DEFAULT_ROLE, ['aria-hidden' => 'true']) ?>
                <?= _('Abmelden und mit einem anderen Konto versuchen') ?>
            </button>
        </form>
    <? endif ?>
</div>
