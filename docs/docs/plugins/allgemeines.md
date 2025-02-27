---
id: plugins
title: Plugins
sidebar_label: Allgemeines
---

### Einleitung

Da jeder Standort, an dem Stud.IP eingesetzt wird, ganz eigene Anforderungen oder Einschränkungen hat, wird mit der Plugin-Schnittstelle ein Mechanismus angeboten, über den man eigene Funktionen zu Stud.IP hinzufügen kann, ohne dabei das Kernsystem anfassen zu müssen. Das Aktualisieren, Entfernen oder Hinzufügen von Komponenten ist dabei im laufenden Betrieb möglich.

Plugins können eigene Seiten im Stud.IP-System anbieten, die an bestimmten Stellen in die Navigationsstruktur eingebunden werden können, z.B. als neuer Reiter in einer Veranstaltung. Darüber hinaus verfügen bestimmte Plugin-Typen auch über die Möglichkeit, auf bestehende Seiten Einfluß zu nehmen und damit z.B. einen eigenen Block auf der Stud.IP-Startseite anzuzeigen.

### Was ist ein Stud.IP-Plugin?

Ein Plugin ist eine ZIP-Datei, die eine Beschreibung des Pugins (Name, Version, usw.), den Programmcode und ggf. von Plugin mitgebrachte Ressourcen (Bilder, Stylesheets usw.) enthält. Im einzelnen kann ein Plugin-Paket die folgenden Komponenten enthalten:

* eine Manifest-Datei mit dem Namen `plugin.manifest`
* mindestens eine PHP-Klasse mit dem Programmcode des Plugins
* optional weitere Dateien mit statischen Inhalten (Bilder, CSS-Stylesheets, JavaScript-Dateien) oder PHP-Bibliotheken
* optional ein SQL-Skript zur Erzeugung des Datenbankschemas für das Plugin
* optional ein SQL-Skript zum Löschen des Datenbankschemas für das Plugin
* optional ein Unterverzeichnis mit dem Namen `migrations`, welches [Migrations-Dateien](../functions/migrations.md) enthält
* optional ein Unterverzeichnis mit dem Namen `locale`, das [Übersetzungsdateien](../quickstart/internationalisierung.md) enthält
* optional ein Unterverzeichnis mit dem Namen `templates` mit Templates zur Darstellung

Ein Beispiel für die Verzeichnisstruktur in einem Plugin-Paket:

```ini
MyPlugin.class.php
  plugin.manifest
  images/
    icon.png
  migrations/
    1_test.php
    2_foobar.php
  sql/
    install.sql
    uninstall.sql
  stylesheets/
    grid.css
  templates/
    page.php
```


### Bestandteile eines Plugins

In der folgenden Abschnitten werden die verschiedenen Bestandteile eines Plugin-Pakets der Reihe nach erklärt:

#### Plugin-Manifest

Jedes Plugin-Paket muß ein sogenanntes "Plugin-Manifest" enthalten, in dem wichtige Informationen über das Plugin für die Installation und Verwaltung des Plugins enthalten sind. Das Plugin-Manifest liegt immer im Wurzelverzeichnis des Plugins und hat den Namen `plugin.manifest`. Es ist eine Textdatei und kann bzw. muß folgende Einträge enthalten:

