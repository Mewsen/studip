---
title: Trails für die Kernentwicklung
sidebar_label: Trails (Web-Framework)
---

Trails ist ein eigenständiges MVC-Framework, welches in Stud.IP fertig konfiguriert zur Verfügung steht.

> **Achtung**: Die folgende Dokumentation bezieht sich auf die Verwendung von Trails für die Stud.IP-Kernentwicklung. Für die Verwendung von Trails in Plugins lesen Sie bitte [Trails in Plugins](TrailsInPlugins).

## Trails & Stud.IP-Kern

Im Folgenden gibt es eine kleine Einführung zur Entwicklung von Trails-Seiten in Stud.IP.

Trails folgt dem [MVC-Paradigma](http://de.wikipedia.org/wiki/Model_View_Controller). Diesem Paradigma folgend gibt es in Stud.IP im Hauptverzeichnis einen Ordner namens `app`, welcher die Unterordner `controllers` und `views` besitzt. Die Models finden sich hingegen unter `lib/models`.

## Die Struktur

Der Controller ist der Dreh- und Angelpunkt für eine Seite.

Einen neuen Controller erstellt man im Verzeichnis `app/controllers`. Im einfachsten Fall erstellt man dort direkt eine PHP-Datei. Handelt es sich um eine größere Sammlung von Controllern, kann man auch Unterverzeichnisse erstellen. Die dortige Pfadstruktur überträgt sich dabei 1:1 auf die URL.

Ein Controller sollte nie direkt Zugriff auf die Datenbank nehmen und auch sonst so wenig wie möglich Datenstrukturen generieren. Er ist dafür zuständig, die korrekten Daten aus dem richtigen model in die view zu schaffen. Models liegen im passenden Verzeichnis, nämlich 'app/models' und müssen zur Verwendung mittels `require_once` im Controller inkludiert werden. Ab Stud.IP Version 2.5 werden Models aus `app/models` automatisch geladen.

Ausgaben passieren prinzipiell nur innerhalb der view in Templates. Die Templates liegen dabei in `app/views` und haben darunter folgende Pfadstruktur `/pfad_zum_controller/name_des_controllers/name_der_action.php`

## Beispiel

Um das ganze Konzept und die Möglichkeiten zu veranschaulichen wird im Folgenden en detail eine Beispiel-Trails-Seite erklärt.

## Der Controller

### Nur in Stud.IP Eingeloggte oder frei verfügbar?

`app/controllers/example/page.php`

```php
require_once 'app/controllers/authenticated_controller.php';
require_once 'app/controllers/studip_controller.php';

class Example_PageController extends AuthenticatedController {
```

Die erste Entscheidung, die man treffen muss, ist, ob man diesen Controllern nur als eingeloggter Nutzer sehen kann oder auch wenn man nicht eingeloggt ist. Dafür entscheidet man sich einfach für eine von zwei Klassen, von denen man erbt.

* Wie der Name schon sagt, ist die Klasse `AuthenticatedController` diejenige, die dafür sorgt, dass nur eingeloggte Nutzer diesen Trails-Controller aufrufen können. 
* Erbt man von `StudipController`, so ist eben (erstmal) kein einloggen nötig. Das müsste der Controller dann bei Bedarf selbst tun. Der Klassenname des Controllers richtet sich nach dem Pfad der Datei. Die Datei `controllers/pfad1/pfad2/dateiname.php` muss dann so lauten: `Pfad1_Pfad2_DateinameController`

### Die index_action - Wichtigste Action im Controller

```php
function index_action($param1 = false, $param2 = false)
    {
        // Daten holen

        $this->daten = array('index', 'Hier wird automagisch das in views/exmaple/asite/index.php hinterlegte Template verwendet. Der Dateiname des Templates ist immer gleich der Action');
    }
```

Hierbei handelt es sich nun um eine Action. Davon kann es in jedem Controller beliebig viele geben. Die `index_action` hat dabei einen Sonderstatus. Wird in der URL keine Action angegeben, so dient diese als Fallback.

### Das Url-Schema

Diese Action kann in Stud.IP nun wie folgt aufgerufen werden:

[`http://irgendeinstudip/dispatch.php/example/pag/index`](http://irgendeinstudip/dispatch.php/example/pag/index)

Diese Url hat dabei folgendes Schema:

[`http://irgendeinstudip.de/dispatch.php/{pfad/zum/controller}/{name_des_controllers}/{name_der_action}[/parameter1][/parameter2][...]`](http://irgendeinstudip.de/dispatch.php/{pfad/zum/controller}/{name_des_controllers}/{name_der_action}\[/parameter1\]\[/parameter2\]\[...\])

### Templates für Actions

Das Besondere am Trails-Framework ist, dass man sich nicht erst aus der Template-Factory ein Template holen muss, sondern dass (solange man nichts anderes sagt) implizit ein Template, welches zur Action gehört, anzeigt.

Diese Templates liegen unter `app/views` und dort in diesem Fall unter `example/page/index.php`.

Variablen an dieses Template übergibt man, indem man sie mittels `$this` setzt. Im Beispiel oben sieht man, dass `$this->daten` ein Array erhält. Im Template hat man dann automatisch eine Variable `$daten` zur Hand, die die im Controller zugewiesenen Werte enthält. Dazu weiter unten im Ausgabetemplate mehr.

### Manipulation des Kontrollflusses - I

Eine Action kann bei Trails mehr tun, als nur Daten an ein automatisch geladenes Template zu übergeben. Sie kann auch auf den Kontrollfluss einwirken.

Dazu folgende Beispiel-Actions:

```php
function redirect_action() {
    $this->redirect('example/asite/helloworld/Hallo Welt! Dieses mal sogar weitergeleitet von redirect!');
}
```

```php
function backendwithmessage_action()
{
    // do something
    $this->flash['nachricht'] = array('message' => array('Diese Nachricht wurde bereits in der delete_action in der reservierten Variable flash gespeichert!'));

    // return to index-action
    $this->redirect('example/asite/index');

}
```

Diese Action beinhaltet zwei der wohl wichtigsten Möglichkeiten von Trails.

### Routing in Trails

Zum einen kann man mit `$this->redirect({pfad_zum_controller}/{name_des_controllers}/{action}[/parameter]);` auf eine andere Action in einem beliebigen anderen Controller weiterleiten. Das ermöglicht es einem Actions zu haben, die keine eigene Ausgabe brauchen, da sie z.B. nur einen Eintrag löschen und danach die selbe Seite wieder anzeigen. Gibt es eine neue Aktion, baut man einfach eine weitere Action ein und leitet dann passend weiter.

Seit Stud.IP 5.1 kann `redirect()` genauso wie `url_for()` bzw. `link_for()` benutzt werden. Das heisst, es können beliebig viele Parameter angegeben werden, die dann zu der URL zusammengebaut werden, auf die weitergeleitet werden soll. Einzige Ausnahme: Es können keine URLs **und** weitere Parameter übergeben werden.

### Persistente Werte

Die Möglichkeit des Routens führt uns direkt zu einem weiteren Aspekt von Trails. Was nun, wenn so eine "verdeckt" operierende Aktion eine Statusmeldung auf der Hauptseite, zu der sie hin-routet haben möchte? Für diesen und ähnliche Zweck gibt es die spezielle Variable _flash_.

Dieser Variablen kann man direkt einen Wert oder einen Wert an einer Stelle in einem Array zuweisen (wie im Beispiel verwendet). Dieser Wert bleibt nun solange in der Variable `$flash` gespeichert, bis er ausgelesen wird. 

In einer Action kann man dann dort mittels `$this->flash` zugreifen, im Template einfach `$flash`.

### Manipulation des Kontrollflusses - II

Außer `$this->redirect` gibt es noch weitere Möglichkeiten zum Eingriff in den Kontrollfluß.

```php
function helloworld_action($text = 'Hallo Welt!') {
    $this->render_text(
        'helloworld, $this->render_text(\*. htmlReady(urldecode($text)) .'\')<br>' .
        'Hier wird das einfach nur Text ausgegeben, ohne Layout<br><br>' .
        htmlReady(urldecode($text))
    );
}
```

Ruft man `$this->render_text(...)` auf so wird nur der angegebene Text ohne jeglichen Stud.IP-Kontext ausgegeben.

```php
function index2_action() {
    $this->daten = array('index2, $this->render_action(\'index\')', 'Hier wird das Template für eine Action in diesem Controller gerendert, mit Layout');
    $this->render_action('index');
}
```

`$this->render_action(*action*)` ruft das Template für eine Action in diesem Controller auf und gibt es mit Stud.IP-Kontext aus.

```php
function index3_action() {
    $this->daten = array('index3: $this->render_template(\'example/asite/index\')', 'Hier wird nur ein Template aus view gerendert, ohne Layout');
    $this->render_template('example/asite/index');
}
```

`$this->render_template(*pfad_zum_controller*/*name_des_controllers*/*name_des_templates*)` gibt das angebgene Template ohne Stud.IP-Kontext aus.

```php
function nihilist_action()
{
    $this->render_nothing();
}
```

Mit `$this->render_nothing()` sagt man Trails: Bitte kein Template ausgeben.

## Die View

Die folgende Datei ist die view für unser Beispiel.

### Variablenzugriff und Partials

```php
// Ausgeben der im Controller gesetzten Variable
var_dump($daten);
?>

<!-- Ein wenig Text/HTML -->
<b>Huhu</b>

<!-- Ein partial-Template -->
<?= $this->render_partial('example/asite/_feedback'); ?>
```

`var_dump($daten)` gibt das aus, was wir im Controller mittels `$this->daten=*...*` zugewiesen haben. Auf diese Art und Weise gelangen vorbelegt Variablen ins Template.

`$this->render_partial(*template*)` kennt man schon von den normalen Templates. Es erlaubt einem, innerhalb eines Templates ein Subtemplate, ein sogenanntes partial zu inkludieren und anzeigen. Der Inhalt dieser partials wird weiter unten erklärt.\\ Besonders beachten sollte man, das `render_partial` einem einen String zurückliefert, den man erst noch ausgeben muss. In unserem Beispiel geschiet dies mittels `<?=`.

### URLs zu Aktionen in Controllern

```php
<br>
So erhält man einen Pfad zu einem Controller:<br>
$controller->url_for('example/asite/backendwithmessage');<br>
<br>
<a href="<?= $controller->link_for('example/asite/backendwithmessage') ?>"><button>Ausprobieren</button></a>
```

`$controller->url_for('path_to_action')` ist die wohl wichtigste Funktion innerhalb eines Templates. Wie der Name schon andeutet, erhält man hier eine URL zu einer bestimmten Action in einem bestimmten Controller.\\ Dies ist die URL, die man in Formulare, Links, etc. hineinsteckt, wenn man sich innerhalb von Trails bewegen möchte.

Zu beachten ist, dass `$controller->link_for('path_to_action')` an den Stellen genutzt werden sollte, wo ein Link ausgegeben wird und `$controller->url_for('path_to_action')` an den Stellen, wo die URL noch von einer weiteren API verwendet wird.

Ab Stud.IP 4.3 können Links zu Controller-Aktionen auch dadurch erzeugt werden, indem man `$controller->*action*(<parameter>)` aufruft. Der Pfad zum Controller und der Controller selbst müssen somit nicht mehr angegeben. Eine entsprechende URL erhält man durch das Anhängen von `URL` an die aufgerufene Methode: `$controller->*action*URL($parameter);`

`$controller->url_for()` nimmt eine beliebige Anzahl von Parametern an (solange dies keine URL ist) und baut daraus eine korrekte URL zu der Controller-Action zusammen. Insbesondere gelten folgende Regeln:

- Ein Aufruf von `$controller->url_for()` ohne Parameter erzeugt eine URL zu der aktuell aufgerufenen Action.
- Ist der letzte Parameter ein Array, so werden die Werte des Array als GET-Parameter an die URL gehängt.
- Wird ein [SimpleORMap](SimpleORMap)-Objekt übergeben, so wird dieser Parameter durch die Id des Objektes ersetzt.

### Zugriff auf persistente Werte im Template

`app/views/example/asite/_feedback.php`

```php
if ($flash['nachricht']['message']) {
    foreach ($flash['nachricht']['message'] as $nachricht) {
        echo MessageBox::info($nachricht);
    }
}
```

Dies ist das oben bereits genannte partial. Partials haben automatisch Zugriff auf alle Variablen ihres Eltern-Templates (dort wo render_partial gesagt wurde). In diesem speziellen Fall wird auf die automagische Variable `$flash` zugegriffen, die in der `backendwithmessage_action` definiert hatten.

## Trails und SimpleORMap (ab Stud.IP 4.3)

Ab Stud.IP 4.3 sind Trails und SimpleORMap etwas näher miteinander verknüpft. Als Parameter von `link_for()` und `url_for()` bzw. den kurzen `*action*()` bzw. `*action*`URL()@@ Aufrufen können direkt die SimpleORMap-Objekte übergeben werden, wodurch ihre Id als Parameter genutzt wird:

```php
$controller->link_for('controller/edit', $sorm);
// bzw.
$controller->edit($sorm);
```

Die Parameter der Action am Controller können ebenfalls direkt SimpleORMap-Objekte zurückgeben, indem sie einen entsprechenden Typehint erhalten. In dem Fall wird die übergebene Id genutzt, um das Objekt zu laden. Solange der entsprechende Parameter nicht optional ist (`= null`), wird `SimpleORMap::find()` verwendet, um das Objekt zu laden. Ist das Objekt als optional markiert, wird das Objekt mittels `new *SORM*($id);` erzeugt.

Über die Eigenschaft `_autobind` am Controller kann gesteuert werden, ob die derart erzeugten Objekte auch automatisch mittels des Namens des Parameters an den View gebunden werden, damit sie dort auch verfügbar sind.

Ein Beispiel hierfür:

```php
# Controller

    protected $_autobind = true; 

    // ...

    public function edit_action(SORM $sorm)
    {
    }

# View

<label>
    <?= _('Titel') ?>
   <input type="text" name="title" value="<?= htmlReady($sorm->title) ?>">
</label>
```
