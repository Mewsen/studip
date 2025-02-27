---
title: Grundlagen für Einsteiger
---

### Programmieren für Stud.IP

Bevor man beginnt, zur Entwicklung von Stud.IP beizutragen, sollte man wissen, was [Stud.IP CoreGroup, StEPs, TICs, Lifters und BIESTs](../rules/introduction) sind.

Ebenfalls wichtig ist es, den [Coding-Stil](../coding-style) einzuhalten, sodass der geschriebene Code schneller für andere Stud.IP Entwickler verständlich ist. In dem Zusammenhang sind auch die [Namenskonventionen](../coding-style#namenskonventionen) und die Regeln für die [PHP Dateiformatierung](../coding-style#php-dateiformatierung) wichtig.

Hier ein veralteter [Workshop](http://develop.studip.de/studip/download/force_download/0/8217c5e9c3b82ab83e388d8aa2ce339f/studip_programmierung_20111222.pdf) aus dem Jahr 2011 von André Noack mit einem Gesamtüberblick.

### Entwicklungssystem

Zum Entwickeln für Stud.IP sollte ein Computer. auf dem ein Webserver,
mindestens PHP 7.2, eine MySQL-Datenbank, git und eine
Entwicklungsumgebung installiert ist, verwendet werden. Wie dies alles
eingerichtet wird, beschreibt [der Artikel zur Entwicklungsumgebung](./entwicklungsumgebung).

Für die Unix-Shell gibt es auch einige [Befehle](./tipps-zum-einstieg), welche die Entwicklung in einem großen Softwaresystem wie Stud.IP erleichtern.


### Das Stud.IP System

#### Stud.IP "Seiten"

In Stud.IP werden neue Seiten nicht als PHP-Skript hinzugefügt, sondern über das Framework [Trails](./trails). In diesem sind Seiten aufgeteilt in [Controller](./trails#der-controller), welche die Programmlogik enthalten und Ansichten (views), welche die Darstellung übernehmen. Sie werden durch sogenannte [Flexi-Templates](./flexi-templates) erzeugt, wobei es sich um PHP-Dateien handelt, denen Objekte und Variablen aus dem Controller übergeben werden.

Um einen Controller aufrufen zu können, ist es notwendig, diese in die globale [Navigation](Navigation) einzuhängen. Das Vorgehen zum Einhängen unterscheidet sich zwischen Controllern in Plugins und Stud.IP-internen Controllern.

#### Datenbank-Zugriffe

Intern verwendet Stud.IP PDO, um auf Datenbanktabellen zuzugreifen. Diese Zugriffe sind über [SORM (SimpleORMap)](./simpleormap) abstrahiert. Dabei handelt es sich um ein kleines Framework, welches die Überführung von Datenbankeinträgen in Objekte übernimmt.

#### Übersetzung

Stud.IP ist in zwei Sprachen erhältlich: Deutsch und Englisch. Im Quellcode werden deutsche Zeichenketten verwendet, welche mittels der PHP-Funktion `gettext` ins Englische übersetzt werden. Für PHP- und JavaScript-Code ist die Vorgehensweise unterschiedlich:

* [in PHP](Howto/Internationalisierung#internationalisierung-im-php-code)
* [in JavaScript](Howto/Internationalisierung#internationalisierung-im-js-code)


#### Plugins

Plugins sind ein bedeutender Bestandteil von Stud.IP. Mit diesen kann die Funktionsweise von Stud.IP verändert werden oder es können neue Funktionen zu Stud.IP hinzugefügt werden.

Die folgenden Seiten geben eine Einführung in die Plugin-Entwicklung:

* ['Erstellung eines Plugins' von Moritz Strohm (PDF)](https://develop.studip.de/studip/dispatch.php/document/download/5747961f81b385b1520cf7dc393f1db6)
* ['Plugin-Tutorial' von Elmar Ludwig](PluginTutorial)

