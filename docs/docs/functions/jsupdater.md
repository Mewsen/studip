---
title: Periodische AJAX Updates
---

Man stelle sich vor, ein Plugin möchte, ohne die Stud.IP-Seite neu laden zu lassen, Informationen darüber abrufen, welches Buddys gerade online sind (so das OnlineBadge-Plugin aus Osnabrück). Ein Messenger möchte alle paar Sekunden abrufen, ob der Nutzer neue Nachrichten bekommen hat. Und die Seite selbst will abrufen, ob es im Forum einen neuen Beitrag gibt. Für Stud.IP war das zuvor nur umständlich möglich. Bei gängigen sozialen Netzwerken ist das hingegen Gang und Gebe.

Ab der Version Stud.IP 2.2 gibt es einen zentralen Mechanismus, damit in dem obigen Fall nicht drei AJAX-Requests pro Sekunde gestartet werden. Das würde selbst leistungsfähige Server an den Rand der Verzweiflung führen. Wenn die Plugins die Klasse UpdateInformation benutzen, um die Daten zu bekommen, entlastet das den Server und den Programmierer, da er sich um einige Dinge nicht mehr kümmern muss.

# Usecase 1: Plugins

Ein Plugin, das zum Beispiel Nutzer anzeigen soll, die online sind, bedient sich der Klasse UpdateInformation, die in der Datei lib/classes/UpdateInformation.class.php liegt. Das Plugin sollte ein Systemplugin sein, damit es (also der Konstruktor) aufgerufen wird, bevor der Trails-Controller app/controllers/jsupdater.php zum Zuge kommt und die Daten wiederum abholt.

Im PHP-Quellcode des Konstruktors des Systemplugins steht dann der Aufruf in etwa so:

```php
UpdateInformation::setInformation('myplugin', $data)
```

Die PHP-Variable $data (ein String, eine Zahl oder ein Array) wird dann an die Implementation des Updaters auf der JavaScript-Seite übergeben.

Auf der JavaScript-Seite muss sich das Plugin registrieren, um diese Benachrichtigungen zu erhalten:

```javascript
STUDIP.JSUpdater.register("myplugin", receiveCallback, sendCallbackOrData)
```

Daraufhin passiert folgender Weg:

* Die Javascript-Funktion STUDIP.JSUpdater.call wird alle paar Sekunden (mal häufiger, mal schneller, je nach Servergeschwindigkeit, Datenlage und Benutzeraktivität) aufgerufen und ruft selbst per Ajax-Request die Seite dispatch.php/jsupdater/get auf.
* Hinter dispatch.php/jsupdater verbirgt sich der Trails-Controller in app/controllers/jsupdater.php und dessen Methode get_action().
* Bevor der Controller irgendwas macht, werden die Konstruktoren der Systemplugins aktiviert. Das Plugin myplugin sammelt Daten und gibt sie an UpdateInformation::setInformation weiter. Die Daten sind jetzt registriert und bereit zur Weitergabe.
* JsupdaterController ist jetzt dran und ruft UpdateInformation::getInformation() auf.
* Bei UpdateInformation::getInformation() gibt die Daten, die es von den Plugins bekommen hat als Array zurück.
* JsupdaterController nimmt dieses riesige Array als ein JSON-Objekt.
* Dieses JSON-Objekt kommt bei der Javascript Funktion STUDIP.JSUpdater.processUpdate an, die die einzelnen Benachrichtigungen an die entsprechenden registrierten Handler weitergibt.

# Usecase 2: Kernfunktionalität

Auch wenn man es manchmal vergisst: Funktionalität in Stud.IP wird nicht nur durch Plugins bereit gestellt. Kernfunktionalität, die periodische AJAX-Updates verwenden möchte, benutzt nicht die Klasse UpdateInformation, sondern erweitert die Methode JsUpdaterController#coreInformation() einfach um ein paar Zeilen. Dort wird ein Array $data initialisiert und am Ende zurück gegeben, das assoziativ ist. Die Indexeinträge sind der Name der Javascript-Funktion (ohne "STUDIP." davor) und die Values sind dann beliebig.

Auf PHP-Seite gibt es diese weiteren Methoden zum Zugriff auf übergebene Daten in Javascript:

* `UpdateInformation::hasData($index)` - prüft, ob Daten unter dem angegebenen Index vorliegen
* `UpdateInformation::getData($index)` - liefert die Daten unter dem angegeben Index zurück (bzw. `null`, falls keine Daten vorliegen.

Auf JavaScript-Seite sieht die API des JS-Updaters so aus:

* `STUDIP.JSUpdater.start()` - Startet den JS-Updater
* `STUDIP.JSUpdater.stop()` - Beendet den JS-Updater
* `STUDIP.JSUpdater.register(index, receiveCallback, sendCallbackOrData)` - Meldet ein neues Objekt unter dem angegeben Index beim Updater an. Zurückgelieferte Daten werden von dem angegebenen `receiveCallback` verarbeitet und in `sendCallbackOrData` angegebene Daten werden bei jedem Aufruf des Updaters mitgesendet. `sendCallbackOrData` kann dabei entweder ein reguläres JavaScript-Array, ein Objekt oder eine Funktion sein, die die Daten dynamisch zurückliefert.
* `STUDIP.JSUpdater.unregister(index)` - Entfernt ein zuvor angemeldetes Objekt.

Der JS-Updater wurde optimiert, so dass zum einen zu jedem Zeitpunkt nur noch ein einziger Aufruf des Updaters stattfindet und zum Anderen wird besser auf Lastsituationen am Server reagiert. Ebenso wird die Anzahl der Abfragen reduziert, wenn sich das Fenster mit Stud.IP im Hintergrund befindet und somit inaktiv ist.
