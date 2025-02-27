---
id: studip-format
title: StudipFormat
sidebar_label: StudipFormat
---

:::danger
Die Funktion ist veraltet und sollte nicht mehr verwendet werden
:::

Zu der Stud.IP-Version 2.3 wurde die Formatierungsengine umgestellt auf einen eigenen Parser, der sich in den Klassen `StudipFormat` und `TextFormat` verbirgt. TextFormat ist dabei der grundsätzliche Parser und StudipFormat ein spezieller, der mit den typischen Studip-Syntax angereichert ist.

Die Benutzung ist grundsätzlich einfach. Man instanziiert den Parser und schickt seinen zu formatierenden Text durch die Methode `format`. Etwa so:

```php
$markup = new StudipFormat();
echo $markup->format("Mein %%cooler%% formatierter Text ist **cool**.");
```


Man kann auch einen eigenen Parser definieren mit der Klasse `TextFormat`. Ein nacktes Objekt davon macht allerdings erst einmal gar nichts denn `TextFormat` bringt von sich aus keine Regeln mit. Eine Regel definiert man über den Konstruktor oder danach über die Methode `addMarkup`. Etwa so:

```php
function bbbold($text) {
    return "<b>".$text."</b>";
}
function bbitalic($text) {
    return "<i>".$text."</i>";
}
$bbmarkup = new TextFormat(array(
    'bold-text' => array(
         'start' => "\[b\]",
         'end' => "\[\/b\]",
         'callback' => "bbbold"
     )
));
$bbmarkup->addMarkup(
    'italic-text', 
    "\[i\]", 
    "\[\/i\]", 
    "bbitalic"
);
```


Innerhalb von Stud.IP werden immer die Funktionen `formatReady` `linkReady` und `wikiReady` verwendet, um Text zu formatieren. Mit dem neuen Parser kann man jetzt auch Plugins bauen, die dieses Markup jeweils erweitern bzw. verändern. `htmlReady` verwendet intern einfach `$markup->format($text)`, um den Text zu formatieren, die wiederum eine Reihe von Formatierungsregeln voreingestellt hat. Diese Formatierungsregeln stehen in der privaten Variable `StudipFormat->studip_rules`, die sich mit den statischen Methoden `getStudipMarkup($name)`, `addStudipMarkup($name, $start, $end, $callback, $before = null)` und `removeStudipMarkup($name)` manipulieren lässt. Am interessantesten ist da sicherlich die Methode `addStudipMarkup`.

Man könnte zum Beispiel ein Plugin bauen, das das Markup für Stud.IP erweitert. Ich stelle mal ein kleines Beispielplugin vor, das OpenstreetMaps überall einbinden, wo der Nutzer sowas schreibt wie `[osmap]latitude;longitude;zoomfactor[/osmap]` Das ist simpel und kann man immer gut gebrauchen.

```php
class OpenstreetmapEmbedder extends StudIPPlugin implements SystemPlugin {
    
    public function __construct()
    {
        parent::__construct();
        StudipFormat::addStudipMarkup("osmap", "\[osmap\]", "\[\/osmap\]", "OpenstreetmapEmbedder::createMap");
        PageLayout::addHeadElement('script', array('type' => "text/javascript", 'src' => $this->getPluginURL()."/assets/khtml_all.js"), *);
    }
    
    public static function createMap($markup, $matches, $adress)
    {
        $id = "map_".uniqid();
        list($latitude, $longitude, $zoom) = explode(";", $adress);
        $output = sprintf(
                '<style>#%s > div:not(:first-child) {display: none;} </style>'.
                '<div id="%s" style="width: 400px; height: 300px; border: 1px solid black;"></div>' .
                '<script>
                jQuery(function () {
                    map=new khtml.maplib.base.Map(document.getElementById("%s"));
                    map.centerAndZoom(new khtml.maplib.LatLng(%s,%s),%s);
                    var zoombar=new khtml.maplib.ui.Zoombar();
                    map.addOverlay(zoombar);
                });
                </script>',
            $id,
            $id,
            $id,
            (double) $latitude,
            (double) $longitude,
            (int) $zoom
        );
        return str_replace("\n", "", $output);
    }
}

```

Wenn man mal von den Eigenheiten der zugrundeliegenden Javascript-Engine zur Darstellung der Karte absieht, ist das Plugin ganz simpel: Im Konstruktor wird das Markup über die Methode `StudipFormat::addStudipMarkup` an StudipFormat angegeben. Damit existiert das Markup schon mal. Der erste Parameter ist einfach ein Name, der frei gewählt werden kann. Der zweite Parameter ist der Startausdruck als regulärer Ausdruck. Hier könnten auch benannte Ausdrücke wie `(.*?)` drin vorkommen, die nachher per `$matches` Variable an die ausführende Markup-Funktion übergeben werden. Aber für unser Beispiel ist das egal, da wir nur einen Parameter brauchen.
Das dritte Argument ist der Endausdruck. Alles zwischen Startausdruck und Endausdruck bekommt die Funktion später als `$contents` übergeben.
Das vierte Argument ist der Name der Funktion, die später das Markup ausführen soll. Statische Funktionen eignen sich dafür am besten.
Es könnte noch ein fünftes Argument geben, das `$before` heißt und angibt, vor welchen anderen Direktiven dieses Markup ausgeführt wird. Es könnte ja sein, dass zwei Markups Doppeldeutigkeiten hervorrufen. Und da 'gewinnt' immer das Markup, das weiter vorne in der Reihenfolge steht. Gibt man `$before = "links"` mit, so wird das eigene Markup immer der Linksyntax vorgezogen.

In der Methode `OpenstreetmapEmbedder::createMap`, der Methode, die das Markup ausführt, bekommen wir drei Parameter, wovon der dritte nur existiert, wenn ein Endausdruck definiert wurde. Der erste Parameter ist immer ein `TextFormat`-Objekt, das gerade das Markup koordiniert. In den meisten fällen, braucht man dieses Objekt nicht zu beachten. `$matches` ist ein Array mit allen Parametern, die man aus dem regulären Ausdruck gewinnt. `$matches[0]` ist stets der ganze String, `$matches[1]` wäre schon der erste benannte Parameter im regulären Ausdruck wie `(.*?)`. `$contents`, das dritte Argument und bei unserem Beispiel gleich `$adress` genannt, gibt alles zwischen Startasudruck und Endausdruck an.
Die Methode `createMap` muss jetzt nur noch etwas zurückgeben, das praktisch für `$matches[0]` eingesetzt wird, also für den betroffenen String. Der hier angefasste String wird überdies nie mehr von anderem Markup beeinflusst.

**WARNUNG** für Pluginentwickler: Seid euch bewusst, dass ihr zwar schnell ein Plugin bauen könnt, das die Leute vermutlich auch schnell verstehen. Aber die Syntax der Formatierung, die ihr wählt, ist von Anfang an für alle Zeiten fest, denn die Forumsbeiträge, die eure neue Syntax verwenden, werden auf Ewigkeiten die Syntax behalten, die ihr anfangs mal verwendet habt. Überlegt euch daher die Syntax gut, geht sparsam mit Formatierungsmöglichkeiten um, von denen absehbar ist, dass sie eines Tages veraltet sein werden (wie SWF-Datei-Integration), oder wo der Datenschutz mindestens strittig ist (wie eine Google-Maps-Integration).
