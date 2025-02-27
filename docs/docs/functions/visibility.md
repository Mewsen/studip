---
id: visibility
title: VisibilityAPI
sidebar_label: VisibilityAPI
---

Mit Hilfe der VisibilityAPI können Sichbarkeitseinstellungen für Benutzer von überall im System (Auch in Plugins) hinzugefügt werden, ohne das direkt in den Sichbarkeitscode eingegriffen werden muss. Die Verwaltung der Einstellungsmöglichkeiten ist ebenfalls ohne Eingriff in bestehenden Code möglich.

## Sichtbarkeitsstufen

Die VisibilityAPI bietet die Möglichkeit, direkt im Dateiensystem festzulegen, welche Sichtbarkeiten zur Verfügung stehen. Der Ordner dafür ist lib/classes/visibility/visibilitySettings.
Dieser enthält standardmäßig folgende Sichtbarkeitseinstellungen:

* Me
* Buddies
* Domain
* Studip
* Extern

Beispiel
```php
class Visibility_Buddies extends VisibilityAbstract{
    
    // Soll dieser Status benutzt werden können
    protected $activated = true;
    
    // Welche int Repräsentation in der Datenbank
    protected $int_representation = 2;
    
    // Was wird in den Einstellungen angezeigt
    protected $display_name = "Buddies";
    
    // Was wird bei Visibility::getStateDescription() angezeigt
    protected $description = "nur für meine Buddies sichtbar";
    
    // Wann haben zwei Nutzer diesen Status
    function verify($user_id, $other_id) {
        return CheckBuddy(get_username($other_id), $user_id) || $user_id == $other_id;
    }   
}
```
Eine Sichtbarkeitseinstellung muss immer die Klasse Visibility_+"Name der Datei" enthalten, die VisibilityAbstract erweitert. Außerdem müssen folgende Attribute und Funktionen vorhanden sein:
* **activated**: Definiert ob die Sichtbarkeitseinstellung verfügbar sein soll. Änderungen werden erst nach einem Relogg übernommen, da die Sichtbarkeitseinstellungen aus I/O Kostengründen in der Session gespeichert werden.
* **int_representation**: Legt fest unter welchem Wert eine Sichtbarkeit in der Datenbank gespeichert wird. Dabei darf es keine Überschneidung geben.
* **display_name**: Beschreibt den Namen unter dem der Punkt in den Einstellungen auftaucht.
* **description**: Ist eine Beschreibung der Einstellung, die verwendet werden kann, um dem Nutzer die aktuelle Einstellung anzuzeigen
* **function verify**: Die Hauptaufgabe einer Sichtbarkeitseinstellung ist die verify Funktion. Sie muss immer eine BenutzerID (Gibt den Besitzer des Sichtbarkeitsobjekts an) und eine AndereID (Gibt den Aufrufenden an) erhalten. Der Rückgabe Wert muss `true` sein, wenn der Aufrufer (other_id) in richtiger Relation zum Besitzer (user_id) steht.

Auch eine Sichtbarkeit nobody steht zur Verfügung. Diese ist vor allem für Debugging von Vorteil.

## Sichtbarkeit der Homepage-Elemente

Grundsätzlich kann eine Sichtbarkeitseinstellung durch zwei Möglichkeiten definiert sein:
* **SichbarkeitsID**: Eine eindeutige ID, die bei der Erstellung generiert wird
* **Identifier und BenutzerID**: Ein Identifikationsstring kann mit Hilfe einer BenutzerID zu einer SichtbarkeitsID aufgelöst werden. Dies erleichtert zwar die Programmierung kann aber unter Umständen zu Überschneidungen bei Identifikationsstrings führen. Deshalb sollte dabei darauf geachtet werden diesen so eindeutig wie möglich zu wählen.

### Hinzufügen einer Sichtbarkeitseinstellung
Soll für einen Nutzer eine Sichtbarkeitseinstellung hinzugefügt werden, so muss reicht folgender Aufruf:
```php
Visibility::addPrivacySetting($name, $identifier = "", $parent = null, $category = 1, $user = null, $default = null, $pluginid = null)
```
Dabei wird eine SichtbarkeitsID erzeugt und zurückgegeben, die gespeichert werden kann.
* **name**: Der Name unter dem die Sichtbarkeitseinstellung beim Benutzer erscheint
* **identifier**: Identifikationsstring
* **parent**: Die Sichtbarkeitseinstellungen bieten an, einen Baum zu erschaffen. Ist das gewünscht muss hier die SichbarkeitsID oder der Identifikationsstring für den Elternknoten angegeben werden. Ansonsten muss hier null angegeben werden, um einen root Knoten zu erstellen. Achtung! Rootknoten sind immer eine Kategorie d.h. es gibt keine Einstellungsmöglichkeiten
* **category**: Unterscheidet den Typ der Sichtbarkeitseinstellung. 0 steht für eine Kategorie, die keine Einstellungsmöglichkeiten enthält. 1 Für einen "normalen" Einstellungspunkt.
* **user**: Gibt den Benutzer an, für den die Sichtbarkeit angelegt wird. Ist dieser Wert `null` so wird der aktuell angemeldete Benutzer verwendet.
* **default**: Kann angeben auf welcher Sichtbarkeit der Wert zu Beginn steht. Ist dieser Wert `null` so wird der Standardwert des `user` verwendet
* **pluginid**: Kann verwendet werden um der Sichbarkeit zwangsweiße eine PluginID zuzuweisen. Wird die Funktion aus einem Plugin aufgerufen, so ist es nicht nötig eine PluginID anzugeben, da die API diese selbständig herausfindet.

