---
id: datenbank
title: Einstieg in die Datenbank von Stud.IP
sidebar_label: Datenbank
---


Die Datenbank in Stud.IP umfasst etwa 100 Tabellen, wobei etliche Tabellen zum Beispiel von Plugins noch hinzukommen können. Dies hier ist ein kleiner und viel zu später Versuch, diese Tabellen zu dokumentieren.

Es gibt einige Dinge, die in den Tabellen stehen, die sich niemals einem Programmierer erschließen würden, wenn er einfach nur in den Quelltext schauen würde. Es geht bei der Dokumentation nicht darum, den Blick in den PhpMyAdmin zu ersetzen. Deswegen ist die Dokumentation auch nicht vollständig und macht sich über genauste Typangaben nicht so viele Gedanken. Stattdessen sollen Felder erklärt werden wie seminare.duration_time, in denen Zahlen von -1 bis Unendlich drin stehen, die aber alle etwas anderes bedeuten können.

## Grundkonventionen

| Typ | Beschreibung |
| ---- | ---- |
| Primärschlüssel | In den Tabellen von Entitäten wie seminare oder Institute werden als Primärschlüssel sehr häufig md5-Hashes verwendet. Das hat den historischen Hintergrund, dass man IDs auf die Weise fast gar nicht erraten kann. Numerische IDs kann man sehr gut erraten. Natürlich ist dies ein minderwertiger Schutz vor Hackerangriffen auf geschützte Daten. Deswegen werden in Stud.IP die Daten mittlerweile anders geschützt. Aber wenn Primärschlüssel einmal gesetzt sind, kann man sie nur sehr schlecht wieder in numerische IDs umwandeln. Deswegen ist es stets bei den md5-Hashes geblieben mit Ausnahme von einigen neueren Tabellen. |
| Zeitstempel | Stud.IP verwendet ausschließlich (falls es doch eine Ausnahme geben sollte, bestätigt sie die Regel) Integer-Werte als Unix-Timestamps, also die Anzahl der Sekunden seit dem 1.1.1970. Die meisten Tabellen haben zwei Felder `mkdate` und `chdate`, die man immer ausfüllen sollte. `mkdate` ist der Zeitpunkt des Erstellens des Datensatzes und `chdate` ist der Zeitpunkt des letzten Änders des Datensatzes.|

## Tabellen

#### auth_user_md5
Die Tabelle auth_user_md5 ist eine der wichtigsten Tabellen für den Programmierer, weil sie für den Nutzer steht. Über diese Tabelle wird die Anmeldung geregelt und seine Email steht hier drin. Es gibt noch eine zweite Tabelle, die in einer 1-zu-1 Verknüpfung mit der auth_user_md5 steht, und zwar die user_info. Dort stehen noch einige weitere Informationen wie Hobbys und so weiter.

| Tabelle | Beschreibung |
| ---- | ---- |
| **user_id** | Eine eindeutige Identifikationsnummer, Primärschlüssel der Tabelle. |
| **username** | Die Anmeldekennung, soll in URLs statt der user_id verwendet werden, damit es schöner aussieht. |
| **password** | Das mittels MD5-Hash verschlüsselte Passwort. Das bedeutet, dass selbst ein Systemadmin nicht das Passwort aus der Datenbank auslesen könnte. Bei Nutzern, die über LDAP oder irgendwie anders authentifiziert werden, ist das Passwort-Feld leer. |
| **perms** | Globale Nutzerrechte. In Stud.IP hat ein Nutzer globale Rechte, damit zum Beispiel klar ist, dass er als 'dozent' Veranstaltungen anlegen kann. Zusätzlich gibt es auch ein lokales Rechtesystem, das hiermit aber kaum was zu tun hat. Grundsätzlich sollte jeder Nutzer von Stud.IP nirgendwo im System lokal mehr Rechte haben als global. |
| **Vorname** | Vorname des Nutzers, als Feld groß geschrieben |
| **Nachname** | Nachname des Nutzers, als Feld ebenfalls groß geschrieben. |
| **Email** | Email-Adresse des Nutzers, als Feld ebenfalls groß geschrieben. Es sollte wie ein UNIQUE-Feld behandelt werden, auch wenn es das formal nicht ist. |
| **auth_plugin** | Gibt an, ob und wie ein Nutzer authentifiziert wird. Ist das Feld leer (NULL) oder mit "standard" gefüllt, wird er über das in `password` gespeicherte Passwort in Stud.IP authentifiziert. Andere Angaben führen dazu, dass ein AuthPlugin die Authentifizierung übernimmt. |
| **locked** | bei 1 ist der Nutzer gesperrt, kann sich also nicht mehr anmelden, bei 0 ist er nicht gesperrt. |



#### Institute

Achtung, diese Tabelle wird sogar im Namen groß geschrieben! Sie ist damit die einzige echte Tabelle in Stud.IP, die groß geschrieben wird.

