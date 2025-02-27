---
title: JavaScript
---

In Stud.IP wird mit der Zeit mehr und mehr JavaScript für erweiterte und vereinfachte Bedienung oder auch einfach für besonders schöne Effekte benutzt.

## Code-Konventionen für JavaScript

Als Programmierer hat man sich im Grunde nur daran zu richten, dass der globale Namespace einigermaßen sauber gehalten wird (also globale Variablen müssen vermieden werden) und dass die Code-Konventionen eingehalten werden, die im Lifters005 zusammen gefasst sind. Die Code-Konventionen sind von [Douglas Crockfords "Code Conventions for the JavaScript Programming Language"](http://javascript.crockford.com/code.html) komplett übernommen.

Was den Namespace angeht, so sollen alle speziellen Stud.IP Funktionen unterhalb des STUDIP-Objektes angehängt werden. Programmiere ich also einige Funktionen für die News, fange ich an mit:

```js
STUDIP.News = {
    openclose: function (id) {},
    open: function (id) {},
    close: function (id) {}
}
```

und fülle diese Methoden dann mit Leben. An diese Konvention sollten sich auch Plugin-Programmierer halten, wobei die noch speziell darauf zu achten haben, dass ihre Methodennamen auch tatsächlich eindeutig sind. Implementieren zwei unterschiedliche Plugins eine Methode `STUDIP.go`, so wird mindestens eines der beiden Plugins weinen. Sinnvoll ist es da, den eindeutigen Klassennamen des Plugins zwischen zu schieben, entweder über `STUDIP.pluginclassname.go` oder eventuell auch einfach nur `pluginclassname.go`.

## Eigene Stud.IP Bibliothek

Für alle JavaScript-Programmierer wird von Interesse sein, dass einige Funktionen schon in Stud.IP implementiert sind, die auch an anderer Stelle nützlich sein könnten. Der Vorteil liegt auf der Hand: der Code bleibt klein und kann später besser um Funktionalitäten erweitert werden.

### Vorhandene Methoden


|Methode |Anwendbar auf |Verhalten |Zu beachten |
---- | ---- | ---- | ---- |
| $.showAjaxNotification(%%position=left%%); | Alle Elemente | Dem Element wird ein AJAX-Indikator vorangestellt. | Der Indikator kann mittels des Parameters %%position="right"%% auch hinter dem Element positioniert werden. Der Indikator wird absolut positioniert, was zu Problemen bei Veränderungen von Elementen in der Umgebung des eigentlichen Elements führen kann. |
| $.hideAjaxNotification(); | Alle Elemente | Der dem Element zugehörende AJAX-Indikator wird entfernt, sofern vorhanden. | - |

### Vorhandene Verhaltsmuster über CSS-Klassen


|Klasse |Anwendbar auf |Verhalten |Zu beachten |
| ---- | ---- | ---- | ---- |
| .add_toolbar | <textarea /> | Dem Element wird eine Menüleiste mit vereinfachten Formatiermöglichkeiten vorangestellt. | Auf diese Art und Weise kann nur das Standard-Buttonset von Stud.IP verwendet werden. |
| .load_via_ajax | <a /> | Eine angegebene URL (entweder *metadata.url* oder die URL des gegebenen Links) wird via AJAX in ein Element (entweder *metadata.target* oder das auf das gegebene Elemente folgende Element) geladen. | Über *metadata.indicator* kann via CSS-Regel das Element angegeben werden, welches den AJAX-Indikator erhält. Über *metadata* können weitere Parameter für den Aufruf der URL angegeben werden. |
| .load_via_ajax.internal_message | <a /> | Spezialfall für interne Nachrichten. | Die Parameter werden an die Gegebenheiten in *lib/sms_func.inc.php* angepasst. |
| .resizable | <textarea /> | Das Element kann durch einen Schieber am unteren Rand in der Höhe verändert werden. | - |

#### AJAX-Anfragen

AJAX-Anfragen sollten über jQuery mit den Methoden `[.load](http://api.jquery.com/load/)` `[.get](http://api.jquery.com/get/)` oder `[.ajax](http://api.jquery.com/jQuery.ajax/)` durchgeführt werden. Die meisten AJAX-Aufrufe haben einen AJAX-Indicator, der dem Nutzer mitteilt, dass gerade etwas geladen wird. Falls dieser Indicator ungebeten sein sollte, kann man die AJAX-Methode einbetten wie folgt: 

```JavaScript
STUDIP.ajax_indicator = false;
$('#dynamischer_bereich').load(url);
STUDIP.ajax_indicator = true;
```

#### URLHelper in JavaScript

Das Objekt `STUDIP.URLHelper` bietet für JavaScript ähnliche Funktionalität wie der [URLHelper in PHP](URLHelper). Dennoch darf man nicht vergessen, dass beide URLHelper gänzlich unabhängig voneinander sind und nicht miteinander kommunizieren können. Wozu braucht man dann einen URLHelper in JavaScript? Zum Beispiel, um:

* Einen Link zu generieren, wo eine JavaScript-Datei sonst nicht wüsste, wie die Adresse des Servers ist. Schreiben Sie also `STUDIP.URLHelper.getURL("about.php")`, um einen URI-kodierten Pfad zu http://www.studip......de/about.php zu bekommen, oder `STUDIP.URLHelper.getURL("about.php")`, um das selbe als nicht URI-kodiert zu erhalten.
* Variablen zu einer beliebigen URL hinzuzufügen, ohne sich Gedanken darüber zu machen, welche Variablen schon in der URL sind und welche nicht. Also wird aus `STUDIP.URLHelper.getURL(alte_url, {hallo: "welt"})` ein http:.../about.php?hallo=welt, egal ob alte_url schon einen Wert für hallo angegeben hatte oder auch nicht. Auch muss man sich dann keine Gedanken darüber machen, ob man den Parameter mit "?" oder einem "&" anhängt. Beachten Sie zudem, dass Parameter in der alte_url weniger Priorität haben als Parameter im zweiten Argument.
* Variablen dauerhaft (also solange die HTML-Seite in Benutzung ist) zu generierten URLs hinzuzufügen. Das geht mit der Methode `STUDIP.URLHelper.addLinkParam("hallo", "welt")`. Nach diesem Aufruf wird jedes `STUDIP.URLHelper.getURL("about.php")` zum Beispiel http://..../about.php?hallo=welt zurück geben. Mit dieser Methode werden auch Parameter aus der abgegebenen URL überschrieben, also `STUDIP.URLHelper.getURL("about.php?hallo=ich")` würde trotzdem Welt als Inhalt des Parameters hallo wider geben. Nicht so jedoch mit `STUDIP.URLHelper.getURL("about.php", {hallo: "ich"})`, wobei der zweite Parameter wieder einmal Priorität hat.
* Variablen dauerhaft von generierten URLs abzuziehen, wenn sie denn vorher drin gewesen sein sollten. Dazu gibt man analog zu oben addLinkParam("hallo", "") an und der Parameter hallo wird stets als zwingend leer angesehen und auch aus bestehenden URLs gestrichen, wenn vorher vorhanden.
* Alle Hyperlinks eines Abschnittes des Dokumentes mit aktuellem Parameter zu versehen. Nach einem oder mehreren addLinkParam-Aufrufen könnte man `STUDIP.URLHelper.updateAllLinks("#container");` aufrufen, wodurch alle Links innerhalb des CSS-Selektors "#container" einmal durch die STUDIP.URLHelper.getURL(...) Methode ersetzt werden und dadurch aktuelle Parameter bekommen. Gibt man keinen Selektor an, werden die Links des ganzen Dokuments ersetzt. 

[#caching](#caching)
#### Caching in JavaScript

Stud.IP bietet seit Version 3.2 eine Abstraktion des Caching in JavaScript über `STUDIP.Cache` an. Man erhält eine Instanz mittels `STUDIP.Cache.getInstance()` mit dem optionalen Parameter `prefix`. Dieser Präfix sollte nach Möglichkeit immer genutzt und sinnvoll gewählt werden, da er Konflikte beim Zugriff auf Cachedaten verhindern kann. Ist der Präfix gesetzt, so ist gewährleistet, dass der Cache nur auf Daten "unterhalb" dieses Präfixes zugreifen kann, ohne einen entsprechenden Mechanismus bei jeder einzelnen Cacheoperation angeben zu müssen:

```JavaScript
let cache0 = STUDIP.Cache.getInstance('foo.');
let cache1 = STUDIP.Cache.getInstance();

cache0.set('test', 42);

console.log(cache0.get('test'), cache1.get('foo.test'));

cache1.set('foo.test', 23);

console.log(cache0.get('test'), cache1.get('foo.test'));
```

Der Cache unterstützt folgende Operationen:

| Funktion | Beschreibung |
| ---- | ---- |
|`has(key)`|Fragt ab, ob der Cache einen Wert für den Schlüssel hat |
|`get(key, setter, expires)`|Holt einen Wert für den Schlüssel ab. Ist kein Wert gesetzt und `setter` ist definiert, so wird der Wert erzeugt und mit der angegebenen Laufzeit gespeichert.|
|`set(key, value, expires)`|Speichert einen Wert für den angegebenen Schlüssel mit der angegebenen Laufeit (`expires = false` bedeutet, dass der Wert gelöscht wird sobald das Browserfenster geschlossen wird).|
|`remove(key)`|Löscht den gespeicherten Wert für den angegebenen Schlüssel.|
|`prune()`|Löscht alle gespeicherten Daten. |

**Zu beachten**: Sollen Daten nur für einen Nutzer oder eine gewisse Session gespeichert werden, so sollte zwingend ein geeigneter Präfix genutzt werden, der die entsprechenden Daten (gehasht) enthält. Der Cache in JavaScript weiß nichts von den Gegebenheiten auf PHP-Seite.

## Das jQuery-Framework in Stud.IP

Zur Zeit (Stud.IP 4.1) wird jQuery 3.2.1 und jQuery-UI 1.12.1 verwendet. Von jQuery-UI sind alle Funktionen in Stud.IP geladen.


### Weitere verwendete JS-Bibliotheken

Eine Übersicht der aktuell verwendeten JS-Bibiliotheken findet sich in `package.json`. 

#### jQuery-Plugins

##### TableSorter [(Link)](http://tablesorter.com)
* [jquery.tablesorter.js](https://develop.studip.de/trac/browser/trunk/public/assets/javascripts/jquery.tablesorter.js?rev=19220)
* [jquery.tablesorter.min.js](https://develop.studip.de/trac/browser/trunk/public/assets/javascripts/jquery.tablesorter.min.js?rev=19220) 
* [jquery.tablesorter.pager.js](https://develop.studip.de/trac/browser/trunk/public/assets/javascripts/jquery.tablesorter.pager.js?rev=19220) TableSorter-Pagination-Plugin

Dieses Plugin stellt ähnliche Möglichkeiten wie das vormalige TableKit-Plugin (s.o.) zur Verfügung und ermöglicht das flexible client-seitige Sortieren von Tabellen.

#### jQuery UI Multiselect [(Link)](http://www.quasipartikel.at/multiselect/)
* [ui.multiselect.js](https://develop.studip.de/trac/browser/trunk/public/assets/javascripts/ui.multiselect.js?rev=19220)

Das jQuery-UI-Multiselect-Plugin wandelt "multiple select inputs" in sexier aussehende Äquivalente um. Das Plugin wurde in den folgenden Changesets gepatchet:
* [https://develop.studip.de/trac/changeset/18594/trunk/public/assets/javascripts/ui.multiselect.js r18594](https://develop.studip.de/trac/changeset/18594/trunk/public/assets/javascripts/ui.multiselect.js r18594)
* [https://develop.studip.de/trac/changeset/18635/trunk/public/assets/javascripts/ui.multiselect.js r18635](https://develop.studip.de/trac/changeset/18635/trunk/public/assets/javascripts/ui.multiselect.js r18635)

#### JS-L10n [(Link)](https://github.com/eligrey/l10n.js/)
* [l10n.js](https://develop.studip.de/trac/browser/trunk/public/assets/javascripts/l10n.js?rev=19220)

Diese Bibliothek wird verwendet, um lokalisierte Strings in JS verwenden zu können. Weiteres ist bereits im [Wiki](quickstart/Internationalisierung) dokumentiert.

## FAQ

#### Wie modularisiere ich meinen JavaScript-Code?
In Stud.IP darf Code nach ECMAScript2015 und besser geschrieben werden, der dann zu ES5 kompiliert wird. Wenn ich meinen Code also auf mehrere Dateien verteilen möchte, verwende ich einfach das "import"-Statement, ein Sprachfeature von JavaScript, das gut auf MDN beschrieben wird: https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Statements/import

Dazu lege ich also eine zweite Datei an und trage dann in meiner ersten Datei ein "import"-Statement mit dem relativen Pfad zu dieser Datei ein.

#### Wie binde ich npm-Bibliotheken ein?
Das kann man gut am Beispiel von "lodash" zeigen: Die lodash-Bibliothek wird via npm installiert: "npm i --save-dev lodash". Dann trage ich in meine Datei ein:
```JavaScript
import lodash from "lodash"
```

#### Was muss ich als Modulname bei `import` hinschreiben?

Die exakten Details finden sich hier: https://webpack.js.org/concepts/module-resolution/#resolving-rules-in-webpack

Hier eine kurze Zusammenfassung:

Verweise ich auf eigenen Code, schreibe ich einen relativen Pfad auf:

```JavaScript
import '../src/file1';
import './file2';
```

Möchte ich eine Bibliothek importieren, verwende ich den Modulnamen der Bibliothek:

```JavaScript
import lodash from 'lodash';
import 'module/lib/file';
```


#### Ich möchte Code/Assets nur bei Bedarf laden. Wo muss ich die eintragen und wie lade ich die?

In ECMAScript wird das dynamische Nachladen gerade standardisiert (Anfang 2019 ist diese Feature gerade in Stage 3) Der gegenwärtige Stand ist in https://github.com/tc39/proposal-dynamic-import dokumentiert.

Dennoch kann man dank webpack jetzt schon damit arbeiten. Dazu lade ich mittels des funktionsartigen "import()"-Ausdrucks einfach eine Datei. 

Hier ein Beispiel:

```JavaScript
import('/modules/my-module.js')
  .then((module) => {
    // Do something with the module.
  }).catch(error => 'An error occurred while loading the module');
```

Ausführliche Doku dazu gibt es hier:

* https://webpack.js.org/guides/code-splitting/#dynamic-imports
* https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Statements/import#Dynamic_Imports

#### Ich habe ein Plugin, das seine Komponenten auch gerne packen würde. Wie sage ich es dem Stud.IP-Kernsystem bzw. dessen Webpack?

Plugins kümmern sich um ihre eigenen Angelegenheiten. Wenn ein Plugin-Entwickler webpack verwenden möchte, tut er das für sein Plugin selbst.

#### Wie kann ich die gleiche Funktion zum Document Ready und beim Dialog Update eine Funktion ausführen?

Ab Version Stud.IP 4.4 gibt es dafür den Event `studip-ready`, der die Events `ready` und `dialog-update` zusammenfasst und in beiden Fällen getriggert wird. Vor Version Stud.IP 4.4 muss die gleiche Funktion von Hand an die beiden Events gebunden werden.

#### Welches ist der Worttrenner für JavaScript-Dateien?

JavaScript-Dateien sollen im Kebab-Case-Stil abgelegt werden (also `eins-zwei.js` und nicht `eins_zwei.js`). 