| Wert | Beschreibung |
| ---- | ---- |
|**pluginname** | Der Name des Plugins. Dieser dient zur eindeutigen Identifizierung im System (d.h. es können nicht zwei Plugins gleichen Namens installiert werden). Für die Anzeige innerhalb des Systems kann das Plugin selbst auch noch einen anderen Namen liefern. |
|**pluginclassname** | Der Name der Plugin-Klasse, also der PHP-Klasse, die von einer der Basisklassen der Pluginschnittstelle abgeleitet wurde. Im Manifest dürfen mehrere solcher Plugin-Klassen angegeben werden, wobei die "Hauptklasse" als erste aufgeführt werden muß. Dies dient dazu, in einem Plugin-Paket mehrere Plugin-Einstiegspunkte zu definieren, z.B. könnte das Plugin einen Einstiegspunkt über die Startseite und auch über die Veranstaltungen definieren. |
|**origin** | Der Ursprung des Plugins, üblicherweise der Name des Programmierers oder der Institution oder Gruppe, zu der dieser gehört. |
|**version** | Die Version des Plugins. Die Version sollte so gewählt werden, daß ein Vergleich mit der PHP-Funktion `version_compare()` sinnvoll möglich ist. |
|**description (optional)** | Eine Kurzbeschreibung des Plugins. Diese wird im System den Nutzern angezeigt, wenn das Plugin zur Aktivierung angeboten wird. |
|**homepage (optional)** | Eine URL zur Homepage des Plugins, die weitere Informationen über dieses Plugin enthält. Das kann z.B. die entsprechende Seite über das Plugin im offiziellen Plugin-Repository von Stud.IP sein. |
|**dbscheme (optional)** | Verweis auf eine Datei mit einem SQL-Skript, die sich innerhalb des Plugin-Pakets befindet. Dieses wird bei der Installation des Plugins ausgeführt (nicht aber bei Updates). Hier können Tabellen und Tabelleninhalte angelegt werden, die das Plugin benötigt. Der Dateiname ist dabei relativ anzugeben. |
|**uninstalldbscheme (optional)** | Verweis auf eine Datei mit einem SQL-Skript, die sich innerhalb des Plugin-Pakets befindet. Dieses wird unmittelbar vor dem Entfernen des Plugins ausgeführt. Hier können Tabellen und Tabelleninhalte wieder gelöscht werden, die das Plugin angelegt hat. Der Dateiname ist dabei relativ anzugeben. |
|**updateURL (optional)** | Verweis auf eine URL mit Update-Informationen zu diesem Plugin. Wenn dieser Eintrag vorhanden ist, kann das Plugin über die Update-Funktion der Plugin-Verwaltung aktualisiert werden. Fehlt der Eintrag, ist ein automatisches Update nur dann möglich, wenn die zentrale Meta-Datei des Plugin-Repositories Update-Informationen zu diesem Plugin enthält. Wenn der Eintrag `updateURL` gesetzt ist, muß er auf eine XML-Datei von der folgenden Struktur verweisen: |

```xml
<?xml version="1.0" encoding="UTF-8"?>
<plugins>
  <plugin name="Demo">
    <release
      version="1.2"
      url="http://plugins.studip.de/uploads/Plugins/demo-1.2.zip"
      studipMinVersion="1.6.0"
      studipMaxVersion="1.8.5" />
    <release
      version="2.0"
      url="http://plugins.studip.de/uploads/Plugins/demo-2.0.zip"
      studipMinVersion="1.8.0" />
  </plugin>
</plugins>
```

-Eine solche Datei mit Meta-Daten kann durchaus auch Informationen über viele verschiedene Plugins (mehrere `plugin`-Elemente) und verschiedene Versionen eines Plugins (d.h. mehrere `release`-Elemente) enthalten.

| Wert | Beschreibung |
| ---- | ---- |
|**studipMinVersion (optional)** | Angabe der minimalen Stud.IP-Version, mit der dieses Plugin kompatibel ist. Versucht man, es in einer älteren Version zu installieren, erhält man eine entsprechende Fehlermeldung und die Installation schlägt fehlt. |
|**studipMaxVersion (optional)** | Angabe der maximalen Stud.IP-Version, mit der dieses Plugin noch lauffähig ist. Versucht man, es in einer neueren Version zu installieren, erhält man eine entsprechende Fehlermeldung und die Installation schlägt fehlt. |


Mit der Neugestaltung der "+"-Seite in der Version 3.2 sind neue optionale Informationen für das Plugin-Manifest, welche der Visualisierung auf der "+"-Seite dienen, hinzu gekommen.

| Wert | Beschreibung |
| ---- | ---- |
|**category (optional)** | Die Kategorie, unter der das Plugin auf der "+"-Seite angezeigt werden soll. Die vorhandenen Kategorien sind: |

* *Lehrorganisation*
* *Kommunikation und Zusammenarbeit*
* *Aufgaben*
* *Sonstiges*

Wenn keine Kategorie angegeben ist, wird das Plugin automatisch unter Sonstiges gelistet. Das Angeben einer nicht vorhandenen Kategorie fügt automatisch diese Kategorie neu hinzu.

