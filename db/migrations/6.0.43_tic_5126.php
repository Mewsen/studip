<?php
class Tic5126 extends Migration
{
    public function description()
    {
        return "Update whats new in Stud.IP 6 tour.";
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

    public function up()
    {
        // remove old tour "Stud.IP 5! for tutor and above"
        $this->removeTour('a848744bde4dac47ec2e8b383155381d', true);

        // add new tour "Willkommen in Stud.IP 6!"
        $tour_id = 'dac47ec2e8a848744bde4b3881d31553';
        $tour = "(:tour_id, :tour_id, 'Willkommen in Stud.IP 6!', 'Einführung in Stud.IP 6', 'tour', 'autor,tutor,dozent', 1, 'de', '6.0', '', '', 1737728592, 0)";
        $settings = "(:tour_id, 1, 'autostart_once', NULL, NULL)";
        $steps = "(:tour_id, 1, 'Willkommen in Stud.IP 6!', \"In den folgenden Schritten möchten wir Ihnen kurz die wichtigsten Neuerungen vorstellen. Dazu klicken Sie auf \\\"weiter\\\" und folgen dem
Ablauf.\r\n\r\nDer Zeitpunkt passt gerade nicht? Kein Problem! Mit einem Klick auf das Fragezeichen-Icon oben rechts können Sie die Tour zu jedem Zeitpunkt erneut starten.\", 'B', 0, '', 'dispatch.php/start', '', '', '', 1737728592, 0),
        (:tour_id, 2, 'Neue Loginseite', \"Die neue Loginseite haben Sie eben schon gesehen.\r\nDas einladende neue Design bietet Raum für wichtige News und Hinweise.\", 'B', 0, '', 'dispatch.php/start', '', '', '', 1737728592, 0),
        (:tour_id, 3, '', 'Erstellen und verwalten Sie Aufgabenblätter in Ihrem Arbeitsplatz, direkt in Ihren Veranstaltungen oder fügen Sie sie in Courseware hinzu.', 'T', 0, '#content .content-item-vips', 'dispatch.php/contents/overview', '', '', '', 1737728592, 0),
        (:tour_id, 4, '', 'So können semesterbegleitende Tests und Lernstandskontrollen, aber auch Studienleistungen und sogar Prüfungen direkt in Stud.IP durchgeführt werden. Probieren Sie es einfach mal aus!', 'B', 0, '', 'dispatch.php/contents/overview', '', '', '', 1737728592, 0),
        (:tour_id, 5, '', 'Entdecken Sie Studiengruppen ganz neu! Mit dem neuen Startseiten-Widget haben Sie den Überblick. Verknüpfen Sie Studiengruppen nun direkt mit Veranstaltungen und tauschen Sie sich in Lerngruppen aus.', 'B', 0, '#nav_browse_my_studygroups A:eq(0)  SPAN:eq(0)', 'dispatch.php/my_studygroups', '', '', '', 1737728592, 0),
        (:tour_id, 6, '', 'Stundenplan und Kalender wurden ebenfalls überarbeitet, bieten einige geänderte Funktionen und kommen im frischen Design von Stud.IP 6.', 'B', 0, '', 'dispatch.php/calendar/schedule', '', '', '', 1737728592, 0),
        (:tour_id, 7, '', 'Viel Spaß beim Entdecken!', 'B', 0, '', 'dispatch.php/start', '', '', '', 1737728592, 0)";
        $this->updateTour($tour_id, $tour, $settings, $steps);
    }

    public function down()
    {

    }

}