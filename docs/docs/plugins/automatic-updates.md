---
title: Automatische Plugin-Updates
slug: /plugins/automatic-updates
sidebar_label: Automatische Updates
---

### Installation

Ab der Version 3.2 kann man in Stud.IP Plugins direkt von github oder anderen Repositories wie gitlab installieren. Dazu geht man als Root in die Pluginverwaltung und klickt in der Sidebar auf "Plugin von URL installieren".

Es öffnet sich ein Dialogfenster, in der man die URL eingibt. Diese URL muss zum ZIP-Download auf github führen. Es reicht also nicht, die Grund-URL des Repositories anzugeben wie https://github.com/studip/PluginMarket, sondern man muss da die URL zur ZIP-Datei angeben wie https://github.com/studip/PluginMarket/archive/master.zip . So eine URL sollte es bei den meisten Systemen wie gitlab, github oder bitbucket geben.

Jetzt kann man direkt auf Speichern klicken und das Plugin wird installiert.

### Einrichtung

Was einmal klappt, kann auch öfter klappen. Warum also sollte Stud.IP das aus dem Web installierte Plugin nicht öfter automatisch installieren und so immer auf dem neusten Stand bleiben? Genau, es spricht kaum etwas dagegen. Stud.IP wird das aber erst tun, wenn github sich bei Stud.IP meldet und Bescheid gibt, dass ein Update des Plugins vorliegt.

Der Vorgang wird dann so aussehen:
* Eine Änderung des Plugins wird ins Repository eingecheckt.
* Das Repository meldet sich per Webhook bei Stud.IP mit der Nachricht "bei Plugin xyz hat sich was geändert".
* Stud.IP überprüft diesen Webhook-Request, denn da könnte ja jeder kommen. Nur wenn auch wirklich das richtige Repository anruft, wird Stud.IP das auch ernst nehmen.
* Stud.IP wird dann von sich aus die eingerichtete URL des ZIP-Downloads wieder aufrufen und das veränderte Plugin installieren.

Damit das funktioniert, muss einerseits Stud.IP als auch das Repository speziell eingerichtet werden.

Stud.IP braucht die URL des ZIP-Downloads und die Angabe, ob der Webhook über einen Security-Token abgesichert werden soll. Die Absicherung per Security-Token funktioniert zur Zeit nur mit github.

Das Repository muss die genaue URL kennen, die vom Webhook aufgerufen werden soll. So eine URL sieht in etwa so aus: 
http://www.superstudip.de/studip/dispatch.php/plugins/trigger_automaticupdate/OnlineList?s=8d1e6b52927a7f5f567f7aedeb8b17b0
Diese URL beinhaltet schon einen Sicherheitstoken; nur wer den Token, also die exakte URL kennt, kann überhaupt den Request aufrufen. Dazu muss gesagt werden, dass Tokens in URLs nicht besonders sicher sind. Aber sie sind besser als nichts. Und bei gitlab oder anderen Systemen ist dies zur Zeit die einzige mögliche Absicherung.
Falls gewünscht, kann in github der Webhook noch über einen Security-Token abgesichert werden. Damit ist NICHT der Token aus der URL gemeint, sondern der separate Token, der in Stud.IP unter der URL angezeigt wird.

Sind Stud.IP und das Repository gleichermaßen eingerichtet, so ist eigentlich alles getan.

### Wichtig

Wir empfehlen nicht, diese automatischen Updates für den Produktivbetrieb einzusetzen. Aber für Testsysteme können sie Gold wert sein. Besonders bei umfangreichen und komplizierten Plugins will man vielleicht immer die ganzen Plugins nach jeder Änderung über die Oberfläche von Stud.IP hochladen. Man bedenke, dazu muss man als Root angemeldet sein, zur Pluginverwaltung gehen, in die Sidebar klicken, runter scrollen, den Dateiupload anklicken, feststellen, dass man vergessen hat, das Plugin zu zippen, dann zippt man das Plugin, wählt nochmal die Datei aus. Und dann dauert es je nach Größe des Plugins noch quälend lange, bis das Plugin hochgeladen worden ist. Und mal ehrlich: wer hat in dem ganzen Prozedere noch nie ein fertig gezipptes Plugin mit ins Repository hochgeladen?

Automatische Updates vereinfachen also das Testen mit Testservern ungemein. Man muss nur noch den Fortschritt des Plugins ins Repository pushen, was man eh machen muss, und die angeschlossenen Testsysteme aktualisieren sich alle gleichzeitig. Wenn man mehrere Testsysteme hat (bei der Entwicklung vom CampusConnect-Plugin ist das zum Beispiel absolut notwendig), verhindert das automatische Update so auch zielsicher, dass man irgendwo ein Update vergessen hat und dann Fehlern nachjagd, die im Code gar nicht existieren.

Ebenso hat das automatische Update den Vorteil, dass der Entwickler des Plugins nicht notwendigerweise Root-Zugriff auf das Testsystem haben muss, um Updates einspielen zu können.
