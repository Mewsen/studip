# Stud.IP v5.5

**18.06.24**

## Neue Features

### System

- Neues Registrierungsformular
- Neue Login-Seite
- Erklärung in leichter Sprache
- komplett neu entwickelte externe Seiten
- Impressum kann optional Seiten für nicht eingeloggte Nutzer ausblenden, Reihenfolge von Seiten lassen sich ändern, neuer Entwurfsmodus
- Mehr Bilder werden als WebP gespeichert
- Neben dem Vollbild-Modus (eingeführt in der Version 5.0), der nur in bestimmten Kontexten gezeigt wird, gibt es nun einen Modus "kompakte Navigation". Der neue Modus wird über das bisherige Icon für den Vollbildmodus aktiviert. Bitte passen Sie ihre Dokumentationen an.

### Modul- und Vorlesungsverzeichnis (MVV)
- Logging von Personen und Dateizuordnungen
- Suche (Filter) nach Abschlüssen und Fächern im Backend

### Veranstaltungen

- neu entwickelte Exporte
- neu entwickeltes Wiki
- Import von ILIAS-Ergebnissen in das Stud.IP-Gradebook
- Verbesserungen bei der Auswertung von Fragebögen

### Persönliche Services

- Komplett überarbeiteter Terminkalender auf Basis von FullCalendar

### Courseware

- Bewertung von Lernmaterialien
- Erweiterte Courseware-Zertifikate
- Blubber Block
- Werkzeugleiste
- Neue Sortierfunktionen für Lernmaterialien
- Seiten im Inhaltsverzeichnis anlegen und umbenennen
- Lerninhalte und Seiten hinzufügen, kopieren und importieren als eine Aktion

## Breaking changes

- Mindestanforderung an PHP auf 7.4 angehoben
- Mindestanforderung MySQL 5.7.8 oder MariaDB-10.2.7

## Security related issues

-

## Deprecated Features

- Das Verwenden von LESS-Stylesheets in Plugins wurde deprecated und wird zu Stud.IP 6.0 entfernt werden. Die betroffenen Plugins müssen angepasst und auf SCSS umgestellt werden.

## Known Issues

- Der Vollbildmodus funktioniert nicht auf Apple iPads. Der Modus kann zwar initiiert werden, beendet sich aber selbsständig, wenn nach oben gescrollt wird. Dieses Verhalten ist en Fehler innerhalb von iOS/iPadOS und kann seitens Stud.IP nicht umgangen werden. Der Fehler ist bei Apple gemeldet.