### Ändern einer Sichtbarkeitseinstellung
Die Funktion `updatePrivacySetting` erhält die selben Parameter, wie `addPrivacySetting` und wird verwendet um eine Sichtbarkeitseinstellung upzudaten. Dabei wird die alte Sichbarkeitseinstellung gelöscht und eine neue erzeugt. Dies erleichtert dem Programmierer die Arbeit um nicht prüfen zu müssen, ob eine Sichtbarkeit bereits existiert. Ist eine Sichtbarkeit an ein Eingabefeld gekoppelt kann die Funktion `updatePrivacySettingWithTest` verwendet werden die Zusätzlich noch als ersten Parameter einen String erhält. Ist dieser String leer so wird die Sichtbarkeitseinstellung nur gelöscht.

### Löschen einer Sichtbarkeitseinstellung
Die Funktion
```php
Visibility::removePrivacySetting($id, $user = null)
```
löscht eine Sichtbarkeitseinstellung anhand einer SichbarkeitsID ($id) oder anhand eines Identifikationsstring($id) und einer BenutzerID ($user)

### Bulkfunktionen
Bei einer Migration kann es notwendig sein, allen Nutzern eine neue Sichbarkeitseinstellung hinzuzufügen. Dafür kann der Befehl
```php
Visibility::addPrivacySettingForAll($name, $identifier = "", $parent = null, $category = 1, $default = null, $pluginid = null)
```
verwendet werden. Zum löschen aller Einträge eines Identifikationsstrings wird die Funktion
```php
Visibility::removeAllPrivacySettingForIdentifier($ident)
```
verwendet.

### Überprüfung
Um dann im Code eine Sichtbarkeit zu überprüfen kann folgender Code verwendet werden:
```php
//Überprüfung mit ID
if (Visibility::verify(1234)) {
 echo 'Ich darf VisibilityID 1234 sehen';
}

//Überprüfung mit Identifier und Benutzername
if (Visibility::verify('homepageelement', $aufgerufener_benutzer->md5) {
  echo 'Ich darf das homepageelement von '.$aufgerufener_benutzer.' sehen';
}

//Überprüfung für anderen Nutzer
if (Visibility::verify('homepageelement', $aufgerufener_benutzer->md5, $test_benutzer->md5) {
  echo $test_benutzer.' darf das homepageelement von '.$aufgerufener_benutzer.' sehen;
}
```

# Alte Version

## Sichtbarkeitsstufen
Erweiterte Möglichkeiten zur Festlegung der persönlichen Privatsphäre und Sichtbarkeiten stehen ab der Stud.IP-Version 2.0 zur Verfügung.

Die Funktionen zum Abfragen der Sichtbarkeiten sind in lib/user_visible.inc.php definiert. Die vorhandenen Sichtbarkeitsstufen sind dort als Konstanten definiert:

* **VISIBILITY_ME**: Nur für den Nutzer selbst (und dessen evtl. Standardvertretungen mit Homepagebearbeitungsrecht)
* **VISIBILITY_BUDDIES**: für Buddies aus dem Adressbuch
* **VISIBILITY_DOMAIN**: für die eigene(n) Nutzerdomäne(n)
* **VISIBILITY_STUDIP**: für alle in Stud.IP eingeloggten Nutzer
* **VISIBILITY_EXTERN**: auf externen Seiten

# allgemeine Sichtbarkeit einer Kennung
Soll die Sichtbarkeit einer Kennung abgefragt werden, so gibt es dafür die Methoden `get_visibility_by_id` bzw. `get_visibility_by_username` bzw. `get_visibility_by_state`.

```php
// Liefert true oder false, je nach Sichtbarkeit der Kennung
$visibility = get_visibility_by_username('tester');

/*
 * Liegt die in der Datenbank hinterlegte Sichtbarkeit
 * bereits vor, so kann wie folgt abgefragt werden:
 */
// Sei die Sichtbarkeit gleich 'yes'
$db_vis = 'yes'

$visibility = get_visibility_by_state($db_vis, get_userid('tester'));
```
Hier kommt als Ergebnis also heraus: "Darf ich die Kennung sehen?", das hängt nicht nur von den Sichtbarkeitsinstellungen der Kennung ab, sondern auch von meinen eigenen Rechten (Root sieht alles).