| Wert | Beschreibung |
| ---- | ---- |
|**displayname (optional)** | Der Name mit dem das Plugin auf der "+"-Seite angezeigt werden soll. Wenn kein displayname angegeben ist wird an entsprechender Stelle der Pluginname verwendet. |
|**complexity (optional)** | Komplexitätsgrad des Plugins, unterteilt in 1 für Standard, 2 für Erweitert oder 3 für Intensiv. |
|**icon (optional)** | Pfad zum Icon des Plugins zur Darstellung auf der "+"-Seite. Dieser Pfad ist relativ zum Wurzelverzeichnis des Plugins anzugeben. |
|**descriptionshort (optional)** | Eine kurze Beschreibung des Plugins, welche auch im zugeklappten Zustand der "+"-Seite zu sehen ist. |
|**descriptionlong (optional)** | Eine ausführliche Beschreibung des Plugins, welche im aufgeklappten Zustand der "+"-Seite zu sehen ist. |
|**screenshot (optional)** | Pfad zu einem Screenshot des Plugins, welcher zur Darstellung auf der "+"-Seite verwendet wird. Im Manifest dürfen mehrere solcher Screenshots angegeben werden, wobei der Screenshot, welcher groß dargestellt wird, als erste aufgeführt werden muss. Dies dient dazu, auf der "+"-Seite mehrere Screenshots darzustellen. Die Reihenfolge der screenshot Einträge im Manifest ist auch die Reihenfolge der Darstellung. Als Beschreibungstext des Bildes wird der Dateiname ohne die Dateiendung angezeigt. Dieser Pfad ist relativ zum Wurzelverzeichnis des Plugins anzugeben. |
|**keywords (optional)** | Stichworte die das Plugin kurz und knapp beschreiben. Diese Stichwörter werden auf der "+"-Seite als Liste angezeigt. Mehrere Einträge sind durch ein Semikolon zu trennen. |
|**helplink (optional)** | Verweis auf eine URL mit Hilfe-Informationen zum Plugin. Bei Angabe der URL wird auf der "+"-Seite ein entsprechender Verweis angezeigt. |

Ein Beispiel für ein vollständiges Manifest:

```ini
pluginname=Demo
pluginclassname=DemoPlugin
origin=virtUOS
version=1.2
dbscheme=sql/install.sql
uninstalldbscheme=sql/uninstall.sql
updateURL=http://plugins.studip.de/svn/plugins/plugins.xml
studipMinVersion=1.6.0
studipMaxVersion=1.8.5

category=Sonstiges
displayname=Demo
complexity=1
icon=images/icons/demoicon.jpg
descriptionshort=Demonstration eines Plugins
descriptionlong=Dieser Text kann ruhig etwas länger sein
screenshot=images/screenshots/demo.jpg
screenshot=images/screenshots/noch_eine_demo.png
keywords=demo;manifest;plugin
helplink=http://hilfe.studip.de/develop/Entwickler/PluginSchnittstelle
```

#### SQL-Skript zur Erzeugung des Datenbankschemas

Innerhalb eines Plugin-Pakets kann sich ein SQL-Skript befinden, welches mit Semikolon abgeschlossene SQL-Befehle enthält. Dieses SQL-Skript dient dem initialen Anlegen einer oder mehrerer Datenbank-Tabellen für das Plugin. Es wird während der Installation des Plugins von Stud.IP ausgeführt.

#### SQL-Skript zum Löschen des Datenbankschemas

Analog zu den Regeln für ein SQL-Skript zur Erzeugung des Datenschemas für das Plugin läßt sich auch ein Skript definieren, welches unmittelbar vor dem Entfernen des Plugins aus Stud.IP ausgeführt wird.

### Plugin-Klasse

Jedes Plugin muß mindestens eine Klasse enthalten, die die für die Einbettung in die Stud.IP-Umgebung erforderlichen Funktionen des Plugins implementiert. Natürlich können daneben beliebig viele weitere Klassen im Plugin-Paket enthalten sein.

Die Plugin-Klasse muß den im Manifest unter **pluginclassname** angegebenen Namen haben und von der Klasse `StudIPPlugin` abgeleitet sein. Außerdem sollte die Klasse mindestens ein Interface implementieren, um sich an bestimmten Stellen in ein bestehendes Stud.IP-System einklinken zu können.

