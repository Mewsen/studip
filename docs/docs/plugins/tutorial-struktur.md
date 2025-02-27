Dieses Tutorial dient zum ersten Kontakt mit Stud.IP Plugins.
Es wird beispielhaft ein Stud.IP Plugin erstellt,
um aufzuzeigen, welche Komponenten für ein Stud.IP Plugin relevant sind.
Dabei werden die einzelnen Komponenten nicht ausführlich noch einmal erklärt,
stattdessen werden auf ausführliche Erklärung im Wiki verwiesen, die sich vorher angeschaut werden sollten.
Dieses Tutorial stellt somit eine Art Leitfaden dar, der verfolgt werden kann,
um die verschiedenen Stud.IP Komponenten kennenzulernen und in einen sinnvollen Zusammenhang zu bringen.

Diese Seite stellt dabei den ersten Teil des Tutorials dar,
indem erst einmal die Grundstruktur eines Plugins erklärt und erstellt wird.
Dieser Teil ist somit größtenteils unabhängig von der eigentlichen Funktionalität des Plugins
und kümmert sich nur darum eine Basis zu Erstellen,
mit der angenehm Plugins erstellt und weiterentwickelt werden.
Ein wenig Kontext für die Funktionalitäten des Plugins ist aber notwendig und fürs Verständnis auch sinnvoll.

Als Ziel soll das Plugin beliebigen Stud.IP Nutzern das Erstellen von "Texten" ermöglichen.
"Texte" bestehen dabei erst einmal nur aus einem Titel, einem Inhalt und besitzen einen Typ.
Außerdem sollen die Nutzer eine Übersicht über alle erstellten Texte einsehen können.

Am Ende dieser Seite ist eine ZIP-Datei mit dem bisherigen Code und zusätzlicher phpDoc angehangen,
damit der eigene Fortschritt überprüft werden kann.
Es sollte jedoch versucht werden, dass PluginTutorial eigenständig nachzuverfolgen.

Um dieses Tutorial vernünftig mitverfolgen zu können,
wird eine laufende Stud.IP Testumgebung (mind. Stud.IP 4.6) inklusive Webserver, php und Datenbanksystem benötigt.


