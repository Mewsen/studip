<?php

class UpdateStudygroupHelpTour extends Migration
{

    public function description()
    {
        return "Updates the help tour for study groups";
    }

    public function up()
    {

       $query = "UPDATE `help_content`
                            SET `content` = :content, `studip_version` = :version
                            WHERE `global_content_id` = :global_content_id";

       $new_content = "Studiengruppen sind eine Möglichkeit, in Gruppen zusammenzuarbeiten. Jede Person kann eine Studiengruppe erstellen und so einen gemeinsamen Ort zum Lernen und Austauschen schaffen.\r\n\r\nDies ist die Übersicht aller Studiengruppen, in denen Sie eingetragen sind.\r\n\r\nUm zu erfahren wie man Studiengruppen anlegen kann, ist unten eine Tour zusammengestellt.\r\n\r\nHinweis: Auf der Startseite sind zwei Widgets zu den Studiengruppen zu finden. Diese ermöglichen eine bessere Sichtbarkeit und Vorschläge für interessante Studiengruppen.";

        DBManager::get()->execute($query, [
            'content' => $new_content,
            'version'     => '6.0',
            'global_content_id' => 'bd0770f9eef5c10fc211114ac35fbe9b'
            ]);

        $tour_id = '19ac063e8319310d059d28379139b1cf';
        $tour = "(:tour_id, :tour_id, 'Studiengruppe anlegen', 'In dieser Tour wird das Anlegen von Studiengruppen erklärt.', 'tour', 'autor,tutor,dozent,admin,root', 1, 'de', '6.0', '', '', 1405684299, 0)";
        $settings = "(:tour_id, 1, 'standard', NULL, NULL)";

        $steps = "(:tour_id, 1, 'Studiengruppe anlegen', 'Studiengruppen ermöglichen eine einfache Zusammenarbeit und den Austausch mit Kommiliton*innen.\nDiese Tour zeigt Ihnen Schritt für Schritt, wie Sie eine Studiengruppe erstellen.\n\rHinweis: Klicken Sie auf “Weiter”, um den nächsten Schritt zu starten.', 'R', 0, '', 'dispatch.php/my_studygroups', '', '', '', 1405684423, 0),
                (:tour_id, 2, 'Studiengruppe anlegen', 'Klicken Sie auf „Neue Studiengruppe anlegen“, um den Dialog zur Erstellung einer neuen Studiengruppe zu öffnen.', 'BL', 0, '.sidebar-widget:eq(1) A:eq(0)', 'dispatch.php/my_studygroups', '.ui-dialog-titlebar-close:eq(0)', '', '', 1405684423, 0),
                (:tour_id, 3, 'Name der Studiengruppe', 'Geben Sie einen klaren und aussagekräftigen Titel für Ihre Studiengruppe ein.', 'R', 0, '#wizard-name', 'dispatch.php/my_studygroups', '', '.sidebar-widget:eq(1) li:eq(0) a:eq(0)', '', 1405684720, 0),
                (:tour_id, 4, 'Beschreibung', 'Beschreiben Sie den Zweck oder die Ziele der Studiengruppe (z. B. Themen, Aktivitäten, Zielgruppe).', 'R', 0, '#wizard-description', 'dispatch.php/my_studygroups', '', '', 'dozent@studip.de', 1405684806, 0),
                (:tour_id, 6, 'Zugang', 'Wählen Sie aus, ob die Studiengruppe für alle offen ist oder ein Beitritt nur auf Anfrage gewährt werden soll.', 'R', 0, '#wizard-access', 'dispatch.php/my_studygroups', '', '', 'root@localhost', 1405685334, 0),
                (:tour_id, 7, '', 'Die Laufzeit von Studiengruppen ist standardmäßig auf zwei\rJahre festgelegt. Gruppenadmins werden rechtzeitig vor Ablauf per E-Mail informiert und können die Laufzeit bei Bedarf verlängern. Diese Einstellung ist besonders hilfreich für zeitlich begrenzte Projekte.', 'R', 0, '#wizard-datepicker', 'dispatch.php/my_studygroups', '', '', 'root@localhost', 1405685652, 0),
                (:tour_id, 8, '', 'Fügen Sie optional Schlagwörter hinzu, die Ihre Gruppe beschreiben (z. B. „Mathe“, „Projektarbeit“). Dies erhöht die Sichtbarkeit für Interessierte.', 'R', 0, '#studygroup-wizard-tags', 'dispatch.php/my_studygroups', '', '', 'root@localhost', 1405685652, 0),
                (:tour_id, 9, 'Studiengruppe speichern', 'Mit dem Klick auf den Button Studiengruppe anlegen wird die Studiengruppe erstellt. Sie können jetzt Mitglieder hinzufügen, Inhalte teilen und gemeinsam arbeiten.', 'T', 0, '.ui-dialog-buttonset', 'dispatch.php/my_studygroups', '.sidebar-widget:eq(1) li:eq(0) a:eq(0)', '', 'root@localhost', 1405686068, 0),
                (:tour_id, 10, '', 'Alle Einstellungen können jederzeit über die Verwaltungsoptionen der Gruppe angepasst werden.', 'B', 0, '', 'dispatch.php/my_studygroups', '', '.ui-dialog-titlebar-close:eq(0)', 'root@localhost', 1405686068, 0)";

        $this->updateTour($tour_id, $tour, $settings, $steps);

    }