#### Standard-Methoden eines Plugins

Durch das Ableiten von der Klasse `StudIPPlugin` besitzt jedes Plugin automatisch eine Reihe von Methoden:

| Wert | Beschreibung |
| ---- | ---- |
|**getPluginId()** | Liefert die ID des Plugins. Die ID wird intern zur Verwaltung des Plugins verwendet. |
|**getPluginName()** | Liefert den im Manifest definierten Namen des Plugins. |
|**getPluginPath()** | Liefert einen Dateisystempfad zum Verzeichnis des Plugins. Dies kann z.B. verwendet werden, um Ausgabe-Templates zu laden. |
|**getPluginURL()** | Liefert eine (absolute) URL zum Installationsort des Plugins. Wenn man im Plugin auf Style-Sheets oder Bilder verweisen möchte, sollte man diese URL verwenden. |
|**isActivated($context = NULL)** | Prüft, ob das Plugin im angegebenen Kontext (z.B. der aktuellen Veranstaltung) aktiviert ist oder nicht. Falls kein Kontext übergeben wird, ist die aktuell gewählte Veranstaltung gemeint. |
|**isActivatableForContext(Range $context)** | Über diese Methode kann das Plugin selbst entscheiden, ob es in dem übergebenen Kontext aktiviert werden kann, sprich auf der "Mehr"-Seite auftaucht. |
|**deactivationWarning($context)** | Liefert einen Warntext, der ausgegeben wird, bevor das Plugin im angegebenen Kontext deaktiviert wird. Hier kann man z.B. auf eventuellen Datenverlust hinweisen. Die Implementierung der Basisklasse muß dafür überschrieben werden. |
|**perform($unconsumed_path)** | Zeigt eine Seite des Plugins an. TODO: Das muß noch genauer beschrieben werden. |

#### Plugin-Interfaces

Um an bestimmten Stellen in Stud.IP aktiv werden zu können, muss ein Plugin noch eines oder mehrere der Plugin-Interfaces implementieren. In der Version 1.11 stehen dafür die folgenden Schnittstellen bereit:

##### HomepagePlugin: Homepage eines Nutzers

Homepage-Plugins werden nur im Homepage-Kontext geladen. Sie können auf der Homepage eigene Navigationspunkte einblenden und einen Informationsblock auf der Übersichtsseite der Homepage anzeigen.

Dieses Interface enthält die folgende Methode:

| Wert | Beschreibung |
| ---- | ---- |
|**getHomepageTemplate($user_id)** | Liefert ein Template, das auf der Übersichtsseite des Benutzers angezeigt wird. Wenn das Plugin dort nicht angezeigt werden soll, sollte die Methode `NULL` liefern. Zur Konfiguration des Anzeigebereichs kann das Plugin im Template neben den eigenen Platzhaltern noch einige spezielle Werte setzen (Voreinstellungen in eckigen Klammern): |

| Wert | Beschreibung |
| ---- | ---- |
| *title* | Anzeigetitel [Name des Plugins] |
| *icon_url* | Plugin-Icon [kein Icon] |
| *admin_url* | Administrations-Link [kein Link] |
| *admin_title* | Beschriftung für den Administrations-Link [Administration] |

##### PortalPlugin: Startseite (Portalseite)

Portal-Plugins werden auf der Startseite geladen, auch wenn der Benutzer (noch) nicht angemeldet ist. Sie können eigene Navigationspunkte auf der Login- und Startseite einblenden und einen Informationsblock auf der Startseite anzeigen.

Dieses Interface enthält die folgende Methode:

| Wert | Beschreibung |
| ---- | ---- |
|**getPortalTemplate()** | Liefert ein Template, das auf der Startseite des Systems angezeigt wird. Wenn das Plugin dort nicht angezeigt werden soll, sollte die Methode `NULL` liefern. Zur Konfiguration des Anzeigebereichs kann das Plugin im Template neben den eigenen Platzhaltern noch einige spezielle Werte setzen (Voreinstellungen in eckigen Klammern): |

