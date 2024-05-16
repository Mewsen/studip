# Stud.IP v6.0

**15.03.2024**

## Neue Features

- Der Stud.IP-Cache ist nun kompatibel zu PSR-6. ([TIC #3701](https://gitlab.studip.de/studip/studip/-/issues/3701))

## Breaking changes

- Im Rahmen von [Issue #3788](https://gitlab.studip.de/studip/studip/-/issues/3788) wurden die Zusätze an allen Icons entfernt. Dadurch kann es sein, dass manche Plugins nicht mehr erscheinen. Diese müssen dann auf eine Variante ohne Zusätze umgestellt werden.
- Die Funktion `get_config()` wurde entfernt. Stattdessen muss die Methode `Config::get()->getValue('CONFIG_KEY')` bzw. der Shortcut `Config::get()->CONFIG_KEY` verwendet werden. ([Issue #2797](https://gitlab.studip.de/studip/studip/-/issues/2797))
- Die Funktion `smile()` wurde entfernt. Sie kann ersatzlos entfernt werden. ([Issue #3158](https://gitlab.studip.de/studip/studip/-/issues/3158))
- Die Funktion `transformBeforeSave()` wurde entfernt. Sie kann ersatzlos entfernt werden. ([Issue #3159](https://gitlab.studip.de/studip/studip/-/issues/3159))
- Die schon lange nicht mehr genutzten Methoden zum Setzen, Auslesen und Enfernen von Schmuckgrafiken von Bildern für die Sidebar wurde entfernt. Die Methoden `Sidebar::setImage()`, `Sidebar::getImage()` sowie `Sidebar::removeImage()` müssen ersatzlos entfernt werden. ([Issue #3157](https://gitlab.studip.de/studip/studip/-/issues/3157))
- Der zweite Parameter für die Methode `Navigation::setImage()` wurde entfernt. Der Parameter schien sich auf das Bild zu beziehen, hat aber Attribute an dem Link gesetzt. Stattdessen muss die Methode `Navigation::setLinkAttributes()` verwendet werden. ([Issue #3578](https://gitlab.studip.de/studip/studip/-/issues/3578))
- Die Unterstützung für LESS-Stylsheets in Plugins wurde entfernt. Als Alternative wird SCSS unterstützt. ([Issue #2720](https://gitlab.studip.de/studip/studip/-/issues/2720))
- Die Funktionen `studip_json_encode()` und `studip_json_decode()` wurden entfernt. Stattdessen müssen die Methode `json_encode()` und `json_decode()` verwendet werden. ([Issue #3814](https://gitlab.studip.de/studip/studip/-/issues/3814))
- Die `MembersModel.php` wurde entfernt ([Issue #3811](https://gitlab.studip.de/studip/studip/-/issues/3811))
- Die `admission.inc.php` wurde entfernt. ([Issue #3812](https://gitlab.studip.de/studip/studip/-/issues/3812))
- Die folgenden Funktionen wurden aus der Datei `lib/functions.php` entfernt: `re_sort_dozenten()`, `re_sort_tutoren()` und `get_next_position()` ([Issue #4002](https://gitlab.studip.de/studip/studip/-/issues/4002))
- Die Methoden `CronjobScheduler::scheduleOnce()` sowie `CronjobTask::scheduleOnce()` wurden ersatzlos entfernt. ([Issue #4078](https://gitlab.studip.de/studip/studip/-/issues/4078))
- Die folgenden Klassen wurden innerhalb von Stud.IP verschoben. Da sie über den Autoloader geladen werden, kann jedes manuelle Einbinden ersatzlos entfernt werden. ([Issue #4105](https://gitlab.studip.de/studip/studip/-/issues/4105))
  - `AuthenticatedController`
  - `PluginController`
  - `StudipController`
  - `StudipControllerPropertiesTrait`
  - `StudipResponse`
- Im Rahmen von [Issue #4118](https://gitlab.studip.de/studip/studip/-/issues/4118) wurde `write_excel` ausgebaut. Als Alternative kann `phpoffice/phpspreadsheet` verwendet werden.
- Im Rahmen von [TIC #3701](https://gitlab.studip.de/studip/studip/-/issues/3701) wurden die Klassen für den Cache umbenannt. Alle Vorkommnisse sollten ersetzt werden:
  - `StudipCacheFactory` -> `Studip\Cache\Factory`
- Die Bibliothek `opis/json-schema` wurde auf Version 2.3.0 aktualisiert ([Issue #4173](https://gitlab.studip.de/studip/studip/-/issues/4173)). Dadurch ergeben sich die folgenden Änderungen für Komponenten aus Courseware (siehe auch [Migration Guide](https://opis.io/json-schema/2.x/php-migration.html#validator)):
  - Instanzen von `Courseware\ContainerTypes\BlockType` müssen die Methode `getJsonSchema` anpassen. Der Return Type ist nun `string` und es muss der Inhalt der Schema-Datei zurückgegeben werden ohne Aufruf von `Schema::fromJsonString()`.
  - Instanzen von `Courseware\ContainerTypes\ContainerType` müssen die Methode `getJsonSchema` anpassen. Der Return Type ist nun `string` und es muss der Inhalt der Schema-Datei zurückgegeben werden ohne Aufruf von `Schema::fromJsonString()`.
- Die von Stud.IP verwendete Template-Bibliothek "Flexi Templates" wurde vollständig in den Kern integriert.
  Obwohl die Umstellung abwärtskompatibel sein sollte, sollten die Klassen folgendermassen ersetzt werden:
  - `Flexi_TemplateFactory` > `Flexi\Factory`
  - `Flexi_Template` > `Flexi\Template`
  - `Flexi_PhpTemplate` > `Flexi\PhpTemplate`
  - `Flexi_TemplateNotFoundException` > `Flexi\TemplateNotFoundException`

  Sollte ein Plugin manuell Flexi einbinden, so wird dies zu einem Fehler führen. Jegliches Einbinden von Dateien
  unterhalb von `vendor/flexi` muss ersatzlos entfernt werden.

## Security related issues

-

## Deprecated Features

-

## Known Issues

-
