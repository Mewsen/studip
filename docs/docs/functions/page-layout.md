---
id: page-layout
title: PageLayout
sidebar_label: PageLayout
---
Über die Klasse PageLayout steht eine API in Stud.IP zur Verfügung, die verschiedene Anpassungen an der HTML-Grundstruktur der Ausgabe ermöglicht. Diess umfaßt einfache Dinge wie das Setzen des Seitentitels, ermöglicht aber auch das Hinzufügen oder Entfernen von HTML-Elementen im `<head>`-Bereich der Seite, um beispielsweise eigene Style-Sheets oder JavaScipt-Dateien einbinden zu können.

Die Anpassung der HTML-Grundstruktur passiert über die neue Klasse `PageLayout`. Dazu bietet die Klasse eine Reihe statischer Methoden, die die verschiedenen Möglichkeiten abdecken.

### Seitentitel

| Funktion | Beschreibung |
| ---- | ---- |
| **setTitle($title)** | Setzt den aktuellen Seitentitel, sowohl für die Anzeige im Browserfester als auch in Stud.IP. |

Beispiel:
```php
PageLayout::setTitle(_('Startseite'));
```

| Funktion | Beschreibung |
| ---- | ---- |
| **getTitle()** | Liefert den aktuellen Seitentitel zurück. |
| **hasTitle()** | Fragt ab, ob für die aktuelle Seite ein Seitentitel gesetzt wurde. |


### Hilfe

| Funktion | Beschreibung |
| ---- | ---- |
| **setHelpKeyword($help_keyword)** | Setzt das Hilfe-Thema für die angezeigte Seite. Dieses wird dann beim Aufruf der Hilfe-Funktion an den Hilfe-Server übermittelt. |


Beispiel:
```php
PageLayout::setHelpKeyword('Basis.Startseite');
```

| Funktion | Beschreibung |
| ---- | ---- |
| **getHelpKeyword()** | Liefert das eingestellte Hilfe-Thema zurück. |


#### Reiternavigation
| Funktion | Beschreibung |
| ---- | ---- |
| **setTabNavigation($path)** | Setzt den Pfad im Navigationsbaum, an dem die Reiternavigation startet. Es werden dann die beiden Ebenen unterhalb des angegebenen Navigationspunkts als Reiter (1. und 2. Ebene) angezeigt. Die Voreinstellung ist das jeweils aktive Element der Hauptnavigation. Ein explizites Setzen ist nur für Navigationskontexte mit Reiteranzeige notwendig, die an anderer Stelle als der Hauptnavigation eingebunden sind (wie z.B. das Impressum). Man kann auch die Anzeige der Reiternavigation ganz ausschalten, wenn man `NULL` als *$path* übergibt. |

Beispiel:
```php
PageLayout::setTabNavigation('/links/siteinfo');
```

| Funktion | Beschreibung |
| ---- | ---- |
| **getTabNavigation()** | Liefert die Reiternavigation zurück. |

### Hinzufügen von Inhalten


| Funktion | Beschreibung |
| ---- | ---- |
| **addStyle($content)** | Fügt eine neues CSS Style Element in den Seitenkopf ein. |

Beispiel:
```php
PageLayout::addStyle('#highlight { background-color: red; }');
```

| Funktion | Beschreibung |
| ---- | ---- |
|**addStylesheet($source, $attributes = [])** | Fügt einen Verweis auf ein Style-Sheet in den Seitenkopf ein. *$source* kann entweder eine komplette URL oder ein Dateiname sein, der relativ zum Assets-Verzeichnis aufgelöst wird. Optional können weitere Attribute für das LINK-Element übergeben werden. |

Beispiel:
```php
PageLayout::addStylesheet('print.css', ['media' => 'print']);
```

| Funktion | Beschreibung |
| ---- | ---- |
|**addScript($source)** | Bindet eine weitere JavaScript-Datei in den Seitenkopf ein. *$source* kann entweder eine komplette URL oder ein Dateiname sein, der relativ zum Assets-Verzeichnis aufgelöst wird. |

Beispiel:
```php
PageLayout::addScript($this->getPluginURL() . '/vote.js');
```

| Funktion | Beschreibung |
| ---- | ---- |
|**addHeadElement($name, $attributes = [], $content = NULL)** | Fügt eine beliebiges HTML-Element in den Seitenkopf ein. *$name*, *$attributes* und *$content* entsprechen den Namen, der Attributliste und dem Inhalt des erzeugten Elements. Ist *$content* `NULL`, so wird das Element nicht abgeschlossen (wie META oder LINK), andernfalls wird automatisch auch ein schließendes Tag hinter dem Inhalt ausgegeben (z.B. bei SCRIPT). |

Beispiel:
```php
PageLayout::addHeadElement('link', [
    'rel' => 'alternate',
    'type' => 'application/rss+xml',
    'title' => 'RSS',
    'href' => $feed_url,
]);
```