| Wert | Beschreibung |
| ---- | ---- |
| *title* | Anzeigetitel [Name des Plugins] |
| *icon_url* | Plugin-Icon [kein Icon] |
| *admin_url* | Administrations-Link [kein Link] |
| *admin_title* | Beschriftung für den Administrations-Link [Administration] |

##### StandardPlugin: Veranstaltungen und Einrichtungen

Standard-Plugins werden nur im Veranstaltungs- und Einrichtungs-Kontext geladen (allerdings zur Zeit nicht im Admin-Bereich). Sie können in der Veranstaltung bzw. Einrichtung eigene Navigationspunkte einblenden und ein Icon mit einem Link zum Plugin auf der Seite "Meine Veranstaltungen" anzeigen.

Dieses Interface enthält die folgenden Methoden:

| Wert | Beschreibung |
| ---- | ---- |
|**getIconNavigation($course_id, $last_visit)** | Liefert ein Navigationsobjekt für das Icon des Plugins auf der Seite "Meine Veranstaltungen". Wenn das Plugin dort nicht angezeigt werden soll, sollte die Methode `NULL` liefern. *$last_visit* ist der Zeitpunkt des letzten Besuchs des Nutzers in der Veranstaltung (bzw. des Plugins). Wenn es seit diesem Zeitpunkt neue oder geänderte Inhalte gibt, sollte dies über ein spezielles Icon dem Nutzer kenntlich gemacht werden. |
|**getInfoTemplate($course_id)** | Liefert ein Template, das auf der Kurzinfoseite der Veranstaltung bzw. Einrichtung angezeigt wird. Wenn das Plugin dort nicht angezeigt werden soll, sollte die Methode `NULL` liefern. Zur Konfiguration des Anzeigebereichs kann das Plugin im Template neben den eigenen Platzhaltern noch einige spezielle Werte setzen (Voreinstellungen in eckigen Klammern): |

| Wert | Beschreibung |
| ---- | ---- |
| *title* | Anzeigetitel [Name des Plugins] |
| *icon_url* | Plugin-Icon [kein Icon]|
| *admin_url* | Administrations-Link [kein Link]|
| *admin_title* | Beschriftung für den Administrations-Link [Administration]|


##### StudienmodulManagementPlugin: Studienmodulsuche

Das StudienmodulManagementPlugin wird in der Anzeige von Studienbereichen verwendet, um weitere modulespezifische Informationen anzeigen zu können.

Dieses Interface enthält die folgenden Methoden:

| Wert | Beschreibung |
| ---- | ---- |
|**getModuleTitle($module_id, $semester_id = null)** | Gibt die Bezeichnung für ein Modul zurück. |
|**getModuleDescription($module_id, $semester_id = null)** | Gibt die Kurzbeschreibung für ein Modul zurück. |
|**getModuleInfoNavigation($module_id, $semester_id = null)** | Gibt ein Objekt vom Typ Navigation zurück, das Titel, Link und Icon für ein Modul enthalten kann, z.B. zur Darstellung eines Info-Icons. |

##### SystemPlugin: systemweite Erweiterungen

System-Plugins werden auf jeder Seite in Stud.IP geladen (mit Ausnahme von Seiten, die eine komplett andere Darstellung verwenden wie z.B. Druckansichten). Sie können überall im System eigene Navigationspunkte einblenden.

Dieses Interface enthält keine Methoden.

##### WebServicePlugin: Web-Services aus Plugins

Plugins haben die Möglichkeit, die SOAP/XMLRPC-Webservices, die Stud.IP zur Verfügung stellen kann, um eigene Services zu erweitern. Dazu muss ein Plugin lediglich das WebServicePlugin-Interface implementieren:

| Wert | Beschreibung |
| ---- | ---- |
|**getWebServices()** | Sollte Plugin-Services laden und liefert dann eine Liste von Service-Klassennamen zurück, die die systemeigenen ergänzen. |

Beispiel: 

```php
[...]
function getWebServices()
{
  require 'MyService1.php';
  require 'MyService2.php';
  return array('MyService1', 'MyService2');
}
[...]
```

### Plugin-Aktionen

