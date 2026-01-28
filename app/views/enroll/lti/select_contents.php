<?php
use Lti\UserIdentityMapping;
use Lti\Enum\UserIdentityMappingContext;

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
                    <colgroup>
                        <col style="width: 10%" />
                        <col />
                    </colgroup>
                    <thead>

                        <tr class="sortable">
                            <th scope="col" data-sort="false">
                                <input
                                    aria-label="<?= _('Inhalt auswählen') ?>"
                                    type="checkbox"
                                    name="all_courses_id"
                                    value="1"
                                    data-proxyfor=":checkbox[name^=courses]"
                                >
                            </th>
                            <th scope="col" data-sort="text"><?= _('Name') ?></th>
                        </tr>

                    </thead>
                    <tbody>
                        <? foreach ($courses as $course) : ?>
                        <tr>
                            <td>
                                <input
                                    aria-label="<?= sprintf(_('Inhalt "%s" auswählen'), htmlReady($course->getFullName())) ?>"
                                    type="checkbox"
                                    name="courses_id[]"
                                    value="<?= $course->id ?>"
                                />
                            </td>
                            <td data-text="<?= htmlReady($course->getFullName()) ?>">
                                <?= htmlReady($course->getFullName()) ?>
                            </td>
                        </tr>
                        <? endforeach ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2">
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