    public function isTourChanged($tour_id) {
        $statement = DBManager::get()->prepare("
            SELECT COUNT(*)
            FROM `help_tour_steps` WHERE tour_id = ? AND `chdate` > `mkdate`
        ");
        $statement->execute([$tour_id]);
        $changed = $statement->fetch(PDO::FETCH_COLUMN, 0);
        if (!$changed) {
            $statement = DBManager::get()->prepare("
                SELECT COUNT(*)
                FROM `help_tour_settings` WHERE tour_id = ? AND `chdate` > `mkdate`
            ");
            $statement->execute([$tour_id]);
            $changed = $statement->fetch(PDO::FETCH_COLUMN, 0);
        }
        return $changed > 0;
    }

    public function updateTour($tour_id, $tour, $settings, $steps) {
        $tour_changed = $this->isTourChanged($tour_id);
        if ($tour_changed) {
            $old_tour_id = md5(uniqid('tours', 1));
            $query = "UPDATE `help_tours` SET `tour_id` = :old_tour_id, `name` = CONCAT(`name`, ' (outdated)') WHERE `tour_id` = :tour_id";
            DBManager::get()->execute($query, [
                'old_tour_id' => $old_tour_id,
                'tour_id'     => $tour_id,
            ]);

            $query = "UPDATE `help_tour_steps` SET `tour_id` = :old_tour_id WHERE `tour_id` = :tour_id";
            DBManager::get()->execute($query, [
                'old_tour_id' => $old_tour_id,
                'tour_id'     => $tour_id,
            ]);

            $query = "UPDATE `help_tour_audiences` SET `tour_id` = :old_tour_id WHERE `tour_id` = :tour_id";
            DBManager::get()->execute($query, [
                'old_tour_id' => $old_tour_id,
                'tour_id'     => $tour_id,
            ]);

            $query = "UPDATE `help_tour_settings` SET `tour_id` = :old_tour_id, `active` = 0 WHERE `tour_id` = :tour_id";
            DBManager::get()->execute($query, [
                'old_tour_id' => $old_tour_id,
                'tour_id'     => $tour_id,
            ]);

            $query = "UPDATE `help_tour_user` SET `tour_id` = :old_tour_id WHERE `tour_id` = :tour_id";
            DBManager::get()->execute($query, [
                'old_tour_id' => $old_tour_id,
                'tour_id'     => $tour_id,
            ]);
        } else {
            $this->removeTour($tour_id, true);
        };

        $query = "INSERT INTO `help_tours` (`global_tour_id`, `tour_id`, `name`, `description`, `type`, `roles`, `version`, `language`, `studip_version`, `installation_id`, `author_email`, `mkdate`, `chdate`) VALUES " . $tour;
        $statement = DBManager::get()->prepare($query);
        $statement->execute(['tour_id' => $tour_id]);

        $query = "INSERT INTO `help_tour_settings` (`tour_id`, `active`, `access`, `mkdate`, `chdate`) VALUES " . $settings;
        $statement = DBManager::get()->prepare($query);
        $statement->execute(['tour_id' => $tour_id]);

        $query = "INSERT INTO `help_tour_steps` (`tour_id`, `step`, `title`, `tip`, `orientation`, `interactive`, `css_selector`, `route`, `action_prev`, `action_next`, `author_email`, `mkdate`, `chdate`) VALUES " . $steps;
        $statement = DBManager::get()->prepare($query);
        $statement->execute(['tour_id' => $tour_id]);
    }

    public function removeTour($tour_id, $remove_user_data = false) {
        $statement = DBManager::get()->prepare("
            DELETE FROM `help_tours` WHERE tour_id = ?;
        ");
        $statement->execute([$tour_id]);
        $statement = DBManager::get()->prepare("
            DELETE FROM `help_tour_steps` WHERE tour_id = ?;
        ");
        $statement->execute([$tour_id]);
        $statement = DBManager::get()->prepare("
            DELETE FROM `help_tour_audiences` WHERE tour_id = ?;
        ");
        $statement->execute([$tour_id]);
        $statement = DBManager::get()->prepare("
            DELETE FROM `help_tour_settings` WHERE tour_id = ?;
        ");
        $statement->execute([$tour_id]);
        if ($remove_user_data) {
            $statement = DBManager::get()->prepare("
                DELETE FROM `help_tour_user` WHERE tour_id = ?;
            ");
            $statement->execute([$tour_id]);
        }
    }
}