Über die oben beschriebenen typspezifischen Fähigkeiten hinaus, d.h. insbesondere der Einbettung in vorhandene Seiten in Stud.IP, hat jedes Plugin die Möglichkeit, komplett eigene Seiten - inklusive einer eigenen Navigation - anzubieten. Dazu gibt es in der Plugin-Schnittstelle einen Mechanismus, der vom Nutzer aufgerufene URLs in Methodenaufrufe im Plugin übersetzt, in dieser Beschreibung als "Plugin-Aktionen" bezeichnet. Eine solche Plugin-Aktion ist eine normale (öffentliche) Methode in der Plugin-Klasse, deren Name auf "`_action`" endet:

```php
class TestPlugin extends StudipPlugin implements SystemPlugin
{
    [...]
    public function delete_action($id)
    {
        [...]
    }
}
```

Eine Aktion kann Funktionsparameter haben (hier im Beispiel: `$id`), aber auch Request-Parameter verarbeiten (z.B. beim Absenden eines Formulars).

#### Navigation im Plugin

Das Erstellen von Navigationspunkten für Plugins ist [an anderer Stelle](Navigation) beschrieben und funktioniert genauso wie im Stud.IP-Kernsystem. Die zugehörigen URLs führen in der Regel zu bestimmten Aktionen im Plugin, deren Erstellung im folgenden Abschnitt beschrieben ist.

#### Erstellen von URLs zu Plugin-Aktionen

Damit der Nutzer eine bestimmte Aktion aufrufen kann, muß natürlich das Plugin auch die zugehörige URL kennen. Um URLs zu diesen Plugin-Aktionen erstellen zu können, gibt es zwei Hilfsfunktionen in der Klasse `PluginEngine`:

| Funktion | Beschreibung |
| ---- | ---- |
|**getLink($plugin_action, $params = array())** | Liefert die URL zu einer Aktion in einem Plugin. Die Aktion wird dabei durch den Klassennamen des Plugins, den Namen der Aktion sowie weitere Funktionsparameter spezifiziert, jeweils getrennt durch einen Schrägstrich ("`/`"). Der Name der Aktion darf fehlen, dann wird die Standardaktion mit dem Namen "`show`" in der angegebenen Plugin-Klasse verwendet. Als zweites Argument kann optional noch ein Array mit Request-Parametern (d.h. GET-Parametern) angegeben werden.|

```php
<a href="<?= PluginEngine::getLink("testplugin/delete/$id") ?>"> Eintrag löschen </a>
```

Das Resultat dieser Funktion ist eine *entity-kodierte URL*, d.h. es kann direkt in Attribute im HTML eingesetzt werden (*action* einer FORM, *href* eines A-Elements). Braucht man die unkodierte URL, sollte `getURL()` verwendet werden.

| Funktion | Beschreibung |
| ---- | ---- |
|**getURL($plugin_action, $params = array())** | Diese Funktion arbeitet genau wie `getLink()`, liefert aber keinen entity-kodierten Wert zurück, sondern die unkodierte URL. Diese kann dann z.B. für Aufrufe über JavaScript oder Redirects verwendet werden. Beispiel:[<<](<<) |

```php
header('Location: ' . $PluginEngine::getURL('testplugin/show'));
```


### Interaktion mit anderen Plugins

Gelegentlich ist es auch wünschenswert, mit anderen Plugins interagieren zu können. Dazu bietet die Klasse `PluginEngine` ebenfalls eine Reihe von Hilfsfunktionen:

| Funktion | Beschreibung |
| ---- | ---- |
|**getPlugin($class)** | Liefert das Plugin mit dem angegebenen Klassennamen. Falls ein solches Plugin nicht installiert ist oder der Nutzer nicht die notwendigen Rechte besitzt, bekommt man statt der Plugin-Instanz nur einen `NULL`-Wert. |
|**getPlugins($type, $context = NULL)** | Liefert alle Plugins des angegebenen Typs (Name eines Plugin-Interface) als Array. Auch hier werden natürlich nur solche Plugins gefunden, die der aktuelle Nutzer sehen darf. |
|**sendMessage($type, $method, ...)** | Ruft die angegebene Methode bei allen Plugins eines bestimmten Typs (Name eines Plugin-Interface) auf. Auf den Namen folgende Argumente werden als Methodenparameter an jedes Plugin weitergereicht. Als Resultat bekommt man ein Array mit den Resultaten der einzelnen Methodenaufrufe. |
|**sendMessageWithContext($type, $context, $method, ...)** | Ruft die angegebene Methode bei allen Plugins eines bestimmten Typs auf, die in einem bestimmten Kontext (z.B. die ID einer Veranstaltung oder Einrichtung) aktiviert sind. Auf den Namen folgende Argumente werden als Methodenparameter an jedes Plugin weitergereicht. Als Resultat bekommt man ein Array mit den Resultaten der einzelnen Methodenaufrufe. |

