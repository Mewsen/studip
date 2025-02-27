---
title: Allgemeine Struktur
---

Jeder, der einmal eine Programmiersprache angefangen hat, weiß, womit man anfängt: mit einem hello-world Programm. Ja, man mag die Dinger nicht mehr sehen, aber nützlich, um die grobe Syntax und die minimalsten Konventionen zu verstehen, sind sie allemal.

Bei Studip betrachten wir einfach mal eine komplett leere Seite, die nur einen Schiftzug in sich trägt. Um zu sehen, welchen Anteil einheitliches Design und Session-Routinen einnehmen, sollte man sich einfach klar darüber werden, wieviele Programmzeilen eine derartig "nackte" Studip-Seite klein ist. 

Die nun folgende Datei könnte bei Studip locker im Ordner public stehen:







```php
<?php
/*
test.php - Anzeige einer leeren Gerüstseite von Stud.IP
Copyright (C) 2009 Rasmus Fuhse <ras@fuhse.org>

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.

$Id: test.php 12381 2009-06-03 16:57:46Z Krassmus $
*/

//Ab hier fangen wir an, über den Code nachzudenken.

//// Initialisierungen: Include-Pfad usw.
require '../lib/bootstrap.php';

//// Hier wird eine Session gestartet. 
page_open(array('sess' => 'Seminar_Session', 
  'auth' => 'Seminar_Default_Auth', 
  'perm' => 'Seminar_Perm', 
  'user' => 'Seminar_User'));
$auth->login_if($_REQUEST['again'] && ($auth->auth["uid"] == "nobody"));
$perm->check("user");

include ('lib/seminar_open.php'); // initialise Stud.IP-Session


//// Variablen, die zur Anzeige helfen.
$HELP_KEYWORD="Basis.Testseite";  // Wer auf Hilfe klickt, wird zur Hilfeseite Basis.Testseite geführt.
$CURRENT_PAGE = _("Testseite");   // Zeigt an, wie diese Seite heißt


//// Ab hier wird der erste Text in das HTML-Doc geschrieben

//HTML-Header bis zur <body> Anweisungen
include 'lib/include/html_head.inc.php';

//Studip-Header, also die Navigationssymbole, die über fast jeder Seite stehen.
include 'lib/include/header.php';


//Hier kommt nun die eigentliche Nachricht
$ausgabe_format = '<table class="blank" width="100%%"
border="0" cellpadding="0" cellspacing="0">
<tr><td class="topic"><b>&nbsp;%s </b>%s</td></tr>
<tr><td class="steel1">&nbsp;</td></tr><tr><td class="steel1"><blockquote>%s</blockquote></td></tr>
<tr><td class="steel1">&nbsp;</td></tr>
</table><br>'."\n";

printf ($ausgabe_format, htmlReady( _("und nun") ), *, formatReady( _("Hello World!") ));


// Save data back to database.
page_close();

?>
```