## Studip-Coding Style
Um das Arbeiten mit mehreren Entwicklern zu erleichtern,
sollte ein einheitlicher Coding Style angestrebt werden.
Der Coding Style für Stud.IP ist [hier](CodingStyle) zu finden.  
Als zweite untergestellte Quelle kann [dieser](https://www.php-fig.org/psr/psr-12/) Artikel herangezogen werden.

## Grundgerüst
Damit ein Verzeichnis von Stud.IP als ein Plugin erkannt wird,
sind in der Wurzel des Verzeichnisses zwei Dateien notwendig.  
Eine `plugin.manifest` Datei, welches Meta-Daten über das Plugin enthält
wie z.B. den Pluginnamen oder die aktuelle Versionsnummer des Plugin, und eine Plugin-Klasse,
welche als initiale Instanz von Stud.IP aufgerufen wird.

### Plugin manifest
Welche Meta-Daten im `plugin.manifest` hinterlegt werden können,
ist in [Plugin-Manifest](PluginSchnittstelle#plugin-manifest) ersichtlich.  
Für unser Beispiel-Plugin würde das `plugin.manifest` folgendermaßen aussehen:
```
pluginname=TextPlugin
pluginclassname=TextPlugin
origin=UOL
version=1.0
studipMinVersion=4.6
```
### Haupt-Plugin-Klasse
Diese Datei beinhaltet die Plugin-Klasse. Sie muss den Klassennamen tragen,
der in `plugin.manifest` mit `pluginclassname` festgelegt wurde.
Der Dateiname muss identisch mit dem Klassennamen sein.
Im Beispiel muss also der Dateiname `TextPlugin.class.php` lauten und darin eine Klasse `TextPlugin` enthalten sein.  
Die Plugin-Klasse erbt standardmäßig von der `StudipPlugin`-Klasse und implementiert ein oder mehrere Plugin-Interfaces.  
Die verschiedenen Typen von Plugins sind in [Plugin-Interfaces](PluginSchnittstelle#plugin-interfaces) erläutert.  
Da das TextPlugin systemweit erreichbar sein soll, implementiert es die `SystemPlugin` Schnittstelle.  
Die Plugin-Klasse sieht dann folgendermaßen aus:
```php
<?php

class TextPlugin extends StudIPPlugin implements SystemPlugin
{

}
```

### Installation
Da das Plugin nun so weit ist, dass es von Stud.IP als Plugin erkannt wird, kann es nun installiert werden.
Dafür gibt es im Wesentlichen zwei Möglichkeiten: Man kann das Plugin zu einer `.zip`-Datei komprimieren
und direkt in Stud.IP installieren oder falls es ein Repository für das Plugin gibt,
kann das Plugin auch zuerst geklont werden und dann in Stud.IP eingebunden werden.

Installierte Plugins sind unabhängig davon, wie sie installiert wurden,
im Studip-Verzeichnis unter `public/plugin_packages/<origin>` zu finden,
wobei `<origin>` jenes origin ist, welches im jeweiligen `plugin.manifest` angegeben ist.

#### Plugin als ZIP-Datei installieren
Um das Plugin als `.zip`-Datei direkt zu installieren,
muss als aktiver `root`-User nach `Admin` => `System` => `Plugins` navigiert werden.
Links in der Sidebar kann dann die `.zip`-Datei ausgewählt oder per Drag and Drop installiert werden. 

Wenn das Plugin installiert ist, muss es schließlich noch aktiviert werden.
Dafür muss lediglich in der gleichen Ansicht das Plugin mit der "Aktiv" checkbox aktiviert
und die Änderung ganz unten auf der Seite gespeichert werden.


#### Plugin aus einem Repo installieren
Falls für das Plugin ein Repository existiert,
kann das Repository auch geklont werden und das Plugin dann installiert werden.
Mit dem Repository kann dann ganz normal gearbeitet werden.

Dazu muss das Repository des Plugins in das entsprechende Verzeichnis geklont werden.
Im Falle des TextPlugins muss das Repo also in `public/plugin_packages/UOL` geklont werden,
da der `origin` im `plugin.manifest` als `UOL` festgelegt ist.
Dabei sollte außerdem darauf geachtet werden, dass der Repository-Pfadname, der gleiche ist,
wie der festgelegte `pluginname`.  
Insgesamt würde das Stud.IP also dann folgendermaßen aussehen:
```ini
<studip-verzeichnis>
  public\
    plugin_packages\
      core\
      UOL\
        TextPlugin\
          .git
          plugin.manifest
          TextPlugin.class.php
```
Als `root`-User muss dann auch hier nach `Admin` => `System` => `Plugins` navigiert werden
und links in der Sidebar unter "Ansichten" die Ansicht "Vorhandene Plugins registrieren" gewählt werden.
Das TextPlugin sollte nun hier als Installationsmöglichkeit gelistet sein und installiert werden.

Wie beim Installieren des Plugins als ZIP-Datei
muss das Plugin nach dem Installieren noch unter `Admin` => `System` => `Plugins`aktiviert werden.

#### Weitere Arbeit mit dem Plugin
Da das Plugin nun installiert ist,
können alle folgenden Änderungen direkt im Verzeichnis des Plugins erfolgen
und werden von Stud.IP automatisch erkannt.  
Das Plugin muss und sollte somit nicht bei jeder Änderung neu installiert werden.

## Inhalt des Plugins
Da nun das Plugin installiert und aktiviert ist,
kann sich um die eigentliche Funktionalität des Plugins gekümmert werden.  
Wir möchten eine Übersichtsseite zum Anzeigen aller Texte als initiale Anlaufstelle des Plugins erstellen.

### Navigation
Dazu muss nun erstmal eine Navigation erstellt werden, um auf diese Seite navigieren zu können.
Wie die Navigation in Stud.IP funktioniert ist in [Navigation](Navigation) erläutert.
Da die Navigation zur Übersichtsseite immer erstellt werden soll,
wird die Navigation in der `__construct`-Methode des TextPlugins erstellt.

```php
    public function __construct()
    {
        parent::__construct();

        $root_nav = new Navigation('Texte', PluginEngine::getURL($this, [], 'overview'));
        $root_nav->setImage(Icon::create('file-text', Icon::ROLE_NAVIGATION));
        Navigation::addItem('/text_root', $root_nav);

        $navigation = new Navigation('Übersicht', PluginEngine::getURL($this, [], 'overview'));
        $root_nav->addSubNavigation('text_overview', $navigation);
    }
```
Den initialen Reiter des Plugins nennen wir einfach `Texte`
und hängen das Navigationselement an die Wurzel der Navigation an,
damit es im Hauptnavigationsreiter auftaucht.
Alle weiteren Navigationspunkte hängen wir dann an dieses Navigationselement an.
Bisher haben wir nur eine Übersichtsseite geplant,
sodass wir ein weiteres Navigationselement `Übersicht` erstellen,
welches wir an unsere `Texte`-Navigation anhängen.
Der Hauptnavigationspunkt taucht jetzt bereits in Stud.IP auf,
jedoch wird bisher noch auf eine nicht existierende Seite verlinkt.

### Plugin- und Controller-Klassen
Bevor wir die fehlende Seite ergänzen,
nehmen wir unserem zukünftigen Ich ein wenig Arbeit ab.  
Ein Plugin-Verzeichnis kann im Allgemeinen mehrere Plugins beinhalten
und wird oft mehrere Controller-Klassen beinhalten.
Wir werden später noch Code schreiben,
die alle unsere Plugin- und Controller-Klassen benötigen werden
und um redundanten Code zu vermeiden,
erstellen wir jeweils eine Klasse,
von der dann alle unsere Plugin- und Controller-Klassen erben können.

Die beiden Dateien `Plugin.php` und `Controller.php` erstellen wir in einem neuen `classes`-Verzeichnis.

```php
<?php

namespace TextPlugin;

use StudIPPlugin;

class Plugin extends StudIPPlugin
{

}
```
```php
<?php

namespace TextPlugin;

use PluginController;

class Controller extends PluginController
{
    
}
```

Außerdem setzen wir einen `namespace` für beide Klassen,
um sie von anderen gleichnamigen Klassen unterscheiden zu können.
Wir sollten auch daran denken,
dass nun unser `TextPlugin`-Klasse von `\TextPlugin\Plugin` erben sollte,
und nicht mehr von `StudIPPlugin`.


### Autoload
Dateien im Plugin-Verzeichnis werden in der Regel von Stud.IP nicht automatisch geladen.
Für unsere neuen Klassen im `classes`-Verzeichnis müssen wir also Stud.IP explizit sagen,
dass er die Klassen mit unserem Plugin laden soll, damit wir sie auch nutzen können.

Das Laden von anderen Klassen wird in der Regel in eine `bootstrap.inc.php`-Datei ausgelagert,
die dann vom Plugin immer mit `require_once` geladen wird.
Wir erstellen also in der Wurzel des Plugin-Verzeichnisses eine `bootstrap.inc.php`-Datei,
in der wir mit dem `StudipAutoloader` alle Dateien im Verzeichnis `models` laden.  
Als prefix für den autoloader sollte der `namespace` der Klassen angegeben werden.

```php
<?php

StudipAutoloader::addAutoloadPath(__DIR__ . '/classes', 'TextPlugin');
```

Die Datei wird dann in der `TextePlugin.class.php` mit `require_once __DIR__ . '/bootstrap.inc.php';` eingebunden,
vorzugsweise vor der Klassendefinition.

### Trails
Nun sind wir endlich so weit,
dass wir uns um die Übersichtsseite kümmern können.  
[Trails](Trails) ist das Model-View-Controller Framework von Stud.IP und legt unter anderem fest,
welche Seite bei welcher URL aufgerufen wird.

Allgemein beinhaltet eine URL für ein Plugin immer `plugins.php/<pluginname>/<controller-name>/<actions-name>`.
Der `pluginname` ist in der `plugin.manifest` festgelegt.  
Der `controller-name` ist der Dateiname des Controllers.
Wenn die Controller-Datei `overview.php` heißt,
muss die Klasse in der Datei `OverviewController` heißen.  
Der `action-name` ist der name einer "action", also einer Methode innerhalb des Controllers,
die mit `_action` endet.

Die URL `plugins.php/textplugin/overview/index` würde beispielsweise eine Methode `index_action()` im Controller `overview`
im Plugin `TextPlugin` aufrufen.
Die action `index` wird dabei immer aufgerufen,
wenn kein `action-name` in der URL angegeben ist.  
Da wir in unserer Navigation mit `PluginEngine::getURL($this, [], 'overview')` auf einen overview Controller innerhalb unseres Plugins verweisen
und keine action angegeben haben, sollten wir einen Controller in einem neuen Verzeichnis `controllers` namens `overview.php`
erstellen, der die Methode `index_action` beinhaltet.

```php
<?php

class OverviewController extends \TextPlugin\Controller
{
    public function index_action()
    {

    }
}
```

Alle weiteren Angaben in der URL werden jeweils als Parameter in die actions reingegeben.
Wenn im OverviewController also eine action `test_action($param1, $param2)` existiert
und die URL `plugins.php/textplugin/overview/test/hallo/welt` aufgerufen wird,
enthält `$param1` den string `hallo` und `$param2` den string `welt`.  
Da `/` zum Separieren in der URL genutzt wird, sollte daher auch vermieden werden,
strings die `/` enthalten, als Parameter zu übergeben.

Nachdem Trails die jeweilige action-methode aufgerufen hat und sie durchgelaufen ist,
wird eine view gerendert, die sich auch aus der URL ergibt.
Dabei muss es in einem `views` Verzeichnis innerhalb des Plugins ein Verzeichnis existieren,
welches nach dem Controller benannt ist
und innerhalb dieses Verzeichnis eine `.php` Datei die nach der action benannt ist.  
Controller-Klassen im `controllers`-Verzeichnis und view-Dateien im `views`-Verzeichnis
werden von Stud.IP automatisch geladen,
sodass wir sie **nicht** mit dem autoloader laden müssen.

Wenn sowohl der Controller mit der action-methode als auch die passende view erstellt wurde
und die Namenskonvention dabei eingehalten wurde,
sollte Stud.IP nun eine leere Seite auf der TextPlugin Übersichtsseite anzeigen.
Die Dateistruktur für das Plugin sollte bis hierhin wie folgt aussehen:

```ini
TextPlugin\
  classes\
    Controller.php
    Plugin.php
  controllers\
    overview.php
  views\
    overview\
      index.php
  bootstrap.inc.php
  plugin.manifest
  TextPlugin.class.php
```

### Datenbanktabellen erstellen (Migration)
Bevor die Seite mit vernünftigem Inhalt gefüllt werden kann,
müssen noch die entsprechenden Datenbanktabellen erstellt werden.
Dies geschieht in Stud.IP mittels [Migrationen](Migrations).

Für die erste Version des Plugins benötigen wir nur eine Tabelle, um die erstellten Texte zu speichern.
Die Migrationsdatei erstellen wir in einem neuen Verzeichnis `migrations` und nennen sie `01_init_texte.php`.
Die Klasse innerhalb der Datei muss dementsprechend `InitTexte` heißen.

```php
<?php

class InitTexte extends Migration
{

    public function up()
    {
        $db = DBManager::get();

        $query = "CREATE TABLE IF NOT EXISTS tp_texte (
                    text_id     CHAR(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
                    title       TEXT NOT NULL,
                    description TEXT NULL DEFAULT NULL,
                    type        TINYINT(2) NOT NULL DEFAULT 1,
                    mkdate      INT(11) NOT NULL,
                    chdate      INT(11) NOT NULL,
                    author_id   CHAR(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
                    PRIMARY KEY (text_id)
                  )";
        $db->exec($query);
    }

    public function down()
    {
        // drop new tables
        DBManager::get()->exec("DROP TABLE IF EXISTS tp_texte");
    }
}
```

Neue Migrationen werden von Stud.IP zwar auch wie die andere Dateien automatisch erkannt,
jedoch müssen Migrationen explizit durchgeführt bzw. installiert werden.
Migrationen können als root-User unter `Admin` => `System` => `Plugins` in der Spalte `Schema` ausgeführt werden.
In dieser Spalte wird standardmäßig die aktuelle migrations-version angegeben.
In unserem Fall, da wir noch keine migration durchgeführt haben, ist die Version 0.
Falls Stud.IP neue Migrationsdateien im Plugin-Verzeichnis erkennt,
wird in der Spalte ein Icon angezeigt, mit dem alle neue Migrationen ausgeführt werden können.  
Wie auf der [Migrationen](Migrations) Wiki-Seite erläutert,
stellt die Nummer im Namen der Migrationsdatei die Version der Migration dar,
sodass neue Migrationen mit aufsteigenden Nummern zu versehen sind. 


### Model-Klassen
Damit wir einfache SQL-Anfragen nicht selber schreiben müssen und Einträge der Tabelle als php-Objekte nutzen können,
erstellen wir für jede Tabelle eine [SimpleORMap](SimpleORMap)-Klasse in einem neuen Verzeichnis `models`.
Den Model-Klassen geben wir außerdem auch den `namespace` `TextPlugin`.  
Da wir nur eine Tabelle haben, erstellen wir eine Modelklasse `Text.php`:

```php
<?php

namespace TextPlugin;

use SimpleORMap;
use User;

class Text extends SimpleORMap

{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'tp_texte';

        $config['belongs_to']['author'] = [
            'class_name' => User::class,
            'foreign_key' => 'author_id',
            'assoc_foreign_key' => 'user_id'
        ];

        parent::configure($config);
    }
}
```

Damit wir die Modelklassen nutzen können, müssen wir wie bei `classes` dran denken,
sie in der `bootstrap.inc.php` mit `StudipAutoloader::addAutoloadPath(__DIR__ . '/models', 'TextPlugin');` zu laden.  
Das Plugin-Verzeichnis sieht zu diesem Punkt folgendermaßen aus:
```ini
TextPlugin\
  classes\
    Controller.php
    Plugin.php
  controllers\
    overview.php
  migrations\
    01_init_texte.php
  models\
    Text.php
  views\
    overview\
      index.php
  bootstrap.inc.php
  plugin.manifest
  TextPlugin.class.php
```

### javascript- und css-Dateien
Wenn css und/oder javascript Dateien genutzt werden sollen,
sollten diese in einem neuen Verzeichnis `assets` abgelegt werden.
Um die Dateien dann zu laden,
kann entweder in der Plugin-Klasse `$this->addStylesheet('<css-dateipfad>');`
bzw. `$this->addScript(<js-dateipfad>');`
oder in Controller-Klassen `PageLayout::addStylesheet($this->plugin->getPluginURL() . '/<css-dateipfad>');`
bzw. `PageLayout::addScript($this->plugin->getPluginURL() . '/<js-dateipfad>');` aufgerufen werden.


### Lokalisierung
Da Stud.IP nicht nur von deutschsprachigen Nutzern genutzt wird,
sollte das Plugin auch auf andere Sprachen übersetzbar sein.
Wie in [Internationalisierung](Howto/Internationalisierung) erklärt ist,
geschieht dies in Stud.IP mithilfe des `gettext`-Packets.

#### Texte als Übersetzbar kennzeichnen
Da aber eventuell strings ausgegeben werden, für die in Stud.IP noch keine Übersetzung existieren,
muss innerhalb des Plugins eine Übersetzungsdatei angelegt werden,
die genau diese neuen string übersetzt.
Bevor dies jedoch gemacht wird, sollten die entsprechenden strings als übersetzbar gekennzeichnet werden
und damit dies nicht im Nachhinein für alle strings gemacht werden muss,
wird dies eingeführt, bevor die eigentliche Funktionalität des Plugins erstellt wird.

Innerhalb unseres Plugins muss mit `bindtextdomain` festgelegt werden,
wo die Übersetzungsdatei zu finden ist und mit `bind_textdomain_codeset` die Zeichenkodierung festgelegt werden.
Um dies nicht einzeln für alle Plugins innerhalb des Plugin-Verzeichnisses festzulegen,
nutzen wir die vorher erstellten `Plugin`-Klasse, von der das `TextPlugin` erbt.

```php
<?php

namespace TextPlugin;

use StudIPPlugin;

class Plugin extends StudIPPlugin
{
    const GETTEXT_DOMAIN = 'TextePlugin';

    public function __construct()
    {
        parent::__construct();
        bindtextdomain(static::GETTEXT_DOMAIN, $this->getPluginPath() . '/locale');
        bind_textdomain_codeset(static::GETTEXT_DOMAIN, 'UTF-8');
    }
}
```

Damit innerhalb des Plugins nun lediglich `$this->_()` bzw. `$this->_n()` für gettext aufgerufen werden kann
und nicht immer `dgettext()` bzw. `dngettext()`,
sollten noch zwei Methoden in die Plugin-Klasse ergänzt werden:

```php
    public function _($string)
    {
        $result = dgettext(static::GETTEXT_DOMAIN, $string);

        if ($result === $string) {
            $result = _($string);
        }

        return $result;
    }

    public function _n($string0, $string1, $n)
    {
        if (is_array($n)) {
            $n = count($n);
        }

        $result = dngettext(static::GETTEXT_DOMAIN, $string0, $string1, $n);

        if ($result === $string0 || $result === $string1) {
            $result = ngettext($string0, $string1, $n);
        }

        return $result;
    }
```

Zum Erstellen der Navigation in `TextPlugin.class.php` hatten wir bereits zwei Texte erstellt ("Texte" und "Übersicht").
Diese können wir nun mit dem Aufruf von `$this->_()` übersetzbar machen, sodass das TextPlugin folgendermaßen aussieht:

```php
<?php

require_once __DIR__ . '/bootstrap.inc.php';

class TextPlugin extends \TextPlugin\Plugin implements SystemPlugin
{
    public function __construct()
    {
        parent::__construct();

        $root_nav = new Navigation($this->_('Texte'), PluginEngine::getURL($this, [], 'overview'));
        $root_nav->setImage(Icon::create('file-text', Icon::ROLE_NAVIGATION));
        Navigation::addItem('/text_root', $root_nav);

        $navigation = new Navigation($this->_('Übersicht'), PluginEngine::getURL($this, [], 'overview'));
        $root_nav->addSubNavigation('text_overview', $navigation);
    }
}

```

Nun möchten wir aber nicht nur die Texte übersetzen, die wir in einer Plugin-Klasse erstellen,
sondern auch in Controller-Klassen und views.
Dazu leiten wir alle Aufrufe von `_()` in Controllers auf das Plugin um,
sodass wir in Controllers einfach komfortable `$this->_()` aufrufen können.
Hierfür nutzen wir analog zur `Plugin`-Klasse die `Controller`-Klasse,
damit wir dies direkt für alle Controller übernehmen.

```php
<?php

namespace TextPlugin;

use PluginController;
use RuntimeException;

class Controller extends PluginController
{

    public function __construct($dispatcher)
    {
        parent::__construct($dispatcher);

        // Localization
        $this->_ = function ($string) use ($dispatcher) {
            return call_user_func_array(
                [$dispatcher->current_plugin, '_'],
                func_get_args()
            );
        };

        $this->_n = function ($string0, $tring1, $n) use ($dispatcher) {
            return call_user_func_array(
                [$dispatcher->current_plugin, '_n'],
                func_get_args()
            );
        };
    }

    public function __call($method, $arguments)
    {
        $variables = get_object_vars($this);
        if (isset($variables[$method]) && is_callable($variables[$method])) {
            return call_user_func_array($variables[$method], $arguments);
        }
        return parent::__call($method, $arguments);
    }

}
```

Nun sind Texte in Plugins und Controllers übersetzbar.
Texte in javascript Dateien können wie im Kern mit `String.toLocaleString()` übersetzt werden.
Texte in Model-Klassen wie `Text.php` müssen allerdings noch direkt
mit dem Aufruf von `dgettext(Plugin::GETTEXT_DOMAIN, $string)` als übersetzbar gekennzeichnet werden.
In view Dateien kann dahingehen `$controller->_($string)` oder alternativ `$_($string)` genutzt werden.

#### Übersetzungsdatei anlegen
Das Anlegen der Übersetzungsdatei geschieht in der Regel einfach mit dem Unix-Shellskript `makeStudIPPluginTranslations.sh`,
welches auf der [Entwickler-Installation von Stud.IP](https://develop.studip.de/studip/dispatch.php/document/download/1bea6c139b56abc3ef0c505731bcc6b6) verfügbar ist.
Es sammelt alle als übersetzbar gekennzeichneten strings und erstellt damit eine `.pot` Datei,
weshalb die Übersetzungsdatei auch erst angelegt werden sollte,
wenn das Plugin fertiggestellt wurde.  
Mithilfe eines Übersetzungseditors können die gesammelten Texte strings der `.pot`-Datei dann übersetzt werden
und eine maschinenlesbare `.mo`-Datei erzeugt werden.

## Zusammenfassung
Es wurde

* Eine `plugin.manifest` mit meta-daten erstellt ([Plugin-Manifest](PluginSchnittstelle#plugin-manifest))
* Eine Plugin-Klasse zur Initialisierung erstellt ([Plugin-Interfaces](PluginSchnittstelle#plugin-interfaces))
* Das Plugin installiert und aktiviert
* Eine Navigation für eine Hauptseite erstellt ([Navigation](Navigation))
* Jeweils eine Eltern-Klasse für Plugin- und Controller-Klassen erstellt
* Mit dem Autoloader die Klassen in `classes` und später in `models` automatisch eingebunden
* Ein Controller mit einer `action` und einer passender `view` erstellt ([Trails](Trails))
* Eine Migration für die Datenbank-Tabellen-Struktur ([Migrationen](Migrations))
* Eine SORM-Klasse für die Datenbank-Tabellen erstellt ([SimpleORMap](SimpleORMap))
* Erläutert wie js- und cc-Dateien einzubinden sind
* Eine Basis erstellt, um strings zu übersetzen ([Internationalisierung](Howto/Internationalisierung))

Der komplette bisher erstellte Code ist hier ([TextPlugin.zip](../assets/862928c482008450a61d0c4156987dee/TextPlugin.zip)) als ZIP-Datei verfügbar.

Zum [zweiten Teil](Plugin-Tutorial-II-(Beispiel-Funktionalitäten))