### CSS und Javascript  in Plugins

Die Basisklasse `StudIPPlugin` stellt die Methode `addStylesheet()` und ab Version 4.4 analog die Methode `addScript()`  bereit. Diesen Methoden kann ein zum Pluginpfad relativer Dateiname angegeben werden, um CSS/LESS-Stylesheets  und Javascript-Dateien einzubinden. CSS und Javascript-Dateien werden ohne weitere Behandlung ausgegeben, während LESS-Dateien kompiliert werden. Ist das System im Entwicklungsmodus, so wird das LESS bei jeder Änderung der Datei neu kompiliert. Im Produktivmodus wird das LESS nur bei jeder Änderung der Pluginversion im Manifest neu kompiliert.

Ab Version 4.4 bietet die Basisklasse auch die Methoden `addStylesheets()` und `addScripts()` an, um mehrere Dateien auf einmal einzubinden. Im Entwicklungsmodus werden diese so eingebunden als würden die Methoden für die einzelnen Methoden aufgerufen. Im Produktivmodus hingegen werden die eingebundenen Dateien aneinander gehängt und als eine einzige Datei ausgegeben.

Alle hier erwähnten Methoden akzeptieren den Parameter `$link_attr` über welchen Attribute an das erzeugte HTML-Element gehängt werden können. Bei LESS-Stylesheets gibt es zusätzlich den Parameter `$variables`, über welchen Variablen reingereicht werden können, die dann im LESS zur Verfügung stehen.

### Weitere Pluginmethoden

| Funktion | Beschreibung |
| ---- | ---- |
|**onEnable($plugin_id)** | Beim Aktivieren eines Plugins wird diese Methode der Klasse aufgerufen, damit das Plugin entsprechend reagieren oder Abhängigkeiten überprüfen kann. Für den Fall, dass das Plugin das Aktivieren verhindern möchte (bspw. weil benötigte Konfigurationen noch nicht vorgenommen worden), muß die Methode `onEnable` den Wert `false` zurückliefern. |
|**onDisable($plugin_id)** | Beim Deaktivieren eines Plugins wird diese Methode der Klasse aufgerufen, damit das Plugin entsprechend reagieren oder Abhängigkeiten überprüfen kann. Für den Fall, dass das Plugin das Deaktivieren verhindern möchte, muß die Methode `onDisable` den Wert `false` zurückliefern. |

### Temporäres Deaktivieren aller Plugins

Über den URL-Parameter `disable_plugins=1` können Root-Administratoren alle Plugins temporär (d.h. für die laufende Session) deaktivieren, falls es zu Problemen nach dem Update eines Plugins kommen sollte und das System mit aktivierten Plugins nicht mehr nutzbar ist. Der Parameter kann auch vor dem Login gesetzt werden (falls schon die Login-Seite nicht mehr aufrufbar ist) und gilt dann für die daran anschließende Session bis zum Logout. Über `disable_plugins=0` kann es auch ohne Logout wieder zurückgesetzt werden.


## Datenschutz in Plugins

Damit Stud.IP auf Nutzeranfrage hin die im System gespeicherten nutzerbezogenen Daten aus Plugins mit ausliefern kann, ist es notwendig dass das Plugin das Interface `PrivacyPlugin` implementiert und die Funktion `exportUserData(StoredUserData $storage)` besitzt. Diese Funktion erhält eine Instanz der `StoredUserData` Klasse und kann darin die gespeicherten personenbezogenen Daten (sowohl tabellarische Daten als auch Dateien) ablegen.

