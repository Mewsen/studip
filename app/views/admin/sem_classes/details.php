<?php

/**
 *  Copyright (c) 2012  Rasmus Fuhse <fuhse@data-quest.de>
 *
 *  This program is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU General Public License as
 *  published by the Free Software Foundation; either version 2 of
 *  the License, or (at your option) any later version.
 *
 * @var array $sem_class
 * @var array $modules
 */

?>
<form action="<?= $controller->link_for('admin/sem_classes/save') ?>" method="post" class="default attribute_table collapsable">
    <input type="hidden" name="sem_class_id" value="<?= Request::int("id") ?>">
    <fieldset>
        <legend>
            <?= _('Veranstaltungskategorie bearbeiten') ?>
        </legend>

        <label>
            <span class="required"><?= _("Name der Veranstaltungskategorie") ?></span>
            <?= I18N::input('sem_class_name', $sem_class->name, ['required' => true]) ?>
        </label>

        <label>
            <span><?= _('Beschreibungstext für die Suche') ?></span>
            <?= I18N::textarea('sem_class_description', $sem_class->description) ?>
        </label>

        <section>
            <span><?= _("Veranstaltungstypen") ?></span>

            <? foreach ($sem_class->getSemTypes() as $id => $sem_type) : ?>
                <? $count = $sem_type->countSeminars() ?>
                <br>
                <div style="display: inline-block; max-width: 48em; width: 100%;">
                    <label>
                        <span><?= sprintf(_('Typ %d (%d Veranstaltungen)'), $id, $count) ?></span>
                        <?= I18N::input("sem_type_$id", $sem_type->name) ?>
                    </label>
                </div>
                <? if ($count === 0): ?>
                    <?= Icon::create('trash')->asInput(['class' => 'text-bottom', 'formaction' => $controller->url_for('admin/sem_classes/delete_sem_type', $id)]) ?>
                <? endif ?>
            <? endforeach ?>
        </section>

        <? foreach (["dozent","tutor","autor"] as $role) : ?>
        <section>
            <?= sprintf(_("Titel der %s"), $GLOBALS['DEFAULT_TITLE_FOR_STATUS'][$role][1]) ?>

            <label>
                <input type="radio" name="title_<?= $role ?>_isnull" value="1"<?= !$sem_class['title_'.$role] && !$sem_class['title_'.$role.'_plural'] ? " checked" : ""?>>
                <?= sprintf(_("Systemdefault (%s)"), htmlReady(implode("/", $GLOBALS['DEFAULT_TITLE_FOR_STATUS'][$role]))) ?>
            </label>

            <div class="hgroup">
                <label>
                    <input type="radio" name="title_<?= $role ?>_isnull" value="0"<?= $sem_class['title_'.$role] || $sem_class['title_'.$role.'_plural'] ? " checked" : ""?>>
                    <input placeholder="<?= htmlReady($GLOBALS['DEFAULT_TITLE_FOR_STATUS'][$role][0]) ?>" title="<?= _("Singular") ?>" type="text" name="title_<?= $role ?>" value="<?= htmlReady($sem_class['title_'.$role]) ?>">
                    <input placeholder="<?= htmlReady($GLOBALS['DEFAULT_TITLE_FOR_STATUS'][$role][1]) ?>" title="<?= _("Plural") ?>" type="text" name="title_<?= $role ?>_plural" value="<?= htmlReady($sem_class['title_'.$role.'_plural']) ?>">
                </label>
            </div>
        </section>
        <? endforeach ?>
    </fieldset>

    <fieldset>
        <legend>
            <?= _("Voreinstellungen beim Anlegen einer Veranstaltung") ?>
        </legend>

        <label>
            <?= _("Lesbar für Nutzer") ?>
            <select name="default_read_level">
                <option value="0"<?= $sem_class['default_read_level'] == 0 ? " selected" : "" ?>><?= _("Unangemeldet an Veranstaltung") ?></option>
                <option value="1"<?= $sem_class['default_read_level'] == 1 ? " selected" : "" ?>><?= _("Angemeldet an Veranstaltung") ?></option>
            </select>
        </label>

        <label>
            <?= _("Schreibbar für Nutzer") ?>
            <select name="default_write_level">
                <option value="0"<?= $sem_class['default_write_level'] == 0 ? " selected" : "" ?>><?= _("Unangemeldet an Veranstaltung") ?></option>
                <option value="1"<?= $sem_class['default_write_level'] == 1 ? " selected" : "" ?>><?= _("Angemeldet an Veranstaltung") ?></option>
            </select>
        </label>

        <label>
            <?= _("Anmeldemodus") ?>
            <select name="admission_prelim_default">
                <option value="0"<?= $sem_class['admission_prelim_default'] == 0 ? " selected" : "" ?>><?= _("Direkter Eintrag") ?></option>
                <option value="1"<?= $sem_class['admission_prelim_default'] == 1 ? " selected" : "" ?>><?= _("Vorläufiger Eintrag") ?></option>
            </select>
        </label>

        <label>
            <?= _("Anmeldung gesperrt") ?>
            <select name="admission_type_default">
                <option value="0"<?= $sem_class['admission_type_default'] == 0 ? " selected" : "" ?>><?= _("Nein") ?></option>
                <option value="3"<?= $sem_class['admission_type_default'] == 3 ? " selected" : "" ?>><?= _("Ja") ?></option>
            </select>
        </label>
    </fieldset>

    <fieldset>
        <legend>
            <?= _("Anzeige") ?>
        </legend>

        <label>
            <input type="checkbox" name="visible" value="1"<?= $sem_class['visible'] ? " checked" : "" ?>>
            <?= _("Sichtbar") ?>
        </label>

        <label>
            <input type="checkbox" name="show_browse" value="1"<?= $sem_class['show_browse'] ? " checked" : "" ?>>
            <?= _("Zeige im Veranstaltungsbaum an.") ?>
        </label>

        <label>
            <input type="checkbox" name="show_raumzeit" value="1"<?= $sem_class['show_raumzeit'] ? " checked" : "" ?>>
            <?= _("Zeige Raum-Zeit-Seite an.") ?>
        </label>
    </fieldset>

    <fieldset>
        <legend>
            <?= _("Sonstiges") ?>
        </legend>

        <label>
            <input type="checkbox" name="studygroup_mode" value="1"<?= $sem_class['studygroup_mode'] ? " checked" : "" ?>>
            <?= _("Studentische Arbeitsgruppe") ?>
        </label>

        <label>
            <input type="checkbox" name="only_inst_user" value="1"<?= $sem_class['only_inst_user'] ? " checked" : "" ?>>
            <?= _("Nur Nutzer der Einrichtungen sind erlaubt.") ?>
        </label>

        <label>
            <input type="checkbox" name="bereiche" value="1"<?= $sem_class['bereiche'] ? " checked" : "" ?>>
            <?= _("Muss Studienbereiche haben (falls nein, darf es keine haben)") ?>
        </label>

        <label>
            <input type="checkbox" name="module" value="1"<?= $sem_class['module'] ? " checked" : "" ?>>
            <?= _("Kann Modulen zugeordnet werden.") ?>
        </label>

        <label>
            <input type="checkbox" name="course_creation_forbidden" value="1"<?= $sem_class['course_creation_forbidden'] ? " checked" : "" ?>>
            <?= _("Anlegeassistent für diesen Typ sperren.") ?>
        </label>

        <label>
            <input type="checkbox" name="is_group" value="1" <?= $sem_class['is_group'] ? 'checked' : '' ?>>
            <?= _('Kann Unterveranstaltungen haben') ?>
        </label>

        <label>
            <input type="checkbox" name="unlimited_forbidden" value="1" <?= $sem_class['unlimited_forbidden'] ? 'checked' : '' ?>>
            <?= _('Unbegrenzte Laufzeit verbieten') ?>
        </label>

        <label>
            <input type="checkbox" name="admission_turnout_mandatory" value="1" <?= $sem_class['admission_turnout_mandatory'] ?  'checked' : '' ?>>
            <?= _('Geplante Teilnehmendenzahl muss angegeben werden') ?>
        </label>

        <label>
            <?= _('Kurzer Beschreibungstext zum Anlegen einer Veranstaltung') ?>
            <textarea name="create_description"><?= htmlReady($sem_class['create_description']) ?></textarea>
        </label>
    </fieldset>

    <fieldset class="attribute_table">
        <legend>
            <?= _("Inhaltselemente") ?>
        </legend>

        <div container="plugins" id="plugins">
            <h2 title="<?= _("Diese Inhaltselemente sind standardmäßig bei den Veranstaltungen dieser Klasse aktiviert.") ?>"><?= _("Verfügbare Inhaltselemente") ?></h2>
            <div class="droparea">
                <? foreach ($modules as $module_name => $module_info) : ?>
                    <?= $this->render_partial("admin/sem_classes/content_plugin.php",
                        [
                            'plugin' => $module_info,
                            'plugin_id' => $module_name,
                            'activated' => $sem_class['modules'][$module_name]['activated'] ?? false,
                            'sticky' => $sem_class['modules'][$module_name]['sticky'] ?? false,
                        ]
                    )?>
                <? endforeach ?>
            </div>
        </div>

    </fieldset>

    <footer>
        <?= Studip\Button::create(_("Speichern"), "save") ?>
        <? if ($sem_class->countSeminars() === 0) : ?>
            <?= Studip\Button::create(_("Löschen"), "delete", ['formaction' => $controller->url_for('admin/sem_classes/delete_sem_class', $sem_class->id)]) ?>
        <? endif ?>
    </footer>
</form>
