---
title: Plugin-Tutorial
sidebar_label: Tutorial
---


In diesem Tutorial wird die Erstellung von Plugins vorgestellt. Da es in Stud.IP verschiedene Arten von Plugins gibt, werden diese anhand verschiedener Beispiele verdeutlicht.

### Das Grundgerüst bauen

Ein Stud.IP-Plugin besteht immer aus mindestens zwei Dateien: Der Plugin-Klasse mit dem PHP-Code und einer Textdatei mit Informationen über das Plugin (Name, Version, Autor usw.). Im Folgenden wird das Grundgerüst eines Plugins erstellt, welches nichts weiter macht als sich im System zu registrieren.

#### Plugin-Klasse

Die Plugin-Klasse kann im einfachsten Fall so aussehen:

**MyPlugin.php**

```php
<?php

class MyPlugin extends StudipPlugin implements SystemPlugin
{
}
```

Wichtig ist hierbei:
* Die Datei ist nach der Klasse ("`MyPlugin`") benannt und hat die Endung "`.class.php`".
* Die eigene Plugin-Klasse muss von der Klasse `StudIPPlugin` abgeleitet werden.
* Die Plugin-Klasse muss mindestens eines der Interfaces implementieren, die festlegen, an welchen Stellen in Stud.IP es aktiv werden kann. Im obigen Beispiel ist es das Interface `SystemPlugin`, womit das Plugin auf jeder Seite aktiv sein soll (mehr dazu später).

Das Plugin selbst tut nichts und tritt damit außerhalb der Plugin-Verwaltung nicht in Erscheinung.

#### plugin.manifest

Die Datei mit den Informationen über das Plugin wird von Stud.IP (unter anderem) verwendet, um dem Administrator Informationen zum Plugin anzeigen zu können. Sie hat immer den Namen "[`plugin.manifest`](PluginSchnittstelle#toc4)" und sollte mindestens folgende Einträge enthalten:

* *pluginname*: Der Name für das Plugin
* *pluginclassname*: Der Name der Plugin-Klasse (in PHP). Falls die Endung .class.php verwendet wird, kann das .class hier weggelassen werden.
* *origin*: Der Name oder die Mail-Adresse des Autors oder dessen Institution oder Gruppe
* *version*: Die Versionsnummer des Plugins
* *description*: Eine kurze Beschreibung, was das Plugin macht
* *studipMinVersion*: Die minimal erforderliche Stud.IP Version

Eine plugin.manifest Datei kann beispielsweise folgenden Inhalt haben:

```ini
pluginname=MyPlugin
pluginclassname=MyPlugin
origin=elmar@example.com
version=1.0
description=Ein völlig nutzloses Beispiel-Plugin
studipMinVersion=2.0
```


#### Plugin installieren

Aus den Plugin-Dateien muss nun ein Installationsarchiv erstellt werden. Dazu packt man alle Plugin-Dateien in ein ZIP-Archiv ein. Hierbei ist es wichtig, den Ordner, in dem das Plugin liegt, nicht mit einzupacken, da sonst die Installation fehlschlägt. Im Wurzelverzeichnis des Archivs sollten also direkt die Dateien plugin.manifest und die PHP-Datei mit der Plugin-Klasse sichtbar sein:

```text
Length      Date    Time    Name
---------  ---------- -----   ----
       71  2011-04-20 11:26   MyPlugin.php
      152  2011-04-20 11:26   plugin.manifest
---------                     -------
      223                     2 files
```

