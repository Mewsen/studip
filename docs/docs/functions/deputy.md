---
id: deputy
title: Vertretung
sidebar_label: Vertretung
---

# Allgemeines

Ab Stud.IP 2.0 steht Funktionalität zur Verfügung, mit der Vertretungen von Personen definiert werden können, die z.B. innerhalb von Veranstaltungen volle Rechte von DozentInnen haben, aber nicht nach außen sichtbar sind. Darüber hinaus können auch Standardvertretungen einer Person definiert werden, die dann automatisch als Vertretung zu Veranstaltungen hinzugefügt werden, wenn die jeweilige Person DozentIn ist. Weiter können solche Standardvertretungen das Recht erhalten, die Profilseite der Person in vollem Umfang zu bearbeiten, deren Standardvertretung sie sind.

Zugehörige Funktionen sind in der Datei `lib/deputies_functions.inc.php` definiert.

Damit die Vertretungsfunktionalität aktiv ist, muss in der globalen Konfiguration die Variable `DEPUTIES_ENABLE` gesetzt sein. Damit Personen ihre Standardvertretungen definieren können, muss zusätzlich `DEPUTIES_DEFAULTENTRY_ENABLE` aktiviert sein, um auch Rechte zur Bearbeitung des eigenen Profils an die Vertretung übergeben zu können, muss `DEPUTIES_EDIT_ABOUT_ENABLE` aktiviert sein.

# Abfrage und Verändern von Vertretungen

### Frage bestehende Vertretungen ab

Zur Abfrage der bestehenden Vertretungen zu einer Veranstaltung oder Person gibt es die Methode `getDeputies`, diese bekommt eine Veranstaltungs- oder Personen-ID sowie optional ein Namensformat (wie üblich etwas in der Art "full" oder "full_rev", Default ist "full_rev") und gibt dann ein Array der Vertretungen zurück:
```php
// Definiere hier eine Seminar-ID...
$seminar_id = '9a739ae7fffab0c2347a783cef0f69be';
// ... und hole die Vertretungen in dieser Veranstaltung
$deputies = getDeputies($seminar_id);
```
führt zu folgendem Ergebnis:

```php
$deputies = Array
(
    [a272fef013c2b9367d1525daeb307c95] => Array
        (
            [user_id] => a272fef013c2b9367d1525daeb307c95
            [username] => tester
            [Vorname] => Toni
            [Nachname] => Tester
            [edit_about] => 0
            [perms] => tutor
            [fullname] => Tester, Toni
        )

)
```
Wie ersichtlich ist, wird ein Array zurückgegeben mit den jeweiligen Nutzer-IDs als Schlüssel und folgenden Daten:

* User-ID
* Username
* Vorname
* Nachname
* Darf die Person die Profilseite des "Chefs" bearbeiten (bei Veranstaltungen natürlich immer 0)
* globale Rechtestufe der Vertretung
* Voller Name gemäß dem beim Aufruf angegebenen Format

## Frage ab, von welchen Personen ein User Vertretung ist

Für den umgekehrten Fall, also zu ermitteln, vom wem eine bestimmte Person die Standardvertretung ist, gibt es die Funktion `getDeputyBosses`:

```php
// Hole alle Personen, von denen User-ID #12345' Vertretung ist:
$bosses = getDeputyBosses('12345');
```
Hier wird (analog zu `getDeputies`) ein Array mit Nutzerdaten zurückgegeben.

## Hinzufügen oder Entfernen von Vertretungen

Zum Verändern der bestehenden Vertretungen existieren die Funktionen `addDeputy`, `deleteDeputy` und `deleteAllDeputies`.

Hier Beispiele zur Verwendung:

```php
// Füge Benutzer mit ID '12345' zur Veranstaltung mit der ID '67890' als Vertretung hinzu:
addDeputy('12345', '67890');

// Entferne Benutzer mit ID '12345' als Vertretung aus der 
// Veranstaltung '67890':
deleteDeputy('12345', '67890');

// Lösche alle Vertretungen aus der Veranstaltung '67890':
deleteAllDeputies('67890');
```

Um der Standardvertretung einer Person die Rechte zur Bearbeitung der Profilseite zu geben oder zu entziehen, gibt es die Funktion `setDeputyHomepageRights`:

```php
// Vertretung mit ID '12345' bekommt das Recht, die 
// Profilseite von User-ID 'abcde' zu bearbeiten:
setDeputyHomepageRights('12345', 'abcde', 1);
```

# Abfrage, ob Vertretung
Über die Methode `isDeputy` kann abgefragt werden, ob eine Person Vertretung in einer Veranstaltung oder von einer bestimmten anderen Person ist. Als Parameter wird die User-ID der abzufragenden Person und die ID der Veranstaltung oder Person angegeben, wo Person 1 Vertretung sein soll. Optional kann noch mit abgefragt werden, ob es sich um eine Vertretung mit Profilbearbeitungsrechten handelt.

```php
// Ist Person 1 mit ID '12345'...
$person1_id = '12345';
// ... Vertretung von Person 2 mit ID 'abcde' ...
$person2_id = 'abcde';
// ... , egal ob mit Profilbearbeitungsrechten oder ohne.
$result = isDeputy('12345', 'abcde');

// Überprüfe zusätzlich, ob Person 1 die Profilseite von 
// Person 2 bearbeiten darf:
$result = isDeputy('12345', 'abcde', true);
```
Analog wird für Veranstaltungen abgefragt, hier darf natürlich nur ohne die Profilbearbeitungsrechte abgefragt werden. 

# Sonstige Funktionen
Über die Funktion `getValidDeputyPerms` kann abgefragt werden, welche Berechtigung eine Person mindestens haben muss, um überhaupt als Vertretung eintragbar zu sein (Höchstberechtigung ist immer 'dozent'). Momentan ist hier fest die Berechtigung 'tutor' implementiert, d.h. nur Personen mit der globalen Berechtigung 'tutor' oder 'dozent' sind erlaubt.

Die Funktion `haveDeputyPerm` überprüft für eine anzugebene User-ID, ob diese Person die nötigen Rechte hat, um als Vertretung eingetragen werden zu können.

In `getMyDeputySeminarsQuery` sind Datenbankanfragen hinterlegt, die dazu dienen, die Veranstaltungen zu finden, in denen der aktuelle User Vertretung ist. Je nach Kontext ist dies für Meine Veranstaltungen, Gruppierungs- und Benachrichtigungseinstellungen sowie den Benachrichtungs-Cronjob von Bedeutung. Die resultierende Datenbankanfrage wird über "UNION" mit der normalen Anfrage der jeweiligen Daten verbunden.
