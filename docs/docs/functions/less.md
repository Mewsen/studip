---
id: less
title: Stylesheets in LESS
sidebar_label: Less
---


Ab Version 2.3 ([#2602](https://develop.studip.de/trac/ticket/2602)) von Stud.IP werden Stylesheets in LESSCss (http://lesscss.org) geschrieben und in normales CSS kompiliert.

Das Kompilieren der .less-Dateien ist nun Teil des Buildprozesses bzw. kann auch separat mittels `make` auf der Kommandozeile angestossen werden.

## Spezielle Variablen und Mixins für Stud.IP

### Pfade

| Variable | Beschreibung |
| ---- | ---- |
| `@image-path` | Pfad zu den Bildern von Stud.IP, entspricht im Standardfall `public/assets/images`|

### Bilder

| Variable | Beschreibung |
| ---- | ---- |
| `.retina-background-image(@normal, @retina)` | Bindet ein Hintergrundbild für Retina-Displays in zwei Größen ein. Die Bilder werden hierbei im Image-Pfad von Stud.IP erwartet. |
Für Retina-Bilder stehen die beiden folgenden Funktionen zur Verfügung:


### Icons

Ab v3.4 können Stud.IP-Icons in LESS/CSS über folgenden Mixins eingebunden werden:

| **Zweck**                             | **Signatur**                                | **Beispiel**|
| ---- | ---- | ---- |
|**Icon im Hintergrund**                |`.background-icon(shape, role);`             |`.background-icon('seminar', 'clickable');` |
|**Button mit Icon inkl. Hover-Effekt** |`.button-with-icon(shape, role, role_hover)` |`.button-with-icon("accept", "clickable", "info_alt")` |
|**Icon als BG in ::before**            |`.icon("before", shape, role)`               |`.icon("before", "arr_1right", "clickable")`|
|**Icon als BG in ::after**             |`.icon("after", shape, role)`                |`.icon("after", "arr_1right", "clickable")` |

### Farben

// TODO: Tatsächliches Farbschema als Screenshot anzeigen
// TODO: Namensräume besser beschreiben

Seit Version 3.0 gibt es für Stud.IP ein eigenes Farbschema, auf welches - soweit möglich - immer zurückgegriffen werden sollte.

| Farbe | Beschreibung |
| ---- | ---- |
| `@red` | Ein Rot-Ton |
| `@orange` | Ein Orange-Ton |
| `@activity-color` | Kennzeichnet Aktivitätsmöglichkeiten |
| `@dark-gray-color` | Dunkles Grau |
| `@light-gray-color` | Helles Grau |
| `@content-color` | Farbe für Inhalte |
| `@base-color` | Grundfarbe |


Alle Farben gibt es jeweils in vier weiteren Abstufungen, bei denen jeweils 80%, 60%, 40% bzw. 20% der Originalfarbe gemischt wurden. Diese Farben spricht man indem, man der Farbvariable den jeweiligen Prozentsatz mit einem - getrennt anhängt, bspw. `red-60`.

## Deprecated: LESS in Plugins

Die folgenden Vorschläge werden sich in Zukunft (vermutlich Stud.IP v5) ändern und sind damit nicht zukunftssicher.

Die Klasse `StudipPlugin` stellt die Methode `addStylesheet()` zur Verfügung, über welche LESS auch in Plugins verwendet werden kann. Dazu muss dieser Funktion der Name der LESS-Datei **relativ** zum Pluginpfad angegeben werden. Dadurch wird die LESS-Datei kompiliert und gleichfalls in der Seite zur Verfügung gestellt. Alle Mixins, die im Kern zur Verfügung stehen, stehen auch dem Plugin zur Verfügung.

Darüber hinaus steht seit Stud.IP 3.4 den Plugins im LESS die Variable `@plugin-path` zur Verfügung, um auf Dateien innerhalb des Pluginverzeichnisses zu referenzieren.

#### Deprecated: Eigene Implementierungen zum Speichern der kompilierten Dateien

Falls die Speicherung der kompilierten Dateien geändert werden soll, kann der `Assets\Storage` über die Methode `setFactory()` eine Instanz einer eigenen Implementierung von `Assets\AssetFactory` übergeben, welche spezialisierte `Assets\Asset`-Objekte erzeugt, die die Speicherung anders verwalten können. Auch der Downloadpfad für den Zugriff auf die Dateien kann dabei entsprechend abgeändert werden. Für nähere Informationen wird auf die Interfaces [AssetFactory](https://develop.studip.de/trac/browser/trunk/lib/classes/assets/AssetFactory.php) und [Asset](https://develop.studip.de/trac/browser/trunk/lib/classes/assets/Asset.php) bzw. deren konkreten Kern-Implementierungen [PluginAssetFactory](https://develop.studip.de/trac/browser/trunk/lib/classes/assets/PluginAssetFactory.php) und [PluginAsset](https://develop.studip.de/trac/browser/trunk/lib/classes/assets/PluginAsset.php) verwiesen.

Eine solche Änderung kann über ein SystemPlugin eingespielt werden, sofern dieses als erstes Plugin geladen wird (kleinste "Position" in der Pluginverwaltung des Systems).