Anschließend kann man das Plugin über die [Plugin-Verwaltung](http://docs.studip.de/admin/Admins/PluginVerwaltung) in Stud.IP installieren. Am einfachsten geht dies, indem man das ZIP-Archiv einfach in den Drag&Drop-Bereich auf der linken Seite der Plugin-Verwaltung zieht.

Attach:plugin-install.png

Nach der Installation muss das Plugin auf der Seite der Plugin-Verwaltung noch aktiviert werden, damit es auch tatsächlich geladen wird. Außerdem kann man in der Spalte "Aktionen" die Rechteverwaltung für das Plugin vornehmen. In der Voreinstellung kann ein Plugin von allen angemeldeten Nutzern im System verwendet werden.

#### Das Plugin ausbauen

Nach der Installation des Plugins liegt dieses im Ordner `/public/plugins_packages/<origin>/<Plugin-Name>/`. "origin" ist hierbei der Wert des Konfigurationsparameters "origin" aus der plugin.manifest Datei. Während der Entwicklung des Plugins kann es einfacher sein, wenn man die installierte Version des Plugins unterhalb des Stud.IP Verzeichnisses weiter bearbeitet, da man sich dann die Neuinstallation des Plugins nach jeder Änderung sparen kann.

Je nachdem, welche Art von Plugin man programmieren möchte, muss das Plugin von unterschiedlichen Klassen abgeleitet werden und es müssen unterschiedliche Methoden implementiert werden.

### Vorstellung verschiedener Plugin-Typen

#### SystemPlugin

Angenommen, wir wollen, dass hinter dem Namen der Stud.IP-Installation in der Kopfzeile immer die Anmeldekennung des gerade angemeldeten Nutzers angezeigt wird. Wie funktioniert so etwas?

* Wir müssen dafür sorgen, dass das Plugin auf jeder Seite aktiv ist.
* Wir müssen irgendwie die Kennung des angemeldeten Nutzers ermitteln.
* Wir müssen diesen Text in die Kopfzeile einbauen können.

Der Punkt 1 ist zum Glück bereits erledigt: Da unser Plugin das Interface `SystemPlugin` implementiert, ist es automatisch auf jeder Seite aktiv.

Punkt 2 führt uns direkt in die Untiefen der Stud.IP-API. Zum Glück gibt es hier aber eine sehr einfach zu bedienende Funktion, die uns die aktuelle Nutzerkennung liefert: `get_username()`.

Für Punkt 3 kann man sich etwas CSS bedienen, um Inhalte auf einer Seite zu verändern:

```css
#id:after {
    content: "some text";
}
```

Dieses Stück CSS sorgt dafür, daß hinter dem Element mit der ID "`id`" im HTML der Text "some text" angezeigt wird. In unserem Fall hat das gesuchte Element in der Kopfzeile die ID "barTopFont". Soweit, so gut. Aber wie bekommen wir das CSS auf die Stud.IP-Seite?

Dazu gibt es eine API in Stud.IP, die es Plugins ermöglicht, Eingriffe in den Seitenaufbau vorzunehmen: [PageLayout](PageLayout). Unter anderem kann man damit auch eigenes CSS in die erzeugte Seite einbauen. Zusammen sieht das ganze dann etwa so aus:

```php
class MyPlugin extends StudipPlugin implements SystemPlugin
{
    public function __construct()
    {
        parent::__construct();

        $username = get_username();
        $css = "#barTopFont:after { content: '[$username]'; }";
        PageLayout::addStyle($css);
    }
}
```

Da ein System-Plugin keinen speziellen Einsprungpunkt für Stud.IP hat, wird der Code direkt in den Konstruktor integriert. Natürlich sollte man hier nicht vergessen, den Konstruktor der Basisklasse aufzurufen... Das fertige Plugin sieht dann so aus:

Attach:myplugin.png

##### Quellcode des Plugins

Attach:MyPlugin.zip

#### PortalPlugin  Ein eigenes Plugin auf der Startseite

Kommen wir nun zu einem weiteren Beispiel, das zeigt, wie ein Plugin eigene Inhalte auf der Startseite anzeigen kann. Als Beispiel wollen wir bei jedem Aufruf der Seite ein zufälliges Zitat aus einer bekannten Fernsehserie einblenden. Und das sowohl für angemeldete als auch für nicht angemeldetet Nutzer.

##### Das Interface `PortalPlugin`

Das Plugin-Interface PortalPlugin bietet jedem Plugin die Möglichkeit, einen eigenen Kasten auf der Startseite zu platzieren, ganz analog zu den vorhandenen Kästen für die Termine, systemweiten Ankündigungen oder Umfragen. Dieses sieht folgendermaßen aus:

```php
interface PortalPlugin
{
    /**
     * Return a template (an instance of the Flexi_Template class)
     * to be rendered on the start or portal page. Return NULL to
     * render nothing for this plugin.
     *
     * The template will automatically get a standard layout, which
     * can be configured via attributes set on the template:
     *
     *  title        title to display, defaults to plugin name
     *  icon_url     icon for this plugin (if any)
     *  admin_url    admin link for this plugin (if any)
     *  admin_title  title for admin link (default: Administration)
     *
     * @return object   template object to render or NULL
     */
    function getPortalTemplate();
}
```

Offensichtlich benötigt man ein *Template*-Objekt für die Ausgabe, und man kann noch gewisse Eigenschaften des erzeugten Kastens vorgeben (Icon, Titel usw.).

##### Wichtig: Flexi-Templates

Text- oder HTML-Ausgabe sollte in Stud.IP immer über *Templates* passieren, das sind (in PHP geschriebene) Vorlagen für die Ausgabe, die mit Platzhaltern bestückt sein können, um Werte aus dem Programmcode an bestimmte Stellen in der Ausgabe zu bringen. Eine komplette Beschreibung mit vielen Beispielen befindet sich hier im Wiki unter dem Punkt [FlexiTemplates](FlexiTemplates).

In unserem Fall brauchen wir nur einen kleinen Bereich, in dem ein vorformatierter Text angezeigt werden kann. Das Template dafür steht in einer eigenen PHP-Datei und kann etwa so aussehen:

**templates/fortune.php**

```php
<pre>
<?= htmlReady($fortune) ?>
</pre>
```

Das "`<pre>`" sollte nicht überraschend sein, aber wieso steht da *htmlReady()*? Nun, damit spezielle Zeichen in dem auszugebenden Text nicht versehentlich vom Browser als HTML-Markup ausgewertet werden (man stelle sich vor, die Variable $fortune enthielte selbst Dinge wie "`<b>`"), müssen diese vor der Ausgabe entsprechend kodiert werden. Aus "`<b>`" würde dann "&lt;b&gt;", was vom Browser wieder als "`<b>`" angezeigt würde.

Daher ist es wichtig, bei jeder Ausgabe eines Werts in einem Template an die Verwendung von *htmlReady()* (oder *formatReady()*, wenn man mit der Stud.IP-Formatierung arbeitet) zu denken. Die einzige Ausnahme sind Werte, die bereits fertige HTML-Fragmente für die Anzeige enthalten. Ein Beispiel dafür sehen wir später.

##### Die Plugin-Klasse

Das Plugin wird wie im ersten Beispiel von der Klasse `StudipPlugin` abgeleitet, implementiert jetzt aber - wie oben besprochen - das Interface `PortalPlugin`. Zusätzlich benötigen wir nun noch eine eigene *TemplateFactoy* für unser Plugin, damit das Template aus dem Ordner des Plugins geladen werden kann. Es ist üblich, alle Templates für ein Plugin in einem Ordner mit dem Namen "`templatess`" abzulegen. Der Code zum Laden des Templates sieht dann so aus:

```php
$template_path = $this->getPluginPath().'/templates';
$template_factory = new Flexi_TemplateFactory($template_path);
$template = $template_factory->open('fortune');
```

Die Methode *getPluginPath()* eines Plugins liefert einen Dateisystempfad zum Installationsordner des Plugins, relativ zu diesem Ort kann man dann z.B. Template-Dateien laden. Dieser Pfad ist aber nur serverseitig gültig, er kann nicht zum erzeugen von URLs verwendet werden.

Schließlich soll unser Plugin noch ein Icon und einen Titel für die Anzeige bekommen. Dazu muß man die entsprechenden Attribute "icon_url" und "title" in dem Template setzen. Als Titel setzen wir einfach den Namen des Plugins ein. Für die URL des Icons benötigen wir eine URL zu einer Ressource im Plugin, dazu gibt es - analog zu *getPluginPath()* für serverseitge Pfade - auch eine Methode *getPluginURL()*, die eine für den Nutzer gültige URL auf den Installationsort des Plugins liefert. Diese URL kann dann als Basis für die Icon-URL verwendet werden:

```php
$template->title = $this->getPluginName();
$template->icon_url = $this->getPluginURL() . '/images/icon.gif';
```

Schließlich muß noch die Template-Varianble *$fortune* aus der Template-Datei mit einem Zitattext gefüllt werden. Eine einfache Möglichkeit ist, einfach das (hoffentlich auf dem Rechner installierte) *fortune* Kommando aufzurufen, das gleiche eine entsprechende Zitat-Datenbank mitbringt. Die komplette Plugin-Klasse sieht dann am Ende so aus:

```php
class Fortune extends StudipPlugin implements PortalPlugin
{
    public function getPortalTemplate()
    {
        $template_path = $this->getPluginPath().'/templates';
        $template_factory = new Flexi_TemplateFactory($template_path);
        $template = $template_factory->open('fortune');

        $template->title = $this->getPluginName();
        $template->icon_url = $this->getPluginURL() . '/images/icon.gif';

        $template->fortune = shell_exec('/usr/games/fortune startrek');
        return $template;
    }
}
```

Im template-Verzeichnis des Plugins muss nun folgende Template Datei namens fortune.php angelegt werden:

```php
<div id="fortune">
            <?= $fortune ?>
</div>
```

#### HomepagePlugin - Ein Plugin auf der Profilseite

Im nächsten Beispiel geht es nun um ein Plugin auf der Profilseite. Als Aufgabe nehmen wir uns vor, eine einfache Version der "Eigenen Kategorien" des Profils in einem Plugin nachzubauen: Es soll in einem Kasten im Nutzerprofil ein (ggf. formatierter) Text angezeigt werden, der dort vom Nutzer selbst auch bearbeitet werden kann. Im Gegensatz zu den "Eigenen Kategorien" gibt es aber immer nur einen Kasten und es können weder Titel noch die Sichtbarkeit eingestellt werden. Dazu müssen wir uns mit folgenden neuen Fragen beschäftigen:

# Anzeige von Inhalten auf der Profilseite
# Umgang mit der Stud.IP-Formatierung
# Ausgabe und Auswertung von Formularen (Nutzerinteraktion)
# Speichern von Nutzereingaben in der Datenbank

##### Das Interface `HomepagePlugin`

Ganz analog zur Anzeige von eigenen Inhalten auf der Startseite gibt es auch ein entsprechendes Interface zur Anzeige von Inhalten auf der Profilseite: `HomepagePlugin`. Es bietet die gleichen Möglichkeiten und wird auch exakt genauso benutzt:

```php
interface HomepagePlugin
{
    /**
     * Return a template (an instance of the Flexi_Template class)
     * to be rendered on the given user's home page. Return NULL to
     * render nothing for this plugin.
     *
     * The template will automatically get a standard layout, which
     * can be configured via attributes set on the template:
     *
     *  title        title to display, defaults to plugin name
     *  icon_url     icon for this plugin (if any)
     *  admin_url    admin link for this plugin (if any)
     *  admin_title  title for admin link (default: Administration)
     *
     * @return object   template object to render or NULL
     */
    function getHomepageTemplate($user_id);
}
```

Auch hier benötigt man natürlich wieder ein Template-Objekt und entsprechende Template-Dateien für die Ausgabe.

#### Ausgabe von formatiertem Text

Das Template für die Anzeige kann wieder sehr einfach gehalten werden:

Attach:editbox.png

**templates/fortune.php**

```php
<div id="edit_box">
    <?= formatReady($text) ?>
</div>
```

Anders als bei dem vorhergehenden Beispiel wird hier die Funktion *formatReady()* aufgerufen, um den auszugebenden Text für die Anzeige aufzubereiten. Während *htmlReady()* sich nur um die Kodierung von Sonderzeichen kümmert, wertet *formatReady()* zusätzlich auch die Stud.IP-Formatierungssyntax aus, d.h. bestimmte [Markierungen im Text](http://docs.studip.de/help/2.0/de/Basis/VerschiedenesFormat) führen zu speziellen Hervorhebungen bei der Anzeige. Zusätzlich können auch Listen, Tabellen, Links, Bilder und anderes angezeigt werden. Die "*id*" auf dem umschließenden DIV wird später verwendet, um diesen Punkt auf der Profilseite gezielt anspringen zu können.

Für die Erstellung des Templates kann man im wesentlichen den Code aus dem letzten Beispiel wiederverwenden (auf ein Icon verzichten wir hier). Der anzuzeigende Inhalt kommt von einer eigenen Methode *getContents()*, die später den Text aus der Datenbank lesen wird:

```php
class EditBox extends StudipPlugin implements HomepagePlugin
{
    public function getHomepageTemplate($user_id)
    {
        $template_path = $this->getPluginPath().'/templates';
        $template_factory = new Flexi_TemplateFactory($template_path);
        $template = $template_factory->open('edit_box');

        $template->title = $this->getPluginName();
        $template->text = $this->getContents($user_id);
        return $template;
    }
}
```

##### Formulare und URLs

Das oben gezeigte Template erlaubt noch keine Bearbeitung des angezeigen Inhaltes. Darum soll es in nun diesem Abschnitt gehen: Wir brauchen dazu noch ein entsprechendes Template für die Eingabe - also ein HTML-Formular - sowie etwas Logik in unserem Plugin zur Auswertung dieser Eingabe. Falls der Stud.IP-Nutzer seine eigene Profilseite aufruft, sollte er einen speziellen Editiermodus des Plugins aktivieren können (dazu gleich mehr), der dann ein entsprechedes Formular anzeigt:

Attach:editbox-edit.png

**templates/edit_mode.php**

```php
<form id="edit_box" action="<?= URLHelper::getLink('#edit_box') ?>" method="POST">
    <textarea name="text" style="display: block; width: 80%; height: 8em;"><?=
        htmlReady($text)
    ?></textarea>
    <?= makeButton('uebernehmen', 'input', false, 'save') ?>
    <?= makeButton('abbrechen', 'input', false, 'cancel') ?>
</form>
```

Das Formular ist sehr einfach aufgebaut: Es gibt eine TEXTAREA zum Bearbeiten des Inhalts sowie zwei Schaltflächen zum Speichern bzw. Verwerfen der Änderungen. Auch hier darf das *htmlReady()* natürlich nicht fehlen. Ein *formatReady()* wäre an dieser Stelle übrigens falsch, da wir ja nicht die bereits formatierte Ansicht bearbeiten wollen. Zur Anzeige von Formularschaltflächen gibt es eine Hilfsfunktion *[makeButton()](http://hilfe.studip.de/api/language_8inc_8php.html#a029ae0013a8aa35f8cea9e5ab43cda16)* in Stud.IP, die wir auch hier verwenden. Dies ist ein Beispiel für eine Funktion, die bereits fertige HTML-Fragmente liefert, das Resultat von *makeButton()* darf also nicht mehr mit htmlReady() behandelt werden. Das von makeButton() erzeugte HTML wird hinterher etwa so aussehen:

```php
<input class="button" type="image" src="[...]/uebernehmen-button.png" name="save">
<input class="button" type="image" src="[...]/abbrechen-button.png" name="cancel">
```

Beim Absenden des Formulars soll unser Plugin die eingegebenen Daten verarbeiten können, wir müssen also dafür sorgen, daß wieder die Profilseite (dort lebt ja das Plugin) angezeigt wird, zusätzlich soll der Kasten des Plugins direkt angesprungen werden. Als URL für das Formular müssen wir also eine passende URL zur Profilseite generieren, inklusive Ansprungpunkt auf der Seite. URLs auf Seiten in Stud.IP werden immer - bis auf wenige, spezielle Ausnahmen - über die Klasse [URLHelper](URLHelper) erzeugt. Im einfachsten Fall gibt man nur den Namen des ensprechenden PHP-Skriptes für die Seite im Aufruf von *URLHelper::getURL()* an und bekommt die entsprechende URL zurück. Hier ist es sogar noch einfacher: Wir befinden uns ja schon auf der Profilseite, müssen also nur den Ansprungpunkt auf der aktuellen Seite angeben: "`#edit_box`".

Auch hier gilt: Beim Einsetzen von Werten in HTML muß man sich immer überlegen, ob noch ein *htmlReady()* erforderlich ist. Normalerweise wäre das der Fall, da das Erzeugen von Links aber recht häufig vorkommt, gibt es hierzu eine Hilfsfunktion im `URLHelper`, die das Kodieren gleich mit erledigt: *URLHelper::getLink()*. Das Resultat dieser Funktion kann also (wie *makeButton()*) immer direkt in die Ausgabe eingesetzt werden.

##### Verarbeitung von Nutzereingaben

Unser Plugin kennt zwei Arten von Nutzerinteraktion: Aktivieren des *Editiermodus* und Abspeichern bzw. Verwerfen der Formulareingaben. Beides ist nur für den Besitzer des angezeigten Profils erlaubt. Um darauf reagieren zu können, müssen wir unsere Plugin-Methode um einige Code-Zeilen erweitern (und zwar in der Funktion getHomepageTemplate zwischen den Zeilen $template = $template_factory->open('edit_box'); und $template->title = $this->getPluginName();) :

```php
if ($user_id == $GLOBALS['user']->id) {
    if (Request::int('edit')) {
        $template = $template_factory->open('edit_mode');
    } else if (Request::submitted('save')) {
        $this->setContents($user_id, Request::get('text'));
    }

    $template->admin_url = URLHelper::getURL('#edit_box', array('edit' => 1));
    $template->admin_title = 'Inhalt bearbeiten';
}
```

In der globalen Variablen `$user` ist der gerade in Stud.IP angemeldete Nutzer hinterlegt. Wir können also leicht überprüfen, ob der aktuelle Nutzer auch der Besitzer des angezeigten Profils ist. Falls ja, kann der Editiermodus über ein spezielles Icon in der Titelzeile des Kastens für das Plugin angewählt werden (ganz rechts, analog zu dem Icon für eigene Ankündigungen und Umfragen). Über das Template kann der Link, ggf. mit weiteren URL-Parametern, und ein Tooltip für das Icon vorgegeben werden. Ist der Editiermodus aktiviert worden, wird das entsprechende Template geladen.

Zur Abfrage von URL- und Formular-Parametern in Stud.IP sollte man immer die Klasse [Request](Request) verwenden, die unter anderem auch typsicheren Zugriff auf die Parameterwerte erlaubt. Die Namen der Parameter entsprechen denen aus dem Template, d.h. "`Request::submitted('save')`" ermittelt, ob die Schaltfläche mit dem Namen "save" im Formular angeklickt wurde.


#### Plugins mit Navigation

Die bisher gezeigten Beispiele für Plugins haben sich alle in vorhandene Seiten integriert, ein Plugin kann aber auch komplett eigene Seiten in Stud.IP anbieten oder sogar Inhalte ausliefern, die gar nicht auf das Stud.IP-Design zurückgreifen wie Web-Services oder Datei-Downloads. Wie das funktioniert wird im diesem Abschitt beschrieben. Auch hier fangen wie wieder mit einem kleinen Beispiel an, diesmal soll die Aufgabe so aussehen:

* Das Plugin soll ein eigenes Icon in der Navigation bekommen.
* Das Plugin soll als Punkt auf der Startseite verlinkt sein.
* Es soll beim Aufruf eine komplett eigenständige Seite anzeigen, inklusive einer Infobox:

Attach:demoplugin.png


### Weitere Entwicklungsschritte

#### Zugriff auf die Datenbank

Zugriffe auf die Datenbank werden in Stud.IP über [SimpleORMap](SimpleORMap) (SORM) durchgeführt. Plugins können eine eigene SimpleORMap-Klasse definieren und damit die Vorteile von SimpleORMap nutzen.

##### Neue Tabelle mittels Migration erstellen

Auch wenn es sich um das Anlegen einer neuen Datenbanktabelle handelt, kann hierfür eine Migration verwendet werden. Dadurch werden das Anlegen der Datenbanktabelle und spätere Migrationen auf die gleiche Art und Weise durchgeführt.

Zum Erstellen einer Migration wird im Plugin-Verzeichnis ein neuer Ordner namens "migrations" erstellt. In diesem werden nummerierte Migrations-Dateien untergebracht, wobei es sich um PHP-Skripte handelt, welche eine einzige Klasse enthalten und deren Dateiname einem besonderen Schema entspricht. Der Dateiname darf nur kleingeschriebene Buchstaben beinhalten, da sonst keine Migration durchgeführt werden kann. 01_initial.php wäre zum Beispiel ein gültiger Dateiname, während 01_Initial.php (großes "i") zu einem Fehler führen würde.

In der Migrations-Datei wird nun eine Klasse erzeugt, welche die Klasse "Migration" erweitert. Ihr Name kann aus Gründen der Einfachheit genauso gewählt werden wie der Dateiname hinter der Nummerierung, im Beispiel also "Initial" (hier sind Großbuchstaben erlaubt). Die Klasse muss die Methoden up() und down() implementieren. up() dient zum Durchführen einer Migration, während down() die Änderungen an Datenbanktabellen, welche mit up() durchgeführt wurden, rückgängig macht. Zum Zugriff auf die Datenbank bedient man sich der Klasse DBManager, welche durch die Methode get() eine Datenbankverbindung zurückliefert:

`$db = DBManager::get();`

Nun kann mit `$db->exec` SQL-Code auf der Datenbank ausgeführt werden, wie das folgende Beispiel zeigt:

```php
$db->exec("CREATE TABLE edit_box (
    user_id varchar(32) NOT NULL,
    content text NOT NULL,
    PRIMARY KEY (user_id)
);"
          );
```

Die Tabelle wurde angelegt, ist aber noch leer. An dieser Stelle kann natürlich noch mit einem zweiten `$db->exec` Aufruf und einer `INSERT INTO` SQL-Anweisung die Tabelle gefüllt werden. Damit ist die up()-Methode fertig. Nun muss die down()-Methode geschrieben werden. Da es sich um die erste Migration handelt, kann beim rückgängig machen der Migration die Tabelle gelöscht werden:

```php
$db = DBManager::get();
$db->exec("DROP TABLE hallo_welt;");
```

Damit ist die Migrations-Datei fertig bearbeitet.


##### Anlegen einer SimpleORMap-Klasse

Nun muss im Plugin eine SORM-Klasse angelegt werden, mit der Einträge aus der Datenbank-Tabelle in Objekte umgewandelt werden können. Dazu legt man im Plugin-Verzeichnis den Unterordner models an und in diesem eine PHP-Datei, welche die Datenklasse, welche man gerne in der Datenbank haben möchte, beinhaltet. Der Name der PHP-Datei muss dem Namen der Klasse entsprechen. Die Klassendefinition kann im einfachen Fall folgendermaßen aussehen:

```php
class EditBox extends SimpleORMap {

    static protected function configure($config = array()) {

        $config['db_table'] = 'edit_box';

        parent::configure($config);
    }
}
```

Damit können Objekte der Klasse EditBox aus der Datenbank geholt werden, sofern die Datenbanktabelle existiert.


**Hinweis:** Man sollte beim Erstellen von neuen Tabellen möglichst immer die Standardeinstellungen der Datenbank übernehmen, d.h. keine Zeichenkodierung oder Storage-Engine vorgeben.

##### Von der Datenbank lesen und schreiben

Lese- und Schreibzugriffe werden im Wiki-Artikel [SimpleORMap](SimpleORMap) erklärt.


#### Lokalisierung mit gettext

Zur Lokalisierung eines Plugins sind mehrere Schritte notwendig. Zuerst müssen die Templates zur Übersetzung vorbereitet werden. Mit deren Hilfe werden die Übersetzungsdateien erzeugt.

##### Übersetzungen in Templates

Um in den Templates eines Plugins Übersetzungen verwenden zu können, wird die Funktion dgettext verwendet. Diese funktioniert fast wie gettext, mit dem Unterschied, dass dgettext zuerst die Übersetzungs-Domäne mitgegeben wird, bevor der zu übersetzende String übergeben wird. Dies hängt damit zusammen, dass die zu übersetzenden Strings des Plugins nicht in den Übersetzungsdateien von Stud.IP gefunden werden können und deswegen im Plugin gesonderte Übersetzungsdateien angelegt werden müssen.
Ein zu übersetzender Text wird folgendermaßen umgeschrieben:
vorher: `echo "Hallo Welt!";`
nachher: `echo dgettext("MyPlugin", "Hallo Welt!");`

Mittels dgettext wurde angegeben, dass die Übersetzung der Zeichenkette "Hallo Welt" in der Übersetzungs-Domäne "MyPlugin" gefunden werden kann, welche nur im Plugin genutzt wird.

##### Anlegen der Übersetzungsdateien

Zum Anlegen der Übersetzungsdateien kann das Unix-Shellskript makeStudIPPluginTranslations.sh verwendet werden, welches für die Übersetzung in mehrere Sprachen verwendet werden kann und einfach für andere Projekte angepasst werden kann. Es befindet sich auf der Entwickler-Installation von Stud.IP: [https://develop.studip.de/studip/dispatch.php/document/download/1bea6c139b56abc3ef0c505731bcc6b6](https://develop.studip.de/studip/dispatch.php/document/download/1bea6c139b56abc3ef0c505731bcc6b6)

Die Ordnerstruktur, in welcher die Übersetzungsdateien unterhalb des Plugin-Verzeichnisses liegen, muss exakt dem folgenden Schema entsprechen: /locale/<Abkürzung der Sprache>/LC_MESSAGES/. Neben Englisch sind natürlich auch weitere Sprachen möglich. Da zurzeit (Juni 2016) in Stud.IP nur Deutsch und Englisch als Sprachen verfügbar sind, können zusätzlichen Sprachen, in die das Plugin übersetzt wurde, nicht aktiviert werden.
Nach der Ausführung des Skriptes liegen im Ordner LC_MESSAGES eine Datei vor: MyPlugin.pot. Diese kann nun mit einem Übersetzungs-Editor, wie beispielsweise Poedit, bearbeitet werden. Der Editor sollte beim Speichern der Übersetzungen aus der pot-Datei automatisch eine .mo-Datei erzeugen, sodass die Übersetzung in maschinenlesbarer Form vorliegt.

#### Anpassungen im Konstruktor der Plugin-Klasse

Um festzulegen, dass Übersetzungen im Plugin von der eigenen Übersetzungs-Domäne bezogen werden sollen, muss im Konstruktor der Plugin-Klasse folgender Code eingefügt werden:
`bindtextdomain('MyPlugin', __DIR__ . '/locale');`

Mittels bindtextdomain wird die Übersetzungs-Domäne auf "MyPlugin" festgelegt. Damit gettext auch weis, wo die zugehörigen Übersetzungsdateien zu finden sind, wird der absolute Pfad zum Unterordner des Plugins, in welchem die Übersetzungen liegen, benötigt.
WICHTIG: `$this->getPluginPath()` liefert nur einen relativen Pfad, welcher in einem Unterordner des Stud.IP Installationsverzeichnisses beginnt und kann deswegen an dieser Stelle nicht verwendet werden, um den Pfad zu den Übersetzungsdateien anzugeben!

Nach diesen Schritten sind alle Voraussetzungen erfüllt, um ein Plugin lokalisieren zu können.

#### Controller im Plugin anlegen

Es kann notwendig sein, dass ein Plugin eigene Controller besitzt, welche eigene Seiten im Plugin bereitstellen. Solche Seiten werden in [Trails](Trails) realisiert. Trails ist ein Framework, welches das MVC-Paradigma umsetzt, sodass die Programmlogik (Controller) von der HTML-Ausgabe (View) und dem Datenbankmodell (Models) getrennt ist. Da in Stud.IP bereits SimpleORMap für die Umsetzung von Modellen verwendet wird, bleiben nur noch Ansichten (views) und Controller übrig, die über Trails umgesetzt werden müssen.

##### Erstellen eines Controllers

Im Verzeichnis des Plugins wird ein Unterordner namens "controllers" angelegt. In diesem wird für jeden Controller eine eigene PHP-Datei angelegt, in welcher jeweils nur eine Controller-Klasse enthalten ist. Der Dateiname, in welchem der Controller enthalten ist, wird in Kleinbuchstaben gehalten. Die Klasse, welche den Controller implementiert, wird in der üblichen Notation (Großbuchstaben an jedem Wortanfang, ohne Unterstriche) geschrieben. Sie erweitert die Klasse PluginController.

Vor der Klassendefinition sollte aus Kompatibilitätsgründen mit alten Stud.IP Versionen noch folgende Zeile eingefügt werden:
`require_once('app/controllers/plugin_controller.php');`

Ein einfacher Controller kann so aussehen:

```php
<?php
class HalloController extends PluginController {
    public function index_action() {
        $this->text = dgettext('MyPlugin', 'Hallo Welt!');
    }
}
```

##### Erstellen einer Ansicht (view)

Jede Ansicht muss im Unterordner "views" des Plugin-Ordners angelegt werden. Dort wird für jeden Controller ein eigener Unterordner erzeugt, in welchem dann die einzelnen Ansichten abgelegt sind. Für jede Aktion eines Controllers wird eine eigene Ansicht erzeugt, wobei jede Ansicht ihre eigene PHP-Datei besitzt. In einer Ansicht kann beliebiger HTML-Code untergebracht sein. Attribute des Controllers sind als einfache Variablen in normaler PHP-Syntax abrufbar.

Der obige Controller besitzt nur eine Aktion: "index". Außerdem heißt er "HalloController" und seine PHP-Datei heißt folglich hallo.php und liegt im Ordner /controllers/. Die zugehörige Ansicht (view) muss im Ordner /views/hallo/index liegen. Für obigen Controller kann sich die Ansicht auf folgenden Code beschränken:

```php
<strong><?= $text; ?></strong>
```

Das Attribut `$this->text` aus dem Controller wurde einfach zu `$text`. Andere Attribute der Controller-Klasse werden ebenfalls der Ansicht übergeben, beispielsweise das Attribute `$this->plugin`, welches vordefiniert ist.

#### Zugriff für nicht angemeldete Nutzer

Wollen wir, daß unser Plugin auch für nicht angemeldete Nutzer sichtbar ist, so muß man der Installation noch bei den Rechteeinstellungen auswählen, daß neben den voreingestellten Standardrollen auch die Rolle "nobody" (diese Rolle ist speziell für nicht angemeldete Nutzer) das Plugin verwenden kann:

Attach:roles-nobody.png

Das installierte Plugin sieht dann beim Aufruf im System so aus:

Attach:fortune.png

##### Quellcode des Plugins

Attach:Fortune.zip

### Veröffentlichung

Ist das eigene Plugin funktionsfähig, kann es auf den Stud.IP Marktplatz hochgeladen werden, damit andere das Plugin testen und nutzen können.

Um ein Plugin in den Martkplatz einstellen zu können, ist ein Benutzerkonto auf der Stud.IP Installation [https://develop.studip.de](https://develop.studip.de) erforderlich. Dort gibt es in der oberen Leiste einen Reiter namens PluginMarktplatz. Auf diesem befindet sich ein Reiter namens "Meine Plugins", unter welchem das HalloWelt-Plugin hochgeladen werden kann. Nach dem Klick auf "Meine Plugins" wählt man dazu im linken Bereich "Neues Plugin eintragen" aus und füllt den sich öffnenden Dialog auf. Bildschirmfotos vom Plugin zeigen anderen Benutzern schnell, was das Plugin alles kann und sollten daher hinzugefügt werden.

Im Punkt "Release hinzufügen" wählt man "als Datei" aus. Nun packt man das fertige Plugin erneut in eine ZIP-Datei und wählt diese nach dem Klick auf den "Durchsuchen" Button im Dialog aus. Nach dem Klick auf "Speichern" wurde das Plugin hochgeladen und muss von einem Administrator des Marktplatzes freigeschaltet werden. Sobald dies geschehen ist, wird das Plugin auf der Startseite des Plugin-Marktplatzes unter "Neueste Plugins" aufgeführt.

**Herzlichen Glückwunsch zum ersten veröffentlichten Stud.IP Plugin!**

Moritz Strohm hat ein weiteres Tutorial zur Erstellung von Plugins
erstellt. Dieses finden Sie hier: https://develop.studip.de/studip/dispatch.php/document/download/5747961f81b385b1520cf7dc393f1db6
