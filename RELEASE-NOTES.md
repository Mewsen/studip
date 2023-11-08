# Stud.IP v5.4

**08.11.23**

## New Features

### System: 
- Komplett neu entwickelte Verzeichnisstrukturen
- Unter Administraion -> Standort haben Root die Möglichkeit, einen systemweiten Pool an Bildern anzulegen und Lizenzen anzugeben. Die Bilder können dann von Lehrenden in Courseware genutzt werden.
- Barrierefreiheitserklärung und Meldefunktion sind nun im Footer integriert. Eine Mustererklärung ist enthalten, diese muss aber angepasst werden. 
- Neben dem Vollbild-Modus (eingeführt in der Version 5.0), der nur in bestimmten Kontexten gezeigt wird, gibt es nun einen Modus "kompakte Navigation". Der neue Modus wird über das bisherige Icon für den Vollbildmodus aktiviert. Bitte passen Sie ihre Dokumentationen an.
- Root können"Banner" auf der Verwaltungsseite für Werkzeuge in Veranstaltungen schalten und damit z.B. auf neue Funktionen hinweisen.
- Der Hinweistext, der Nutzenden unter Profil->Persönliche Angaben->Grunddaten angezeigt wird, wenn die Grunddaten dort nicht änderbar sind, weil bspw. Shibboleth verwendet wird, lässt sich nun global konfigurieren. Damit kann ein Hinweis hinterlegt werden, an welche Stelle man sich zur Änderung der Grunddaten wenden soll.
- Veranstaltungen auf der "Meine Veranstaltungen"-Seite lassen sich nun auch nach Modulen gruppieren (wenn das Modularisierte Vorlesungsverzeichnis MVV verwendet wird)

### Raumverwaltung: 
- Vereinfachte Raumanfragen 
- Sammelaktionen

### Veranstaltungen: 
- Hauptordner im Dateibereich lässt sich für Uploads von Studierenden/Teilnehmenden sperren.
- Die "Mehr"-Seite zur Verwaltung von Veranstaltungswerkzeugen gibt es nicht mehr als separaten Reiter. Alle Funktionen der "mehr"-Seite sind nun unter "Verwaltung" zu finden - bitte weisen Sie die Lehrenden unbedingt darauf hin und passen Sie ggf. ihre Dokumentation an.
- Verwaltungsseite wurde komplett in vue.js neu programmiert.  

### Courseware: 
- Jede Courseware ist nun ein einzelnes Lernmaterial und kann kopiert, exportiert, importiert werden und gibt Lernfortschritt an.
- Sammelmappe für Abschnitte und Blöcke
- Übersichtsseite für Feedback und Kommentare
- Neue Blöcke zur Darstellung eines Lebenslaufs
- Funktionen zum Teilen von Seiten an Personen und Gruppen, ermöglicht niedrigschwelliges Peerfeedback
- Übersichtsseite für Lehrende über verteilte Aufgaben mit Bearbeitungsstatus, Feedbackfunktion und Fristverlängerungsanfrage

## Breaking changes

-

## Security related issues

-

## Deprecated Features

- Das Verwenden von LESS-Stylesheets in Plugins wurde deprecated und wird zu Stud.IP 6.0 entfernt werden. Die betroffenen Plugins müssen angepasst und auf SCSS umgestellt werden.
- Die REST-API ist als deprecated markiert und wird perspektivisch entfernt. Neue Entwicklungen sollten nicht darauf aufbauen. 
- Evaluationen werden perspektivisch entfernt, wenn die "Fragebögen"-Funktion dem Funktionsumfang der Evaluationen gleich kommt.

## Known Issues

- Der Vollbildmodus funktioniert nicht auf Apple iPads. Der Modus kann zwar initiiert werden, beendet sich aber selbsständig, wenn nach oben gescrollt wird. Dieses Verhalten ist en Fehler innerhalb von iOS/iPadOS und kann seitens Stud.IP nicht umgangen werden. Der Fehler ist bei Apple gemeldet.

## Other
- Mindestanforderung an Node.JS ist nun Version 16
