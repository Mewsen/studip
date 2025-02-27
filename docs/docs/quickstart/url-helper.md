---
id: url-helper
title: URLHelper
sidebar_label: URLHelper
---

## Benutzung der Klasse URLHelper

Zur Vereinfachung der Umstellung von vorhandenem PHP-Code auf Tabbed-Browsing und um allgemein die Verwendung von Session-Variablen auf ein sinnvolles Maß zurückzuführen, wurde mit dem Lifter001 die Klasse `URLHelper` eingeführt. Neuer Code muß diese Klasse verwenden, um Verweise auf andere Seiten (oder die gleiche Seite) in Stud.IP zu erzeugen. Externe Links und Bereiche von Stud.IP, die bereits eigene Funktionen zur Link-Erzeugung verwenden, sind von der Verwendung des `URLHelper` ausgenommen. Das sind im einzelnen:

* Links auf externe Seiten (z.B. die Hilfe)
* Links auf statische Inhalte (Bilder, Videos usw.)
* Links auf Aktionen in einem Plugin (`PluginEngine::getLink()`)
* Links auf Dokumente im Download-Bereich (`GetDownloadLink()`)
* Links auf Trails-Controller (`Trails_Controller::url_for()`)

Alle anderen Links - insbesondere auch Links aus Plugins auf Seiten im Stud.IP-Kernsystem - müssen so umgestellt werden, daß sie die Klasse `URLHelper` zur Erzeugung der URL verwenden.

### Allgemeines

Hauptzweck dieser Klasse ist es, alle Links auf einer Seite nach Bedarf um zusätzliche URL-Parameter erweitern zu können, ohne immer wieder alle Links anpassen zu müssen. Insbesondere bei durch Hilfsfunktionen oder -klassen erzeugten Links wäre so ein Anpassen teilweise auch überhaupt nicht (sinnvoll) möglich.

Die Grundidee dabei ist relativ simpel: Es gibt eine globale Liste von "automatischen" - d.h. bei der `URLHelper`-Klasse registrierten - Link-Parametern sowie eine Hilfsfunktion *getLink()*, die eine übergebene URL mit diesen registrierten Parametern versieht. Der Aufrufer von `getLink()` muß sich also nicht darum kümmern, welche zusätzlichen Parameter gerade eingebaut werden sollen. In Stud.IP wird dieser Mechanismus zum Beispiel dafür verwendet, um die aktuell gewählte Veranstaltung oder die auf einer Seite eingestellten Ansichtsoptionen bei jedem Klick weiterzureichen, ohne diese serverseitig in der Session speichern zu müssen (was bei Tabbed-Browsing unweigerlich zu Problemen führen würde).

Ein einfaches Beispiel könnte so aussehen:

```php
// $view enthält die gewählte Ansicht
URLHelper::addLinkParam('view', $view)

[...]

switch ($view) {
    case 'show':    // normale Ansicht der Seite
        [...]
    case 'search':  // Suchergebnisse anzeigen
        [...]
    case 'edit':    // Seite bearbeiten
        [...]
}

[...]

// Ausgabe erzeugen (kann auch im Template sein)
echo '<a href="'.URLHelper::getLink(*, array('page' => 25)).'">...</a>';
```

Der aktuelle Inhalt der Variablen `$view` wird dann automatisch zu dem so erzeugten Link hinzugefügt und man erhält so etwas wie:

```php
<a href="?page=25&amp;view=edit">
```

Natürlich kann jeder Link auch eigene Parameter enthalten, die spezifisch für diesen Link sind. Diese würden dann direkt im Aufruf von `getLink()` angegeben und nicht global als Parameter registriert. Lokal im Aufruf angegebene Parameter haben dabei Vorrang vor den global registrierten, d.h. man kann bei bei Bedarf auch für einzelne Links registrierte Parameter mit anderen Werten versehen oder ganz ausblenden (Parameter beim Aufruf auf `NULL` setzen).

### Methoden der Klasse `URLHelper`

An dieser Stelle sind die wichtigsten Operation der Klasse `URLHelper` gesammelt und dokumentiert. Es handelt sich dabei jeweils um *Klassenmethoden*, d.h. der Aufruf erfolgt über `URLHelper::`*Name*.

* **addLinkParam($name, $value)**

  Registriert einen Link-Parameter mit dem angegeben Namen und Wert. Sollte es bereits einen Parameter gleichen Namens geben, wird der alte Wert durch den neuen ersetzt. Eine ggf. vorhandene Bindung (siehe `bindLinkParam()`) wird aufgehoben.

* **bindLinkParam($name, &$var)**

  Bindet einen Link-Parameter an die angegebene PHP-Variable. Sollte es bereits einen Parameter gleichen Namens geben, wird der alte Wert durch die Bindung ersetzt. Der konkrete Wert dieses Parameters wird im Unterschied zu `addLinkParam()` bei dieser Operation nicht direkt gesetzt, sondern erst *beim Aufruf* von `getLink()` oder `getURL()` durch Auslesen der angegebenen Variable ermittelt. Ändert man also nach dem Aufruf von `bindLinkParam()` den Wert dieser Variable, wird immer der gerade aktuelle Wert verwendet.

  Außerdem wird die angegebene Variable durch diesen Aufruf mit dem Wert des URL-Parameters in der REQUEST-Umgebung der Seite initialisiert. Diese Funktion ist vor allem dazu nützlich, bisher in Session-Variablen gespeicherten Zustand in URL-Parameter auzulagern.