In dieser Tabelle stehen alle Informationen zu Fakultäten/Einrichtungen/Instituten/Klassen in Stud.IP.

| Attribut | Beschreibung |
| ---- | ---- |
| **Institut_id:**  | Primärschlüssel der Tabelle. Über die Institut_id (Achtung, Großschreibung beachten!) wird eine Einrichtung identifiziert. |

#### seminar_cycle_dates

In dieser Tabelle werden Metatermine gespeichert, was nichts anderes ist als regelmäßige Termine. Wenn ein Kurs beispielsweise einen Montagstermin hat von 10 bis 12 Uhr, dann umfasst das eine Anzahl von 12 Terminen, die in der Tabelle `termine` liegen, aber es gibt eben auch einen Meta-Termin, der alle 12 Termine auf einmal repräsentiert. Wenn man den Metatermin verändert, so werden auch alle Einträge in der Tabelle `termine` mit verändert - zumindest tun das die verantwortlichen Klassen in Stud.IP.

Da es im universitären Bereich nicht nur wöchentliche Termine gibt, sondern auch zweiwöchentliche und Termine, die an jedem ersten Donnerstag eines Monats stattfinden, ist die Sache mit den Metaterminen doch etwas kompliziert geworden.

| Attribut | Beschreibung |
| ---- | ---- |
| **weekday** | Eine Zahl von 1 bis 7, wobei 1 für Montag steht und 7 für Sonntag. |
| **week_offset** | Anzahl der Wochen, die der Termin am Anfang des Semester noch nicht startet. Bei 0 startet der Termin also genau dann, wenn das Semester begonnen hat, bei 1 erst eine Woche später und so weiter. |
| **cycle** | Entweder 0, 1 oder 2, wobei 0 für wöchentlich, 1 für zweiwöchentlich und 2 für dreiwöchentlich steht. |


## Datenbankzugriff

### PDO
Stud.IP verwendet standardmäßig die MySQL-Datenbank. Um auf diese Datenbanken zuzugreifen wurde sowohl die Klasse DBManager angelegt und PDO benutzt. Alle Datenbankzugriffe in Stud.IP sollen ab jetzt direkt über PDO funktionieren. Im Quellcode bedeutet dies konkret, dass ein Datenbankzugriff so ähnlich aussieht:

```php
$db = DBManager::get();
$result = $db->query("SELECT * FROM user_info WHERE Nachname = '".$name."'")->fetchAll();
foreach ($result as $nutzer) {
}
```

Der DBManager sorgt für die Verbindung zur Datenbank und PDO regelt also die Zugriffe, sobald das Verbindungsobjekt $db erst einmal initialisiert ist.

Weitere Infos zu PDO findet man unter [php.net](http://de2.php.net/manual/en/class.pdo.php).

Mehr Beispiele und erweiterte Funktionalität findet man hier:
[StudipPDO](StudipPDO)

### Slave-Zugriffe

Wenn man lesende Zugriffe hat, deren Korrektheit nicht 100% gewährleistet sein muss (autocompleter), kann man zur Steigerung der Performance auch den Slave ansprechen:

```php
$db = DBManager::get("studip-slave");
$result = $db->query("SELECT * FROM user_info WHERE Nachname = '".$name."'")->fetchAll();
foreach ($result as $nutzer) {
}
```

Wenn die Installation keine Replikation verwendet, werden die Anfragen an den Slave automatisch auf den (einzigen) Master gerichtet.

# SQL-Injections
Wichtig um SQL-Injections zu verhindern: Es gibt zwei Methoden, um eklige Datenbankhacks durch Einspeisen von bösem SQL-Code zuvor zu kommen. Das obige Beispiel ist im Grunde noch nicht geschützt.

1.) Wannimmer man eine potentiell gefährliche Variable in ein SQL-Statement einfügt, sollte die Variable über $db->quote($name) geschehen. Der Quellcode dazu sieht dann so aus:

```php
$db = DBManager::get();
$result = $db->query("SELECT * FROM user_info WHERE Nachname = '".$db->quote($name)."'")->fetchAll();
foreach ($result as $nutzer) {
}
```

2.) Man sollte bei einem System wie Stud.IP immer auch auf die Performance achten. Dazu kann es sehr sinnvoll sein, häufig auftauchende Befehle über prepare vorzubereiten, damit die Datenbank ein- und denselben Befehl nicht jedes mal neu umsetzen muss.

```php
$db = DBManager::get();
$preparation = $db->prepare("SELECT * FROM auth_user_md5 WHERE Nachname = :name", array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$preparation->execute(array('name' => $name));
$result = $preparation->fetchAll();
foreach ($result as $nutzer) {
}
```

Dies ist im Zweifelsfall die saubere Variante, weil sie der Datenbank das Leben erleichtert und zudem SQL-Injections automatisch verhindert.
