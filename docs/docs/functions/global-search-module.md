---
id: global-search-module
title: Module für die globale Suche
siebar_label: Globale Suche
---

# Module für die globale Suche
Die globale Suche (ab Stud.IP 4.1) hat für jede Suchkategorie eine eigene Klasse, die die nötigen Operationen ausführt, um passende Suchergebnisse zu finden.

Jedes Modul stammt von der abstrakten Oberklasse `GlobalSearchModule` ab und implementiert folgende Methoden:

## Methoden
* `getName()` liefert einen Anzeigenamen für das Modul zurück. Dieser erscheint in der Konfiguration der Suchmodule und in der kategorisierten Übersicht der Suchergebnisse.
* `getSQL($search)` generiert die SQL-Anfrage, die ausgeführt werden soll, um die Suchergebnisse aus der Datenbank zu lesen. Hier muss vollständiger SQL-Code zurückgegeben werden, also kein Prepared Statement oder SQL mit Parametern. Um die Suchanfrage performant zu halten, sollte `LIMIT` verwendet werden, um die Anzahl der Ergebnisse zu beschränken. Über die Variable `GLOBALSEARCH_MAX_RESULT_OF_TYPE` aus der globalen Konfiguration wird gesteuert, wie viele Ergebnisse pro Kategorie in der Schnellsuche angezeigt werden. Da jede Kategorie klickbar ist und dann mehr Ergebnisse anzeigt, kann z.B. `LIMIT  (4 * Config::get()->GLOBALSEARCH_MAX_RESULT_OF_TYPE)` angegeben sein.
* `filter($data, $search)` bereit ein einzelnes Suchergebnis zur Weiterverarbeitung vor. Hier wird eine einzelne Datenbankzeile übergeben, aus der dann die für die Darstellung benötigten Attribute erzeugt werden. Die Rückgabe ist ein Array von der Art

```php
[
    'id' => <Stud.IP-ID des Objekts>,
    'name' => <Titel/Name des Objekts, am besten über GlobalSearchModule::mark gekennzeichnet>,
    'url'  => <URL zum Aufruf des Ergebnisses im System, z.B. Profil, Veranstaltungsseite etc.>,
    'date' => <Erzeugungs-/Änderungsdatum/Semester>,
    'description' => <Ausführlichere Information, Beschreibungstext, Textausschnitte etc, am besten über GlobalSearchModule::mark gekennzeichnet>,
    'additional' => <Weitere Daten, wie z.B. Untertitel, Liste von Dozierenden etc.>,
    'expand' => <URL zur weiterführenden Suche>,
    'img' => <URL eines Bildes/Avatars für dieses Ergebnis>
]
```
* `getSearchURL($searchterm)` liefert die URL einer weiterführenden Suche, z.B. Forensuche innerhalb einer Veranstaltung oder Stud.IP-weite Veranstaltungssuche
## Markierung des Suchbegriffs
Um zu kennzeichnen, warum ein Ergebnis überhaupt angezeigt wird, kann die statische Methode `GlobalSearchModule::mark($string, $query, $longtext = false, $filename = true)` verwendet werden, die in einem gegebenen String das Suchwort markiert und ihn ggf. kürzt. Hierfür wird das HTML-Tag `<mark>` verwendet.
# GlobalSearchFulltext
Statt der "normalen" SQL-Suche über `LIKE` kann auch in bestimmten MySQL-Versionen die Volltextsuche via `MATCH AGAINST` verwendet werden. Hierfür muss das Interface `GlobalSearchFulltext` implementiert werden. Dieses besitzt drei Methoden:
* `enable()`: Aktionen, die beim Aktivieren der Volltextsuche dieses Moduls ausgeführt werden sollen, z.B. Erzeugung von nötigen Tabellenindizes
* `disable()`: Aktionen, die beim Abschalten der Volltextsuche dieses Moduls ausgeführt werden sollen. z.B. Entfernen von Tabellenindizes
* `getFulltextSearc($search)` generiert analog zu `GlobalSearchModule->getSQL()` die SQL-Anfrage, um die Suchergebnisse aus Datenbank zu holen
# Aktivierung und Sortierung von Suchmodulen
Eine neue Suchmodulklasse muss noch unter Admin->Globale Suche aktiviert werden, dort kann auch per Drag & Drop sortiert werden, in welcher Reihenfolge die Module abgefragt bzw. die Suchergebnisse angezeigt werden.
