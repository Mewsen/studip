---
title: API-Dokumentation
---

# Offizielle API-Dokumentation

Die offizielle API-Dokumentation kann unter der URL:

https://docs.gitlab.studip.de/api

eingesehen werden. Verwendet wird dafür das Werkzeug [doxygen](https://www.doxygen.nl/index.html), was (anders als phpdoc) schnell und zuverlässig aus dem Quellcode die API-Dokumentation erzeugt. In der dortigen Sidebar befinden sich folgende Auflistungen:


| Wert | Beschreibung |
| ---- | ---- |
|Data Structures|vorhandene Klassen |
|Class Hierarchy|Klassen hierarhisch nach Inheritance gruppiert |
|Data Fields|Klassen- und Instanzvariablen |
|File List|alle Dateien |
|Directory Hierarchy|aufgeklappte Verzeichnisstrukturen |
|Examples|Liste aller in den Kommentaren enthaltenen Beispiele |
|Globals|Liste aller globalen Variablen und Funktionen |



### Erzeugen der Dokumentation

Im Gitlab liegt ein [Makefile](https://gitlab.studip.de/studip/studip/-/blob/main/Makefile) mit Target "doc", so dass der folgende Aufruf:

`1 ~ % make doc`

im Verzeichnis `doc/html` die entsprechende API-Dokumentation frisch erzeugt.

Voraussetzung dafür ist die Installation von `doxygen`. Verwendet man Linux, kann man `doxygen` meist einfach über die Paketverwaltung installieren. Unter Ubuntu reicht dort zum Beispiel:

`2 ~ % sudo apt-get install doxygen`

Grundsätzlich kann `doxygen` auch für Unix, Mac und Windows aus den Quellen installiert werden. Eingehender dazu informiert die [englische Anleitung](https://www.doxygen.nl/manual/install.html).

Die Konfiguration für die Erzeugung befindet sich in [tools/Doxyfile](https://gitlab.studip.de/studip/studip/-/blob/main/Doxyfile). 
Besonders einfach lässt sich diese mit `doxywizard` erzeugen.


## Wie schreibe ich API-Dokumentation?

Wer schon einmal mit `@phpdoc@` oder `@javadoc@` gearbeitet hat, kennt sich praktisch schon aus. 
Selbstverständlich gibt es auch noch doxygen eigene Spezifika, die [weiter unten](#besonderheiten) erläutert werden. 
Zunächst aber einige Best Practices, wie dokumentiert werden soll.


#### Top/Datei-Level-Kommentare
Jede PHP-Datei außerhalb von `/template` muss mit einem Copyright-Vorspann und einer Beschreibung des Inhalts der Datei eingeleitet werden:

```php
/**
 * filename - Short description for file
 *
 * Long description for file (if any)...
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      name <email>
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 */
```



### Class-Kommentare

Jede Klasse muss einen Docblock haben, der den Nutzen und(!) die Verwendung beschreibt.

```php
/**
 * This class provides a singleton instance that is used to manage PDO database
 * connections.
 *
 * Example of use:
 *
 *   # getting a PDO connection
 *   $key = 'studip';
 *   $db = DBManager::get($key);
 *   $db->query('SELECT * FROM user_info');
 *
 *   # setting a PDO connection
 *   $manager = DBManager::getInstance();
 *   $manager->setConnection('example', 'mysql:host=localhost;dbname=example',
 *                           'root', *);
 *
 **/
```


Wenn die Klasse schon ausführlich im Top-Level-Kommentar beschrieben wurde, darf man stattdessen dorthin verweisen: "für eine ausführliche Beschreibung siehe Kommentar am Anfang dieser Datei". 

### Methoden- und Funktionskommentare

Jede Funktion und Methode muss einen Docblock haben, der beschreibt, was die Funktion/Methode tut und wie man sie verwendet. 
Die Kommentare sollten deskriptiv ("Opens the file") und nicht imperativ ("Open the file") sein. Für gewöhnlich braucht der Kommentar nicht beschreiben, %%wie%% die Funktion funktioniert. 
Solche Kommentare sollten direkt im Quelltext stehen.

Die folgenden Dinge sollten im Kommentar enthalten sein:

* eine Beschreibung der Funktion
* alle Argumente und ihre Beschreibung
* alle möglichen Rückgabewerte und ihre Beschreibung
* ob und wann die Funktion Exceptions wirft

Es müssen ganze Sätze verwendet werden. Funktionen sollten ebenso wie Klassen deskriptiv (in dritter Person) kommentiert werden.

Wenn Getter/Setter-Methoden, Konstruktoren oder Destruktoren nichts unerwartetes tun, darf die Beschreibung der Funktion/Methode weggelassen werden.

````phpregexp
    /***
     * Returns the value of the selected query parameter as a string.
     *
     * @param string $param    parameter name
     * @param string $default  default value if parameter is not set
     *
     * @return string  parameter value as string (if set), else NULL
     */
````

#### Codebeispiele

Codebeispiele können ganz einfach in einen Kommentar einfügt werden, indem man semantisch den Bereich mit  @code und @endcode einschliesst. Beispielsweise enthält die Datei [DBManager](https://develop.studip.de/trac/browser/trunk/lib/classes/DBManager.class.php#L15) folgenden Kommentar:

```php
/**
 * This class provides a singleton instance that is used to manage PDO database
 * connections.
 *
 * Example of use:
 * @code
 *   # get hold of the DBManager's singleton
 *   $manager = DBManager::getInstance();
 *
 * [...]
 *
 * @endcode
 */
```