| Funktion | Beschreibung |
| ---- | ---- |
|**addBodyElements($html)** | Fügt ein beliebiges HTML-Fragment direkt zu Beginn des BODY in die Seitenausgabe ein. Das ist vor allem in Plugins verwendbar, die Inhalte auf beliebigen Stud.IP-Seiten ausgeben wollen. |

### Entfernen von Inhalten

| Funktion | Beschreibung |
| ---- | ---- |
|**removeStylesheet($source, $attributes = [])** | Entfernt einen Verweis auf ein Style-Sheet wieder aus dem Seitenkopf. *$source* kann wie bei **addStylesheet** entweder eine komplette URL oder ein Dateiname sein, der relativ zum Assets-Verzeichnis aufgelöst wird. |

Beispiel:
```php
PageLayout::removeStylesheet('style.css');
```

| Funktion | Beschreibung |
| ---- | ---- |
|**removeScript($source)** | Entfernt eine eingebundene JavaScript-Datei wieder aus dem Seitenkopf. *$source* kann wie bei **addScript** entweder eine komplette URL oder ein Dateiname sein, der relativ zum Assets-Verzeichnis aufgelöst wird. |
|**removeHeadElement($name, $attributes = [])** | Entfernt alle Elemente mit dem angegebenen Namen und den Attributen wieder aus dem Seitenkopf. |

Beispiel:
```php
PageLayout::removeHeadElement('link', ['rel' => 'stylesheet']);  // remove all style sheets
```

### Darstellung von Meldungen

| Funktion | Beschreibung |
| ---- | ---- |
|**postMessage(MessageBox $message)** | Veranlaßt das System, das angegebene [`MessageBox`-Objekt](MessageBox) bei nächster Gelegenheit anzuzeigen, d.h. bei der nächsten Ausgabe eines Layouts. Die Meldung bleibt so lange gespeichert, bis sie angezeigt wurde, auch über (ggf. mehrere) Redirects hinweg.  |

Für jeden Typen der `MessageBox` gibt es auch eine eigene `post<type>`-Methoden am `PageLayout`-Objekt, wie beispielsweise `PageLayout::postSuccess()` oder `PageLayout::postError()`.

Beispiel:
```php
PageLayout::postMessage(MessageBox::success('Eintrag gelöscht'));
// Äquivalent:
PageLayout::postSuccess('Eintrag gelöscht');
```

| Funktion | Beschreibung |
| ---- | ---- |
|**clearMessages()** | Löscht alle Meldungen, die zur Anzeige hinterlegt und noch nicht ausgegeben wurden. |

### Bestätigen von Aktionen

| Funktion | Beschreibung |
| ---- | ---- |
|**postQuestion($question, $accept_url = "", $decline_url = "")** | Holt eine Bestätigung des Nutzers zu einer bestimmten Aktion ein. Wird die Ausführung der Aktion bestätigt, so wird ein POST-Request auf die angegebene `$accept_url` abgesetzt, im anderen Fall wird die `$decline_url` über GET aufgerufen. Weitere Details finden sich im ersten Abschnitt unter  [Modaler Dialog](ModalerDialog#server). Der Mechanismus funktioniert analog wie `postMessage()`, so dass die Bestätigung bei der nächsten Gelegenheit dargestellt wird. |

Beispiel:
```php
PageLayout::postQuestion(
    'Wollen Sie diese Aktion wirklich ausführen?', 
    URLHelper::getURL('dispatch.php/foo/confimed')
);
```

### Anzeige des Seitenkopfs

| Funktion | Beschreibung |
| ---- | ---- |
|**disableHeader()** | Unterdrückt die Anzeige des Seitenkopfs mit dem Navigationsbereich, z.B. für eine Druckansicht (die sollte aber besser mit einem Print-Style-Sheet gelöst werden) oder ein Popup-Fenster. |

### Setzen des Id-Attributs des Elements `<body>`

| Funktion | Beschreibung |
| ---- | ---- |
|**setBodyElementId($id)** | Setzt die Id des `<body>`-Elements, um über dieses beispielsweise in CSS oder Javascript gezielter Elemente ansprechen zu können.
| **getBodyElementId()** | Liefert die gesetzte Id des `<body>`-Elements zurück. Wurde keine Id gesetzt, wird `false` zurückgeliefert. |

### Ersetzen der Schnellsuche

| Funktion | Beschreibung |
| ---- | ---- |
|**addCustomQuicksearch($html)** | Ersetzt die Schnellsuche (oben rechts) durch beliebiges HTML.
| **hasCustomQuicksearch()** | Fragt ab, ob die Schnellsuche ersetzt wurde.
| **getCustomQuicksearch()** | Liefert den HTML-Code zurück, der die Schnellsuche ersetzen soll. Wurde kein HTML durch `addCustomQuicksearch()` gesetzt, liefert diese Methode `null` zurück. |

## Beispiel

Zum Abschluß noch ein kleines Beispiel aus einem Plugin, das (u.a.) eine eigene CSS-Datei mitbringt:

```php
PageLayout::setTitle('Neueste Aktivitäten');
PageLayout::setHelpKeyword('Plugins.Activities');
PageLayout::addStylesheet($this->getPluginURL() . '/css/activities.css');
```
