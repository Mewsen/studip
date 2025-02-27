---
id: coursesets
title: Anmeldesets und -regeln
sidebar_label: Anmeldesets und -regeln
---

## Anmeldesets und -regeln

### Konzept
Ein Anmeldeset stellt einen Rahmen für Veranstaltungen dar, die gemeinsame Regeln zur Anmeldung besitzen. Mit Stud.IP 3.0 werden initial schon einige verschiedene Regeln mitgeliefert, hier soll jedoch unter anderem beschrieben werden, wie man selbst eine solche Regel implementieren kann.



### Aufbau einer Anmelderegel
Alle Anmelderegeln liegen im Ordner `lib/admissionrules`. Pro Regeltyp gibt es dort einen Ordner, in dem typischerweise die Klassendefinition der Regel liegt (`Regeltyp.class.php`, SQL-Anweisungen, die bei  (De-)Installation der Regel ausgeführt werden müssen und templates zur Konfiguration und zur Kurzanzeige der Regel.

Im Folgenden soll das Beispiel `NightAdmission` entwickelt werden, eine Regel, die eine Anmeldung nur zwischen 22 und 6 Uhr zulässt.

#### Speichern und Laden der Daten
Die Regel vom Typ `NightAdmission` sollen alle in einer eigenen Tabelle `nightadmissions` in der Datenbank gespeichert werden. Da dieser Regeltyp neben dem standardmäßig vorhandenen Infotext keine weiteren Attribute besitzt, sieht diese Tabelle so aus:

```sql
CREATE TABLE `nightadmissions` (
    `rule_id` VARCHAR(32) NOT NULL,
    `message` TEXT NOT NULL,
    `mkdate` INT NOT NULL,
    `chdate` INT NOT NULL,
    PRIMARY KEY (`rule_id`);
```

Wird dieser Regeltyp komplett aus dem System entfernt, so reicht folgende SQL-Anweisung zum Aufräumen:

```sql
DROP TABLE `nightadmissions`;
DELETE FROM `courseset_rule` WHERE `type`='NightAdmission';
```

#### Regeldefinition
Wir legen eine Datei `NightAdmission.class.php` an, die von der bereits vorhandenen Klasse `AdmissionRule` erbt. Da wir nur die aktuelle Uhrzeit berücksichtigen müssen, braucht diese Klasse keine eigenen, weiteren Attribute. Wir definieren nur, mit welchen anderen Anmelderegeltypen diese Regel kombinierbar ist (nämlich alle Standardregeln außer zeitgesteuerter und komplett gesperrter Anmeldung).

Einige Standardmethoden müssen wir ebenfalls implementieren, um das Laden und Speichern in eigene Tabellen zu realisieren.

```php
<?php
class NightAdmission extends AdmissionRule {

    /**
     * Standardkonstruktor
     */
    public function __construct($ruleId=*, $courseSetId = *)
    {
        parent::__construct($ruleId, $courseSetId);
        $this->default_message = _("Sie können sich nur nachts zwischen 22 und 6 Uhr anmelden.");
        if ($ruleId) {
            // Regel bereits vorhanden, lade Daten.
            $this->load();
        } else {
            // Erzeuge neue ID.
            $this->id = $this->generateId('nightadmissions');
        }
        return $this;
    }

    /**
     * Lösche aktuelle Regel aus der Datenbank.
     */
    public function delete() {
        parent::delete();
        $stmt = DBManager::get()->prepare("DELETE FROM `nightadmissions` WHERE `rule_id`=?");
        $stmt->execute(array($this->id));
    }

    /**
     * Beschreibungstext für diesen Regeltyp, wird angezeigt, wenn eine neue
     * Regel zu einem Anmeldeset hinzugefügt werden soll.
     */
    public static function getDescription() {
        return _("Diese Regel erlaubt die Anmeldung nur nachts zwischen 22 und 6 Uhr.");
    }

    /**
     * Name für diesen Regeltyp, wird angezeigt, wenn eine neue Regel zu einem
     * Anmeldeset hinzugefügt werden soll.
     */
    public static function getName() {
        return _("Nächtliche Anmeldung");
    }

    /**
     * Holt das Template zur Anzeige der Konfiuration dieser Regel
     * (configuration.php, hinterlegt im Unterordner templates). Für unser
     * Beispiel brauchen wir nur das Standardtemplate, da es nichts eigenes*
     * für diesen Regeltyp zu konfigurieren gibt.
     */
    public function getTemplate() {
        $tpl = $GLOBALS['template_factory']->open('admission/rules/configure');
        $tpl->set_attribute('rule', $this);
        return $tpl->render();
    }

    /**
     * Lädt die Regel aus der Datenbank.
     */
    public function load() {
        $rule = DBManager::get()->fetch("SELECT * FROM `nightadmissions` WHERE `rule_id`=? LIMIT 1", array($this->id));
        $this->message = $rule['message'];
        return $this;
    }

    /**
     * Diese Funktion überprüft, ob sich der gegebene Benutzer zur gegebenen
     * Veranstaltung anmelden darf, ob die Regel also greift.
     * Zurückgegeben wird eine Fehlermeldung, falls die Anmeldung nicht
     * möglich ist.
     */
    public function ruleApplies($userId, $courseId) {
        $failed = array();
        $now = mktime();
        // Zeit zwischen 6 und 22 Uhr => keine Anmeldung erlaubt.
        if (date('H', $now) < 22 && date('H', $now) >= 6) {
            $failed[] = $this->default_message;
        }
        return $failed;
    }

    /**
     * Speichert die aktuelle Regel in der Datenbank.
     */
    public function store() {
        $stmt = DBManager::get()->prepare("INSERT INTO `nightadmissions`
            (`rule_id`, `message`, `mkdate`, `chdate`)
            VALUES
            (:id, :message, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
            ON DUPLICATE KEY
            UPDATE `message`=VALUES(`message`), `chdate`=VALUES(`chdate`)");
        $stmt->execute(array('id' => $this->id, 'message' => $this->message));
        return $this;
    }

}
```

Am wichtigsten sind die beiden Methoden `ruleApplies` und `getTemplate`.

Erstere Methode spezifiert das Verhalten der Regel, also wann und unter welchen Voraussetzungen überhaupt eine Anmeldung erfolgreich sein kann. Hier können im Prinzip beliebige Datenbankabfragen oder sonstige anderen Funktionen aufgerufen werden.

Das Template definiert die GUI zur Konfiguration der jeweiligen Regel. Standardmäßig wird nur ein Textfeld angeboten, das einen Text aufnehmen kann, der vor der Anmeldung auf der Veranstaltungsseite erscheint. Will man hier weitere Werte, Checkboxen oder anderes einstellbar machen, muss man selbst ein [Flexi-Template](FlexiTemplates) dafür schreiben.

Daneben kann es noch ein Info-Template geben, das nur zur Anzeige der Regel in normalem Prosatext dient.

### Zusammenfassung
Um diese Beispielregel in Stud.IP zu installieren, reicht es, in `lib/admissionrules` einen Ordner `nightadmissions` zu erstellen, dort die obige Klasse `Nightadmission.class.php` hineinzukopieren und die nötigen SQL-Anweisungen auszuführen. Da kein eigenes Template benötigt wird, ist hier auch kein Unterordner `templates` von Nöten. In der **globalen Konfiguration** unter **Anmelderegeln ** kann die Regel dann aktiviert werden. An gleicher Stelle muss dann unter **Regelkompatibilität** eingestellt werden, mit welchen vorhandenen Regelndie neue Regel kombinierbar ist.



## Verteilungsalgorithmus
Der Algorithmus, der die Plätze der Veranstaltungen eines Anmeldesets verteilt, kann ebenfalls frei selbst implementiert werden. Standardmäßig wird bereits ein Algorithmus mitgeliefert.

Zum Anlegen eines neuen Algorithmus reicht es, das vorhandene Interface `AdmissionAlgorithm` zu implementieren, dabei handelt es sich im Prinzip nur um eine Methode `run()`, die den Algorithmus ausführt.
