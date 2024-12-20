<?php
class Tic4433 extends Migration
{
    public function description()
    {
        return 'Insert default content for Nutzungsbedingungen';
    }

    protected function up()
    {
        $content = '<!--HTML--><p>[lang=de]</p><ol><li>Bei Stud.IP besteht Klarnamenpflicht. Der Benutzer oder die Benutzerin verpflichtet sich, seinen/ihren korrekten Vornamen und Nachnamen anzugeben. Der zum Login benötigte Anmeldename ist innerhalb der programmtechnisch festgelegten Grenzen frei wählbar.</li><li>Der Benutzer oder die Benutzerin hat sicherzustellen, dass seine/ihre angegebene E-Mailadresse gültig und funktionsfähig ist.</li><li>Alle anderen Angaben zu Ihrer Person erfolgen freiwillig. Wenn Sie weitere Daten von sich angeben, sind diese nur für andere, registrierte Nutzer des Systems zugänglich. Eine Ausnahme hiervon sind automatisch aus dem System generierte Personalverzeichnisse der beteiligten Institute.</li><li>Der Benutzer oder die Benutzerin stellt sicher, dass er/sie bei der Nutzung des Kommunikationssystems Stud.IP nicht gegen eine geltende Rechtsvorschrift verstößt. Insbesondere verpflichtet sich der Benutzer oder die Benutzerin:<ol style="list-style-type:lower-latin;"><li>Stud.IP weder zum Abruf noch zur Verbreitung von sitten- oder rechtswidrigen Inhalten zu benutzen.</li><li>Die geltenden Jugendschutzvorschriften zu beachten.</li><li>Die Privatsphäre anderer zu respektieren und daher in keinem Fall belästigende, verleumderische oder bedrohende Inhalte einzustellen oder zu verschicken.</li><li>Keine Anwendungen auszuführen, die zu einer Veränderung der physikalischen oder logischen Struktur der genutzten Netze führen können.</li></ol></li><li>Die Nutzung von Stud.IP für jede andere Form von Werbe- oder Marketingbotschaften ist nicht gestattet und verpflichtet den Benutzern oder die Benutzerin zum Ersatz des Stud.IP entstandenen Schadens.</li><li>Der Benutzer oder die Benutzerin verpflichtet sich, seinen/ihren Zugang gegen die unbefugte Benutzung durch Dritte zu schützen. Stud.IP weist an dieser Stelle darauf hin, dass das Passwort nicht weitergegeben werden darf. Der Benutzer oder die Benutzerin haftet für jede durch sein/ihr Verhalten ermöglichte unbefugte Benutzung seines/ihres Accounts, soweit ihn/sie ein Verschulden trifft.</li><li>Bei einem Verstoß des Benutzers oder der Benutzerin gegen die oben aufgeführten Obliegenheiten erfolgt eine unverzügliche Sperrung des Zugangs.</li></ol><p>[/lang] [lang=en]</p><ol><li>Stud.IP bears a RealName obligation. &nbsp;The user is obliged to give his or her correct forename and surname. The registration name, necessary to login, is arbitary within the technical limitations of the program.</li><li>The user must ensure that his or her entered E-mail address is valid and functional.</li><li>All other information about the user is not compulsory. If you enter further information, it will only be accessed by other, registered users in the system. The only exception is system automatically generated personel indexes of the participating institutes.</li><li>The user must make sure that he or she does not violate any applicable laws or regulations by using Stud.IP communication system. In particular, the user is obliged:<ol><li>not to use Stud.IP to either call up or distribute immoral or illegal material.</li><li>to heed the applicable child protection regulations.</li><li>to respect the privacy of others and under no circumstances to call up or send harrassing, libellous or threatening material.</li><li>not to execute any applications that may lead to a change in the physical or logical structure of the shared net.</li></ol></li><li>The usage of Stud.IP for all other forms of advertising or marketing is not permitted, in which case the user is obliged to compensate Stud.IP for any damage caused.</li><li>The user is obliged to protect his or her access to Stud.IP against the unauthorised use by a third party. To this affect, Stud.IP advises that the password should not be passed on. The user is liable for every unauthorised usage of his or her account, as long as it is his or her fault.</li><li>Following a breachment of the above conditions by the user, the access will be immediately blocked.</li></ol><p>[/lang]</p>';

        $query = "INSERT INTO `siteinfo_details`
                        (`rubric_id`, `draft_status`, `page_disabled_nobody`, `name`, `content`)
                        VALUES (1, 1, 0, 'Nutzungsbedingungen', ?)";
        DBManager::get()->execute($query, [$content]);

        DBManager::get()->execute(
            "INSERT IGNORE INTO `config` VALUES (:field, :value, :type, :range, :section, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), :description)",
            [
                'field' => 'TERMS_OF_USE_URL',
                'value' => 'dispatch.php/siteinfo/show/1/10',
                'type' => 'string',
                'range' => 'global',
                'section' => 'privacy',
                'description' => 'URL zu den Nutzungsbedingungen'
            ]
        );
    }

    public function down()
    {
        DBManager::get()->exec("DELETE FROM `siteinfo_details` WHERE `name` = 'Nutzungsbedingungen'");

        DBManager::get()->execute(
            "DELETE `config`, `config_values`
             FROM `config`
             LEFT JOIN `config_values` USING (`field`)
             WHERE `field` = :field",
            ['field' => 'TERMS_OF_USE_URL']
        );
    }
}