Beim Löschen von Personen wird das Event `UserDidDelete` gesendet, woraufhin ein Plugin auch seine nutzerbezogenen Daten zu dieser Person aus dem System löschen sollte.
Werden einzelne Teile von Personendaten gelöscht, zum Beispiel zum Anonymisieren einer Person, wird das Event `UserDataDidRemove` gesendet. Dieses Event liefert als weiteren Parameter noch den Typ der gelöschten Personendaten. Die verfügbaren Typen können dem Beispiel unten entnommen werden. Welche dieser Typen für das Plugin relevant sind hängt von den durch das Plugin gespeicherten Daten ab.

Ein Plugin könnte folgendermaßen aussehen:
```php
class MyPlugin extends StudIPPlugin implements StandardPlugin, PrivacyPlugin
{

    public function __construct()
    {
        parent::__construct();
        NotificationCenter::addObserver($this, 'deleteUser', 'UserDidDelete');
        NotificationCenter::addObserver($this, 'removeData', 'UserDataDidRemove');
    }
    ...

    /**
     * Export available data of a given user into a storage object
     * (an instance of the StoredUserData class) for that user.
     *
     * @param StoredUserData $store object to store data into
     */
    public function exportUserData(StoredUserData $storage)
    {
        $db = DBManager::get();

        $table_data = $db->fetchAll('SELECT * FROM my_table WHERE user_id = ?', [$storage->user_id]);
        $storage->addTabularData('Anzeigetitel', 'my_table', $table_data);

        $file_data = $db->fetchAll('SELECT * FROM my_files WHERE user_id = ?', [$storage->user_id]);
        foreach ($file_data as $file) {
            $storage->addFileAtPath($file['name'], $file['path']);
        }
    }

    /**
    * delete given user from plugin
    *
    * @param String $event name of the notification event
    * @param User $user
    */
    public function deleteUser($event, $user)
    {
        ...
        PageLayout::postInfo('Nutzer X aus MyPlugin gelöscht.');
    }

    /**
    * delete data of given user from plugin
    *
    * @param String $event name of the notification event
    * @param String $user_id
    * @param String $type of data that should be removed
    */
    public function removeData($event, $user_id, $type)
    {
        switch ($type) {
            case 'course_documents':
            case 'personal_documents':
                ...
                PageLayout::postInfo('Dokumente von Nutzer X aus MyPlugin gelöscht');
                break;
            case 'course_contents':
            case 'personal_contents':
                ...
                PageLayout::postInfo('Inhalte von Nutzer X aus MyPlugin gelöscht');
                break;
            case 'names':
                ...
                PageLayout::postInfo('Namen von Nutzer X aus MyPlugin gelöscht');
                break;
            case 'memberships':
                ...
                PageLayout::postInfo('Veranstaltungszuordnungen von Nutzer X aus MyPlugin gelöscht');
                break;
        }
    }
}
```

## Pluginmigration von Stud.IP v4.6 auf v5.0

### Breaking Changes

#### Geänderte API des JSUpdater

Mit dem STUDIP.JSUpdater können Plugins teilhaben am regelmäßigen Pollen des Servers, wie das auch schon für Blubber oder die PersonalNotifications verwendet wird.

Um im Plugin am JSUpdater teilzunehmen, gab es bisher zwei Möglichkeiten:

* Aufruf von STUDIP.JSUpdater.register
* Automatische Verwendung durch Implementation einer JS-Funktion namens "periodicalPushData"

Die zweite Möglichkeit wurde entfernt und muss durch eine explizite Registrierung ersetzt werden. Alles weitere unter [Entwickler/UpdateInformation](UpdateInformation)


# Internationalisierung von Plugins
Die PluginEngine unterstützt eine Internationalisierung des Plugins auf Basis von gettext. Um eine Übersetzung des Plugins durchzuführen sind die üblichen Schritte nötig:
```shell
xgettext -n PLUGINPAKET/*.php
```
Übersetzen der so erzeugten messages.po

```shell
msgfmt messages.po
```
Dann die so erzeugte messages.mo umbenennen in gtdomain_PLUGINCLASSNAME.mo

* Die .mo-Datei in die folgende Verzeichnisstruktur legen:
  * PLUGINPAKET/locale/SPRACHKÜRZEL/LC_MESSAGES/
* evtl. den Server neu starten, um Änderungen angezeigt zu bekommen
