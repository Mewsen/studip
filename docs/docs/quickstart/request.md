---
id: request
title: Request
sidebar_label: Request
---


## Benutzung der Klasse Request

### Allgemeines

Die Klasse `Request` soll den Zugriff auf Request-Parameter vereinheitlichen und vereinfachen. Sie ermöglicht das typsichere Abfragen von Werten (z.B. als Zahl oder Optionswert) und liefert unabhängig von den Einstellungen von `register_globals` und `magic_quotes_gpc` immer gleiche Ergebnisse. Außerdem gibt es eine Reihe von Hilfsfunktionen zur Abfrage von Request-Eigenschaften wie der aktuellen URL, des Server-Namens oder der Request-Methode. Im Unterschied zu `$_REQUEST` wertet die Klasse nur GET- und POST-Parameter aus, aber keine Cookies.

Für den Zugriff auf die Parameter gibt es generell zwei Möglichkeiten: Direkten Zugriff über statische Methoden der Klasse (wie `Request::get($param)`) und alternativ Array-Zugriff über eine Instanz der Klasse (siehe `Request::getInstance()`). Letzteres braucht man vor allem dann, wenn man die Liste aller Parameter durchlaufen oder an eine Funktion übergeben möchte.

### Verwendung der Klasse `Request`

#### Abfragen von Request-Parametern

Parameter aus dem aktuellen Request (d.h. GET- und POST-Parameter) sollten in Stud.IP über die Methoden der `Request`-Klasse abgefragt werden. Diese kann eine Validierung der Parameter vornehmen - z.B. daß es sich bei dem übergebenen Wert tatsächlich um eine Zahl handelt - und stellt sicher, daß die Ergebnisse unabhängig von PHP-Einstellungen wie `magic_quotes_gpc` sind. Für verschiedene Typen gibt es jeweils spezifische Abfragefunktionen.

Die folgende Methoden dienen zum typsicheren Zugriff auf Request-Parameter, die skalare Werte enthalten. Falls es keinen Parameter mit dem angegeben Namen gibt (oder der Parameter im Aufruf den falschen Typ hat), wird der Wert `NULL` zurückgeliefert bzw. der übergebene Vorgabewert, sofern dieser angegeben wurde:

| Funktion | Beschreibung |
| ---- | ---- |
| `get($param, $default = NULL)` | Liefert den Wert eines Parameters als String. **Vorsicht**: Der Wert wird so geliefert, wie vom Nutzer angegeben.| 
| `username($param, $default = NULL)`| Liefert den Wert eines Parameters als Nutzerkennung. Eine Nutzerkennung besteht nur aus Buchstaben und Ziffern sowie den Zeichen "_", "@", "." und "-". | 
| `option($param, $default = NULL)`| Liefert den Wert eines Parameters als Optionswert. Ein Optionswert besteht nur aus Buchstaben, Ziffern und Unterstrichen. | 
| `int($param, $default = NULL)`| Liefert den Wert eines Parameters als ganzzahligen Wert. | 
| `float($param, $default = NULL)` | Liefert den Wert eines Parameters als Gleitkommazahl. | 

Einige Beispiele:

```php
if (!Request::submitted('reset')) {
    $title   = Request::get('title');         // title can contain any characters
    $inst_id = Request::option('inst_id');    // IDs are always alphanumeric
    $sem_id  = Request::option('sem_id');
    $page    = Request::int('page', 1);       // page number is an integer
}

$days     = Request::int('days', 14);         // default to 14 days
$category = Request::option('category');      // like "wiki", "forum" or "news"
$enable   = Request::int('enable');
```

Analog dazu gibt es auch Methoden zum typsicheren Zugriff auf Request-Parameter, die ein Array als Wert enthalten (z.B. eine Liste von Nutzerkennungen). Die Behandlung der einzelnen Werte passiert ganz analog zu den entsprechenden Methoden für skalare Werte. Falls es keinen Parameter mit dem angegeben Namen gibt, wird hier jeweils ein leeres Array zurückgeliefert:

* `getArray($param)`
* `usernameArray($param)`
* `optionArray($param)`
* `intArray($param)`
* `floatArray($param)`

Beispiel:

```php
$institutes = Request::optionArray('institutes');
$is_enabled = Request::intArray('is_enabled');
```

#### Auflisten von Request-Parametern

Möchte man die komplette Liste der Request-Parameter durchlaufen, so kann man dies über eine Instanz der `Request`-Klasse tun. Diese ist dann genau so zu verwenden wie das Array `$_REQUEST`, sie verhält sich allerdings immer so, als wäre *magic_quotes_gpc* ausgeschaltet:

* `getInstance()`

  Liefert die Singleton-Instanz der Request-Klasse. Über dieses Objekt kann man direkt mit Array-Notation auf die aktuellen Request-Parameter zugreifen oder mittels einer `foreach`-Schleife diese iterieren. Die oben aufgelisteten typsicheren Zugriffsmethoden können auch über das Objekt aufgerufen werden.

Beispiel:

```php
$request = Request::getInstance();

$user = $request['user'];     // alternativ: $request->get('user')
$mode = $request['mode'];     // alternativ: $request->option('mode')

foreach ($request as $key => $value) {
    [...]
}
```

#### Auswerten von Schaltflächen in Formularen

Für die Auswertung, ob eine bestimmte Schaltfläche in einem Formular angeklickt wurde, gibt es eine spezielle Funktion (der Name des tatsächlich übergebenen Parameters ist nicht in jedem Browser identisch). Hierbei ist der Name der Schaltfläche anzugeben:

* `submitted($param)`

  Testet, ob ein Formular-Button (INPUT bzw. BUTTON) mit dem übergebenen Namen angeklickt wurde.

Beispiel:

```php
if (Request::submitted('add_user')) {
    $cmd = 'add_user';
}
```

#### Auswertung von Request-Eigenschaften

Es gibt noch eine Reihe von weiteren Hilfsfunktionen, um allgemeine Eigenschaften des Request abzufragen. 
Dazu gehört die aktuelle URL sowie der Name des Servers oder die Request-Methode (typischerweise `GET` oder `POST). 
Die wichtigsten davon sind hier kurz aufgelistet:

* `url()`

  Liefert die komplette URL der aktuellen Seite.

* `method()`

  Liefert die zum Aufruf verwendete Request-Methode (`GET`, `POST`, `HEAD` o.ä.).

* `isAjax()`

  Fragt ab, ob es sich um einen Ajax-Request (d.h. `XmlHttpRequest` von jQuery oder prototype) handelt.

Beispiel:

```php
if (Request::isAjax()) {
    $this->set_layout(null);
}
```

Weitere Details befinden sich in der zugehörigen [API-Dokumentation](http://hilfe.studip.de/api/class_request.html).
