<?php
/**
 * @var BlubberController $controller
 * @var BlubberThread $thread
 * @var Contact[] $contacts
 */
?>

<form class="default" action="<?= $controller->compose($thread ? $thread->getId() : null) ?>" method="post" data-dialog>

    <?= CSRFProtection::tokenTag() ?>

    <div class="file_select_possibilities" <?= !$thread ? "" : 'style="display: none;"' ?>>
        <div>
            <a href="#"
               onclick="$('.file_select_possibilities').hide(); $('.private_blubber_composer').show(); return false;">
                <?= Icon::create('group3')->asImg(50) ?>
                <?= _('Kontakte') ?>
            </a>

            <a href="<?= $controller->link_for('blubber/index/global') ?>">
                <?= Icon::create('globe')->asImg(50) ?>
                <?= _('Öffentlich') ?>
            </a>

            <? if (!$GLOBALS['perm']->have_perm('admin')) : ?>
                <a href="#"
                   onclick="$('.file_select_possibilities').hide(); $('.course_blubber_composer').show(); return false;">
                    <?= Icon::create('seminar')->asImg(50) ?>
                    <?= _('Veranstaltung') ?>
                </a>
            <? endif ?>
        </div>
    </div>

    <div class="course_blubber_composer" style="display: none;">
        <? if (!$GLOBALS['perm']->have_perm('admin')) : ?>
            <ul class="clean">
                <? foreach (CourseMember::findBySQL("INNER JOIN seminare USING (Seminar_id) WHERE user_id = ? ORDER BY seminare.name ASC", [$GLOBALS['user']->id]) as $member) : ?>
                    <li>
                        <a href="<?= $controller->to_course($member['seminar_id']) ?>">
                            <?= CourseAvatar::getAvatar($member['seminar_id'])->getImageTag(Avatar::SMALL) ?>
                            <?= htmlReady($member->course['name']) ?>
                        </a>
                    </li>
                <? endforeach ?>
            </ul>
        <? endif ?>
    </div>

    <div class="private_blubber_composer" style="display: none;">

        <? if (!empty($contacts)) : ?>
            <div class="blubber_composer_select_container">
            <span class="container">
                <label>
                    <?= _('Kontakte') ?>
                    <select name="user_ids[]" class="select2" id="blubber_contacts" multiple>
                    <? foreach ($contacts as $contact) : ?>
                        <option value="<?= htmlReady($contact->user_id) ?>"
                                data-avatar="<?= htmlReady(Avatar::getAvatar($contact['user_id'])->getImageTag(Avatar::SMALL)) ?>">
                            <?= htmlReady($contact->friend->getFullName()) ?>
                        </option>
                    <? endforeach ?>
                </select>
                </label>
            </span>

                <a href="" onClick="$('#blubber_contacts').trigger('focus').select2('open'); return false;">
                    <?= Icon::create('search')->asImg(['class' => 'text-bottom']) ?>
                </a>
                <a href="" onClick="$('#blubber_contacts').val(null).trigger('change'); return false;">
                    <?= Icon::create('decline',)->asImg(['class' => "text-bottom"]) ?>
                </a>
            </div>
        <? endif ?>
        <script>
            jQuery(function ($) {
                let format = function (state) {
                    if (!state.id) { // optgroup
                        return state.text;
                    }
                    let avatar = $(state.element).data('avatar');
                    let span = $('<span>');
                    span.text(state.text);
                    $(avatar).prependTo(span);
                    return span;
                };
                $('#blubber_contacts').select2({
                    width: '100%',
                    templateResult: format,
                    templateSelection: format
                });
                STUDIP.Blubber.Composer.init();
            });
        </script>
        <div class=".more_persons">
            <?= _('Weitere Personen') ?>

            <ul class="clean" id="blubber_contact_ids">
                <li v-for="user in users" :key="user.user_id">
                    <input type="hidden" name="user_ids[]" :value="user.user_id">
                    <span>{{ user.name }}</span>
                    <a href="#" @click.prevent="removeUser">
                        <studip-icon shape="trash"></studip-icon>
                    </a>
                </li>
            </ul>
            <quicksearch :searchtype="quicksearch" name="qs" @input="addRange" :placeholder="$gettext('Suchen')"></quicksearch>
            <div class="blubber_composer_select_container">
                <?= QuickSearch::get('search_user_id', new StandardSearch('user_id'))
                    ->setInputStyle('width: 90%')
                    ->fireJSFunctionOnSelect('STUDIP.Blubber.Composer.vue.addUser')->render() ?>

                <a href="" onClick="$('input[name=search_user_id_parameter]').trigger('focus'); return false;">
                    <?= Icon::create('search')->asImg(['class' => "text-bottom"]) ?>
                </a>
                <a href="" onClick="STUDIP.Blubber.Composer.vue.clearUsers(); return false;">
                    <?= Icon::create('decline')->asImg(['class' => "text-bottom"]) ?>
                </a>
            </div>
        </div>

    </div>

    <div data-dialog-button>
        <?= Studip\Button::create($thread ? _('Speichern') : _('Erstellen'), 'submit_blubber') ?>
    </div>
</form>