Um explizit die globale Sichtbarkeit, unabhängig von Root-Rechten o.ä. abfragen zu können, existieren die Methoden `get_global_visibility_by_id` und `get_global_visibililty_by_username`, die als Parameter die User-ID bzw. den Usernamen erhalten und die in der Datenbank hinterlegte Sichtbarkeit zurückgeben. Hier kommt also ein Wert aus der Menge `{'yes', 'no', 'always', 'never', 'unknown', 'global'}` heraus

Zur Abfrage der Sichtbarkeit in einem bestimmten Bereich von Stud.IP gibt es die Methoden `get_local_visibility_by_id` bzw. `get_local_visibility_by_username`. Hiermit kann durch Angabe der User-ID bzw. des Usernamens und des gewünschten Bereichs die Sichtbarkeit in diesem Bereich abgefragt werden. Gültige Bereiche sind

* **online** für die Wer ist online-Liste
* **chat** für die Sichtbarkeit des eigenen Chatraums
* **search** für die Auffindbarkeit in der Personensuche
* **email** für die Anzeige der Emailadresse
* **homepage** für die Sichtbarkeitseinstellungen der einzelnen Elemente der Profilseite

Will man z.B. wissen, ob der User mit dem Usernamen 'tester' über die Personensuche auffindbar ist, so kann dies so abgefragt werden:

```php
$search_visibility = get_local_visibility_by_username('tester', 'search');
```
Besonders auf externen Seiten ist es noch nützlich, auch zu wissen, welche Berechtigung der abzufragende User im System hat. Daher kann optional auch angegeben werden, dass diese Berechtigung mit zurückgegeben werden soll:

```php
$search_visibility = get_local_visibility_by_username('tester', 'search', true);
```
führt dann zur Ausgabe

```php
$search_visibility = Array(
  'perms' => 'dozent', 
  'search' => true
);
```

## Sichtbarkeit der Homepage-Elemente
Auf der Profilseite einer Person werden am Anfang standardmäßig alle Sichtbarkeiten der einzelnen Elemente geladen. Damit wird die Anzahl der Datenbankanfragen minimiert, indem nur eine globale Anfrage für alle Elemente statt eines Queries pro Element ausgeführt werden muss.

Mittels der Funktionen `is_element_visibible_for_user` und `is_element_visible_externally` kann dann überprüft werden, ob ein einzelnes Element anhand seiner Sichtbarkeitseinstellungen für den aktuellen Nutzer angezeigt werden soll.

Hierzu ein Beispiel: Aus der Datenbank wurde geladen, dass das Element private_phone (also die private Telefonnummer) die Sichtbarkeit 1 (=VISIBILITY_ME) hat, also nur für den Besitzer der Homepage selbst angezeigt werden soll. Die Methode `is_element_visible_for_user` bekommt nun als Parameter die ID des aktuellen Users, die ID des Users, zu dem die gerade besuchte Homepage gehört und den Wert der Sichtbarkeit, also 1. Daraus wird nun ermittelt, ob die Telefonnummer angezeigt werden soll oder nicht.

Im Code sieht das so aus:

```php
// Der "Besitzer" der Homepage hat die ID '12345'
$visibilities = get_local_visibility_by_id('12345', 'homepage');
// Der Besucher der Homepage hat die ID 'abcde'
$private_phone = is_element_visible_for_user('abcde', '12345', $visibilities['private_phone']);
```
Geht es nur um einzelne Elemente der Homepage, so kann man auch explizit deren Sichtbarkeit abfragen:

```php
// Wieder Homepagebesitzer-ID '12345'
$private_phone_visibility = get_homepage_element_visibility('12345', 'private_phone');
```
Aus Performancegründen wird für eine gesamte Homepage nur die erste Variante ausgeführt, wo alle Sichtbarkeiten auf einmal geladen werden.

Über die Methode `get_visible_email` kann die nach außen sichtbare Emailadresse ermittelt werden. Hat ein Nutzer eingestellt, dass die eigene Emailadresse nicht angezeigt werden soll, so wird stattdessen versucht, über die Einrichtungszuordnung dieser Kennung eine Emailadresse zu ermitteln (nur Zuordnungen mit mindestens Recht autor). Dabei wird zuerst die Emailadresse der ersten gefundenen Einrichtung verwendet, sollte es mehrere Einrichtungszuordnungen geben und eine davon als Standardeinrichtung definiert sein, so wird diese Email verwendet. Bei keiner gefundenen Zuordnung wird ein Leerstring als Emailadresse zurückgegeben.