* **removeLinkParam($name)**

  Entfernt einen zuvor registrierten Link-Parameter wieder. War kein Parameter dieses Namens registriert, passiert nichts.

* **getLinkParams()**

  Liefert eine Liste (ein Array mit Name/Wert-Paaren) aller derzeit registrierten Parameter. Damit könnte man diese z.B. als *hidden*-Felder in eine FORM einbauen, um Längenbeschränkungen der URL-Parameter aus dem Weg zu gehen.

* **getLink($url = *, $params = NULL)**

  Ergänzt die übergebene URL um alle aktuell registrierten Parameter. Im Falle von an Variablen gebundenen Parametern wird der zum Zeitpunkt des Aufrufs aktuelle Wert der jeweiligen Variable eingesetzt. Wird der zweite (optionale) Parameter übergeben, können weitere Parameter gesetzt werden, deren Werte ebenfalls zur URL hinzugefügt werden.

  Im Falle von gleichnamigen Parametern gilt: Einträge im `$params`-Array haben Vorrang vor Parametern in der `$url`. Parameter aus der übergebenen `$url` haben Vorrang vor registrierten Parametern. Möchte man einen registrierten Parameter komplett aus der URL ausblenden, so muß man diesem im `$params`-Array einen Wert von `NULL` geben.

  Das Resultat dieser Funktion ist eine *entity-kodierte URL*, d.h. es kann direkt in Attribute im HTML eingesetzt werden (*action* einer FORM, *href* eines A-Elements). Braucht man die unkodierte URL, sollte `getURL()` verwendet werden.

* **getURL($url = *, $params = NULL)**

  Diese Funktion arbeitet genau wie `getLink()`, liefert aber keinen entity-kodierten Wert zurück, sondern die unkodierte URL. Diese kann dann z.B. für Aufrufe über JavaScript verwendet werden.

### Probleme durch URL-Parameter

Durch die Verwendung des `URLHelper` können auch neue Proleme auftreten, die bei der Nutzung von Session-Variablen nicht oder nicht im dem Maße bestehen. Es eignen sich auch nicht alle Arten von Session-Daten zur Übergabe über die URL, so daß man im Einzelfall abwägen muß, ob eine Umstellung sinnvoll ist. Dabei sollten die folgenden Punkte berücksichtigt werden:

* Längenbeschränkung von URLs

  Es gibt eine browser-abhängig maximale Längenbeschränkung von URLs in der Größenordnung von einigen tausend Zeichen (typischerweise 2048 Zeichen beim Internet Explorer, 8192 Zeichen bei Firefox). Längere URLs werden abgeschnitten und führen damit zum Verlust von Informationen. Möchte man also einen sehr komplexen Zustand über die URL übergeben - z.B. eine beliebig große Menge an aufgeklappten Knoten in einer Baumansicht - sollte man dies ggf. besser serverseitig speichern und nur einen Verweis auf eine gespeicherte Konfiguration in der URL hinterlegen.
  
* Manipulation von URL-Parametern durch den Nutzer

  Man sollte sich grundsätzlich der Tatsache bewußt sein, daß URL-Parameter - im Gegensatz zu Session-Daten - vom Nutzer nach belieben Verändert werden können, man darf also niemals darauf vertrauen, daß diese vom Nutzer nicht manipuliert werden. Würde man also bisher in der Session gespeicherte Berechtigungen eines Nutzers in die URL verschieben, so müßte man diese bei jedem Seitenaufruf immer neu prüfen.

* Namenskollisionen bei Parametern verschiedener Seiten

  Der `URLHelper` unterscheidet beim Erzeugen der URLs nicht, auf welche Seite ein Link verweist. Es ist auch nicht möglich, Parameter nur für bestimmte Ziele zu registrieren (da in Stud.IP in zunehmendem Maße Dispatcher eingesetzt werden, würde das auch nicht viel bringen). Daher sollte man darauf achten, Namenskollisionen bei über den URLHelper verwalteten Parametern verschiedener Seiten zu vermeiden, entweder durch Verwendung eindeutiger Präfixe (*wiki_search*) oder durch Abgleich mit der Liste bereits verwendeter Namen.

### Beispiele

Hier sollten einige kleine Bespiele für die Verwendung des `URLHelper` aus dem praktischen Einsatz in Stud.IP gesammelt werden. Leider gibt es hier noch nicht viel...

```php
$link = URLHelper::getLink('wiki.php', array('keyword' => $keyword));
echo '<a href="'.$link.'">'.htmlReady($keyword).'</a>';
```

### URLHelper für Javascript

Unabhängig hiervon gibt es auch einen [URLHelper für Javascript](HowToJavascript), der eine ähnliche API aufweist und auch ähnliches tut. Jedoch sind diese beiden URLHelper nicht aufeinander abgestimmt und völlig unabhängig voneinander.