# Wichtige Elemente der Testseite: Bootstrap
[#bootstrap](#bootstrap)

Das erste Statement eines Skripts in `public` lautet immer:

```php
// Initialisierungen: Include-Pfad usw.
require '../lib/bootstrap.php';
```

Damit wird der $STUDIP_BASE_PATH gesetzt, der Include-Pfad angepasst und alle wichtigen Konfigurations- und Systemklassendateien geladen.

# Wichtige Elemente der Testseite: Sessions
[#page_open](#page_open)

Bei Studip wird aus diesen oder jenen Gründen einiges anders gemacht als bei anderen PHP-Modulen. Das fängt zum Beispiel mit der Session an. Bei PHP gibt es ein eingebautes Session-Management, womit man theoretisch Variablen über das, was der Nutzer gerade macht, welche Daten er eingegeben hat und so weiter, global auf dem Server ablegen kann. Aber leider geht das erst ab PHP4. Da Studip unter PHP3 entstanden ist, wird bis heute ein Session-Management mitgeschleppt, das auf den Zusatz PHPLIB aufbaut und für moderne PHP-Entwickler etwas altbacken aussieht. Im Grunde ist es aber das selbe wie eine normale PHP-Session und lässt sich auch einfach bedienen. Auf der Testseite wird diese Session durch

```php
page_open(array('sess' => 'Seminar_Session', 
  'auth' => 'Seminar_Default_Auth', 
  'perm' => 'Seminar_Perm', 
  'user' => 'Seminar_User'));
```

gestartet und durch

`page_close();`

wieder beendet. Beenden muss man die PHPLIB-Session, damit alle Variablen auch wirklich bei der nächsten Session (auf der nächsten Stud.IP Seite) wieder zur Verfügung stehen.

# Sicherheitsüberprüfung

Gleich nach dem page_open(...) folgt die Sicherheitsüberprüfung, ob der Nutzer überhaupt die Seite sehen darf. Bei

`$perm->check("user");`

wird zum Beispiel überprüft, ob der Betrachter der Seite die Rechte eines "user" hat. Bei einer Seite, die man nur mit Admin-Rechten sehen sollte, würde also 

`$perm->check("admin");`

stehen.
Es gibt fünf Sicherheitslevels: Gast (also ohne besondere Rechte, der darf nur öffentliche Veranstaltungen sehen, bei denen die Sicherheitsabfrage im Code fehlt), "user", "tutor", "dozent" und "admin". Gleich nach der Sicherheitsüberprüfung, wird in der Include-Datei

`include ('lib/seminar_open.php');`

die Session mit allerlei Variablen zugemüllt, die auf den meisten Seiten von Belang sind, nur auf unserer kleinen Testseite noch nicht.

# Aufbau der Kopfzeilen

In Studip strebt man ein einheitliches, gediegenes Design an. Dazu gehört, dass alle Seiten (nimmt man mal zum Beispiel den Messenger raus) die gleiche Kopfzeile und die gleichen Style-Anweisungen einbauen. Das geschieht in den Zeilen:

`include 'lib/include/html_head.inc.php';`

für das HTML-Grundgerüst von `<html>` bis zu `<body>` mitsammt allen Einbindungen von CSS-Dateien und so weiter und

`include 'lib/include/header.php';`

was die eigentlich sichtbare Kopfzeile darstellt mit den Icons für Startseite, Nachrichten, Homepage, dem Studip-Logo und so weiter. In der Kopfzeile taucht auch der Name der Seite auf und ein Link zur Hilfe-Seite, der zudem Informationen darüber verfügt, worüber genau eine Hilfeseite angezeigt werden soll. Beide Informationen werden in der header.php anhand von zwei Variablen gesetzt. Deswegen sollte schon VORHER im Code stehen:

```php
$HELP_KEYWORD="Basis.Testseite";  
$CURRENT_PAGE = _("Testseite");
```

# Textbausteine in Studip

Was [später in dieser Hilfe](quickstart/Internationalisierung) noch genauer erklärt wird, sind die Textbausteine wie das gerade aufgetauchte 

```php
_("Testseite")
```

Ein Studip-Neuling fragt sich zwangsläufig, was diese Unterstrichfunktion sein soll. Normaler Text würde es hier sicherlich auch tun. Das Problem ist gewissermaßen die Möglichkeit, dass man sich jede Studip-Seite auch auf Englisch oder theoretisch jeder anderen Sprache anzeigen lassen könnte. Die entsprechende Übersetzungsarbeit wird nicht im Code vorgenommen (was den Code noch unübersichtlicher werden lassen würde, als er ohnehin schon ist), sondern in der Deklaration der Funktion Unterstrich "_" oder auch gettext(). Deswegen gilt für die Entwickler, dass jedes bisschen Text, das nicht eine HTML-Anweisung ist, durch die _("...") Funktion gejagt wird.

Als Beispiel, was genau durch gettext gesetzt werden muss und was nicht, eignet sich das Beispiel oben gut. Die Variable `$CURRENT_PAGE` wird als tatsächlich sichtbarer Text in die Kopfzeile geschrieben und die Variable `$HELP_KEYWORD` dient nur als Link-Parameter, der nicht sichtbar ist, sondern nur der Hilfe-Seite in der Adresszeile des Browsers übergeben wird.
