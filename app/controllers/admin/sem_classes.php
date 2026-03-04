<?php
/*
 *  Copyright (c) 2012  Rasmus Fuhse <fuhse@data-quest.de>
 *
 *  This program is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU General Public License as
 *  published by the Free Software Foundation; either version 2 of
 *  the License, or (at your option) any later version.
 */

class Admin_SemClassesController extends AuthenticatedController
{
    function before_filter (&$action, &$args)
    {
        parent::before_filter($action, $args);
        if (!$GLOBALS['perm']->have_perm("root")) {
            throw new AccessDeniedException();
        }
        PageLayout::setHelpKeyword("Admins.SemClasses");
        PageLayout::setTitle(_('Veranstaltungskategorien'));
    }

    public function overview_action()
    {
        Navigation::activateItem("/admin/locations/sem_classes");

        $links = new ActionsWidget();
        $links->addLink(
            _('Neue Kategorie anlegen'),
            $this->url_for('admin/sem_classes/add_sem_class'),
            Icon::create('add'),
            ['data-dialog' => 'size=auto']
        );
        Sidebar::Get()->addWidget($links);
    }

    public function add_sem_class_action() {
    }

    public function create_sem_class_action() {
        $name = Request::i18n('add_name');
        $copy = Request::get('add_like');

        if (SemClass::countBySql('name = ?', [$name])) {
            $message = sprintf(_('Es existiert bereits eine Veranstaltungskategorie mit dem Namen "%s"'), htmlReady($name));
            PageLayout::postError($message);
        } else {
            if ($copy) {
                $sem_class = clone $GLOBALS['SEM_CLASS'][$copy];
                $sem_class->setId(0);
            } else {
                $sem_class = new SemClass();
            }

            $sem_class->name = $name;
            $sem_class->store();

            PageLayout::postSuccess(_('Veranstaltungskategorie wurde erstellt.'));
        }
        $this->redirect('admin/sem_classes/overview');
    }

    public function delete_sem_class_action($id)
    {
        $sem_class = $GLOBALS['SEM_CLASS'][$id];
        $sem_class->delete();
        PageLayout::postSuccess(_('Veranstaltungskategorie wurde gelöscht.'));
        $this->redirect('admin/sem_classes/overview');
    }

    public function details_action()
    {
        Navigation::activateItem("/admin/locations/sem_classes");

        $plugins = PluginManager::getInstance()->getPlugins(StudipModule::class);
        $this->sem_class = SemClass::getClasses()[Request::get("id")];
        $modules = [];
        foreach ($this->sem_class->getModuleObjects() as $plugin) {
            $modules[get_class($plugin)] = [
                'name' => $plugin->getPluginName(),
                'id' => $plugin->getPluginId(),
                'enabled' => $plugin->isEnabled(),
                'activated' => $this->sem_class->isModuleActivated($plugin->getPluginName())
            ];
        }
        foreach ($plugins as $plugin) {
            if (!$plugin->isActivatableForContext(new Course)) continue;
            if (isset($modules[get_class($plugin)])) continue;
            if ($this->sem_class->isModuleForbidden(get_class($plugin))) continue;
            $modules[get_class($plugin)] = [
                'name' => $plugin->getPluginName(),
                'id' => $plugin->getPluginId(),
                'enabled' => $plugin->isEnabled(),
                'activated' => false
            ];
        }

        $this->modules = $modules;

        if (!count($this->sem_class->getSemTypes())) {
            PageLayout::postInfo(_('Beachten Sie, dass es noch keine Veranstaltungstypen gibt!'));
        }

        $links = new ActionsWidget();
        $links->addLink(
            _('Veranstaltungstyp anlegen'),
            $this->url_for('admin/sem_classes/add_sem_type', $this->sem_class->id),
            Icon::create('add'),
            ['data-dialog' => 'size=auto']
        );
        Sidebar::Get()->addWidget($links);
    }

    public function save_action()
    {
        $id = Request::int("sem_class_id");
        $sem_class = $GLOBALS['SEM_CLASS'][$id];
        $old_data_sem_class = clone $sem_class;

        $sem_class->modules = Request::getArray("modules");
        $sem_class->name = Request::i18n("sem_class_name");
        $sem_class->description = Request::i18n("sem_class_description");
        $sem_class->title_dozent = Request::get("title_dozent") ?: null;
        $sem_class->title_dozent_plural = Request::get("title_dozent_plural") ?: null;
        $sem_class->title_tutor = Request::get("title_tutor") ?: null;
        $sem_class->title_tutor_plural = Request::get("title_tutor_plural") ?: null;
        $sem_class->title_autor = Request::get("title_autor") ?: null;
        $sem_class->title_autor_plural = Request::get("title_autor_plural") ?: null;
        $sem_class->studygroup_mode = Request::int("studygroup_mode", 0);
        $sem_class->only_inst_user = Request::int("only_inst_user", 0);
        $sem_class->default_read_level = Request::int("default_read_level");
        $sem_class->default_write_level = Request::int("default_write_level");
        $sem_class->bereiche = Request::int("bereiche", 0);
        $sem_class->module = Request::int("module", 0);
        $sem_class->show_browse = Request::int("show_browse", 0);
        $sem_class->visible = Request::int("visible", 0);
        $sem_class->course_creation_forbidden = Request::int("course_creation_forbidden", 0);
        $sem_class->create_description = Request::get("create_description");
        $sem_class->admission_prelim_default = Request::int("admission_prelim_default");
        $sem_class->admission_type_default = Request::int("admission_type_default");
        $sem_class->show_raumzeit = Request::int("show_raumzeit", 0);
        $sem_class->is_group = Request::int("is_group", 0);
        $sem_class->unlimited_forbidden = Request::bool('unlimited_forbidden', 0);
        $sem_class->admission_turnout_mandatory = Request::bool('admission_turnout_mandatory', 0);
        $sem_class->store();

        foreach ($sem_class->getSemTypes() as $sem_type) {
            $sem_type->name = Request::i18n('sem_type_' . $sem_type->id);
            $sem_type->store();
        }

        foreach ($sem_class->modules as $module_name => $module) {
            if ($sem_class->isModuleMandatory($module_name) && !$old_data_sem_class->isModuleMandatory($module_name)) {
                $sem_class->activateModuleInCourses($module_name);
            }
            if (!$sem_class->isModuleAllowed($module_name) && $old_data_sem_class->isModuleAllowed($module_name)) {
                $sem_class->deActivateModuleInCourses($module_name);
            }
        }

        PageLayout::postSuccess(_('Änderungen wurden gespeichert.'));
        $this->redirect('admin/sem_classes/details', ['id' => $id]);
    }

    public function add_sem_type_action($id)
    {
        $this->sem_class = $id;
    }

    public function create_sem_type_action()
    {
        $name = Request::i18n('name');
        $class = Request::int('sem_class');

        SemType::create(compact('name', 'class'));

        $this->redirect('admin/sem_classes/details', ['id' => $class]);
    }

    public function delete_sem_type_action($id)
    {
        $sem_type = $GLOBALS['SEM_TYPE'][$id];
        $class = $sem_type->class;

        if ($sem_type->countSeminars() === 0) {
            $sem_type->delete();
        }

        $this->redirect('admin/sem_classes/details', ['id' => $class]);
    }

}
