---
title: Entwicklungsumgebung
---

Um selbst mitzuentwickeln, brauchst du ein lokales Testsystem auf einem Rechner, für den du alle nötigen Rechte hast. 

Das heißt im Einzelnen, dass du folgendes brauchst

* Ein Webserver (Apache oder Nginx), vorzugsweise mit Schreibzugriff auf die zentralen Konfigurationsdateien
* PHP ab Version 7
* Schreibrechte im Dateisystem sowohl für Dateien, die für dne Webserver erreichbar sind, als auch für solche, die außerhalb liegen
* Voller Zugriff auf eine MySQL-Datenbank MySQL oder MariaDB)
* Einen Git-Client zum Auschecken der aktuellsten Entwickler-Version von Stud.IP
* Ein Editor oder eine Entwicklungsumgebung (bspw. PHPStorm) zum Bearbeiten von Dateien

## Serverumgebung

Um für Stud,IP zu entwickeln brauchst du Zugriff auf einen Webserver und dessen Dateisystem. 
Den kannst du auf deinem eigenen Rechner einrichten oder einen vorhanden Server nutzen, auf den du per SSH oder anderem Remote-Zugriff zugreifst. 
Im Folgenden werden einige erprobte Lösungen für verschiedene Server-Betriebssysteme aufgeführt.

### Linux

Du benutzt Linux? Dann könnte man ja fast davon ausgehen, dass Du weißt, wie man einen Webserver installiert. Aber beispielhaft für ein Ubuntu Linux müsstest Du folgende Schritte durchgehen:

- Apache installieren (alternativ nginx): `sudo apt install apache2`
- MariaDB installieren: `sudo apt install mariadb`
- PHP installieren: `sudo apt install php libapache2-mod-php php-mysql`
- git installieren: `sudo apt install git`
- Stud.IP-Repository auschecken: `git clone git@gitlab.studip.de:studip/studip.git`
- In dem Stud.IP-Verzeichnis `make` ausführen.
- Im Ordner `./config` die Datei `config.inc.php.dist` nach `config.inc.php` kopieren.
- Im Ordner `./config` die Datei `config_local.inc.php.dist` nach `config_local.inc.php` kopieren und dann diese Datei bearbeiten. Hier müssen mindestens die Variablen `$DB_STUDIP_USER` und `$DB_STUDIP_PASSWORD` so gesetzt werden, dass dort die Zugangsdaten zur MariaDB bzw. MySQL drin stehen.
- Die Datei `./config/.htaccess.dist` nach `./public/.htaccess` kopieren (oder alternativ die Apache-Konfiguration oder die php.ini verändern).
- In MariaDB bzw. MySQL eine neue Datenbank `studip` anlegen: `CREATE DATABASE studip CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;`
- Und dann aus dem Ordner `./db` nacheinander in dieser Reihenfolge die Dateien `studip.sql`, `studip_root_user.sql`, `studip_default_data.sql`, `studip_demo_data.sql`, `studip_mvv_demo_data.sql`, `studip_resources_default_data.sql` und `studip_resources_demo_data.sql` einspielen.
- Wenn jetzt noch der `DocumentRoot` des Apache auf den Ordner `public` von Stud.IP zeigt, sollte alles laufen.

### Windows

Diese Installationsanleitung erläutert die Installation eines Stud.IP Testsystems
in der Version 4.6 auf Windows. Eine Anleitung für Unix-Betriebssysteme findet sich
[hier](https://hilfe.studip.de/admin/Admins/Installationsanleitung).

Für die Installation werden folgende Anwendungen benötigt
- Apache Webserver
- MySQL/MariaDB Datenbankserver: Mindestens MySQL 5.7 oder MariaDB-10.2.3
- PHP: mindestens PHP 7.2, höchstens PHP 8.1

In dieser Anleitung wird demonstrativ die Apache-Distribution XAMPP in der Version 7.3.28 verwendet.
Bei der Wahl einer anderen Version sollte auf eine kompatible PHP Version geachtet werden.
Es wird außerdem nur eine Basisinstallation erstellt,
also die möglichst einfachste Variante, ein Testsystem in Betrieb zu nehmen,
ohne zu sehr ins Detail der einzelnen Aspekte zu gehen.
Es geht also eher darum, einfach mal ein Testsystem aufzusetzen,
welches beispielsweise zum Entwickeln von Plugins genutzt werden kann.
Für weitere und ausführliche Erläuterungen sollte die oben verlinkte Unix-Installationsanleitung betrachtet werden.

Die Stud.IP Dateien können mittels git unter folgender Repository Adresse geladen werden:
`git clone git@gitlab.studip.de:studip/studip.git`


## Apache Konfiguration

Der "document-root" von dem XAMPP die anzuzeigenden PHP-Dateien lädt,
befindet sich standardmäßig unter `C:/xampp2/htdocs`.
Dies heißt, dass alle anzuzeigenden Dateien (also beispielsweise das Stud.IP Verzeichnis) in diesem liegen müssen,
damit sie über `localhost` geöffnet werden könne.
Alternativ kann der document-root von Apache auch beliebig angepasst werden.
Dazu muss in der Datei http.conf (xampp-verzeichnis\apache\conf\http.conf)
die Einstellung `DocumentRoot` und `Directory` (~ Zeile 252f) auf das neue Verzeichnis umgestellt werden.
Bedacht werden sollte, dass bei jeder Änderung von config Dateien (http.conf, php.ini etc.)
die betroffenen Anwendungen (Apache, MySQL etc.) neu gestartet werden müssen, damit die Änderungen übernommen werden.
Um den document-root im Verzeichnis `C:\Users\MaxMustermann\PhpstormProjects` zu setzen,
könnte die http.conf an betreffender Stelle beispielsweise so aussehen:

```
\[...\]  
DocumentRoot "C:\Users\MaxMustermann\PhpstormProjects"  
<Directory "C:\Users\MaxMustermann\PhpstormProjects">  
\#  
\[...\]
```

## PHP Konfiguration

In der php.ini (xampp-verzeichnis\php\php.ini) müssen folgende Konfigurationen getroffen werden.  
Hinter der jeweiligen Einstellung ist die Zeile angegeben,
falls XAMPP in der gleichen Version neu installiert wurde,
andernfalls kann in der Regel mit Strg+F die ini Datei einfach durchsucht werden.  
Außerdem sollte angemerkt werden, dass Zeilen, die mit einem semicolon (`;`) beginnen, auskommentiert sind.
Also sollte entweder ein anderer Eintrag zur Überschreibung der Einstellung gesucht werden (bspw. `error_reporting` ),
oder falls nur dieser Eintrag existiert, das semicolon entfernt werden (bspw. `mbstring.internal_encoding`).

- short_open_tag = On (Zeile 192)
- max_execution_time = 300 (Zeile: 380)
- max_input_vars = 10000 (Zeile: 397)
- memory_limit = 1024M (Zeile: 401)
- error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT  & ~E_NOTICE (Zeile: 457)
- post_max_size = 514M (Zeile: 687)
- default_charset  = "UTF-8" (Zeile: 706)
- upload_max_filesize = 512M (Zeile: 839)
- allow_url_fopen = On (Zeile: 850)
- mbstring.internal_encoding  = "UTF-8" (Zeile: 1670)

## Stud.IP Konfiguration

### Basis Konfiguration

Wenn der Webserver und PHP richtig konfiguriert wurden, kann Stud.IP konfiguriert werden.  
Dafür existiert seit der Stud.IP Version 4.5 ein Installationsassistent der automatisch beim ersten Aufruf der Startseite aufgerufen wird.
Stud.IP ist nach der Konfiguration in einem Webbrowser unter `localhost` (oder der IP-Adresse des Rechners auf dem XAMPP läuft) verfügbar.
Um die Startseite zu öffnen, muss in den Ordner `public` navigiert werden.  
Beispiel: Wenn das komplette Stud.IP Verzeichnis im `DocumentRoot` liegt und `4.6` heißt,
kann mit der URL `localhost/4.6/public` im Webbrowser die Startseite aufgerufen werden.
Falls Stud.IP noch nicht konfiguriert wurden, wird der Installationsassistent geöffnet.

Im Folgenden werden alle Schritte des Installationsassistenten durchgegangen, essenzielle Schritte sind fett markiert.

Schritt 1:  
Assistent starten klicken  
**Schritt 2**:  
Hier wird angezeigt, ob PHP richtig konfiguriert wurde. Falls Teile nicht richtig konfiguriert wurden, wird hier darauf hingewiesen.
Falls die richtige XAMPP version installiert wurde und die PHP Konfiguration richtig stattgefunden hat,
sollten hier alle Einstellungen "ok" sein. Falls doch noch Änderungen gemacht werden, sollte dran gedacht werden,
den Apache Server neu zu starten.  
**Schritt 3**:  
Hier wird die Datenbank initial erstellt, als Host wird `localhost` und als Nutzer `root` empfohlen.
Wenn der MySQL/MariaDB Server gestartet ist (in XAMPP das Modul unter Apache),
sollte das Prüfen der Verbindung erfolgreich sein.  
Schritt 4:  
Hier wird angezeigt, ob MariaDB richtig konfiguriert ist.
Mit der empfohlenen XAMPP Version sollte bereits, ohne weitere Einstellungen getroffen zu haben, alles stimmen.  
Schritt 5:  
In diesem Schritt wird geprüft, ob ausgewählte Datenverzeichnisse beschreibbar sind.
Unter Windows sollten standardweise keine weiteren Einstellungen nötig sein und die Verzeichnisse beschreibbar sein.  
Schritt 6:  
Hier können die Standard-Daten für die Datenbank erstellt werden. Die Installation optionaler Demo Daten wird hier empfohlen,
falls nicht anderweitig Daten verfügbar sind.
Die restlichen, zu treffenden Einstellungen sind lediglich für Produktivsysteme relevant und können daher für dieses Testsystem beliebig gefüllt werden.  
**Schritt 7**:  
Hier ist es möglich ein root Konto für den Login in das Stud.IP System zu erstellen.
Dieses root-Konto sollte sich gemerkt werden, da nur root-Nutzer Zugang zu Funktionen wie dem Installieren von Plugins besitzen.  
Schritt 8:  
Hier muss lediglich gewartet werden, dass die Installation durchgeführt wird.  
Schritt 9:  
Bestätigung, dass die Installation (hoffentlich) erfolgreich war.

### Weitere Konfiguration
Unter Windows müssen und können noch weitere Konfigurationsschritte durchgeführt werden.
Alle diese sollten in die lokale Konfigurationsdatei `config_local.inc.php` (`<studip-verzeichnis>/config/config.local.inc.php`)
innerhalb des zweiten `namespace` getroffen werden. Hier findet sich auch die Einstellung für die Verbindung mit der Datenbank,
falls gewünscht wird, diese anzupassen.  
Eine config.local.inc.php Datei, die alle folgenden Einstellungen getroffen hat, ist hier sonst separat verfügbar. Eventuell muss die Datenbankkonfiguration innerhalb dieser jedoch angepasst werden.
[config_local.inc.php](../assets/3310a5850c6c4ed2d0b55a1884e5a39b/config_local.inc.php)

Es wird ein `tmp` Verzeichnis/Ordner zum Ablegen von temporären Dateien benötigt.
Empfohlen wird hier das Verzeichnis im Stud.IP Verzeichnis zu erstellen (auf derselben Ebene wie die Verzeichnisse `app`, `lib`, `config`, `public` etc.).
In der oben genannten Konfigurationsdatei muss der Variable `$TMP_PATH` der Pfad des tmp-Verzeichnisses zugewiesen werden.
Falls das `tmp`-Verzeichnis im Stud.IP Verzeichnis angelegt wurden, kann einfach die folgende Zeile in die Konfigurationsdatei übernommen werden.  
`$TMP_PATH = $STUDIP_BASE_PATH . "/tmp";`

Um mögliche Fehlermeldungen zu verhindern, sollte das Caching abgeschaltet werden.
Dazu kann die folgende Zeile der Konfigurationsdatei hinzugefügt werden.  
`$CACHING_ENABLE = false;`

Da es sich hierbei um ein Testsystem handelt, wird vermutlich kein Mailserver benötigt, um E-Mails abzuschicken,
jedoch sollte dann, um Fehlermeldungen zu vermeiden, das Verschicken von Mails unterbunden werden.
Dazu kann die folgende Zeile der Konfigurationsdatei hinzugefügt werden.  
`$MAIL_TRANSPORT = 'debug';`

In einigen Stud.IP Versionen ist außerdem die Aufnahme von folgender Zeile in die Konfigurationsdatei zu empfehlen,
um PHP Warnung zu verhindern:  
`define("LC_MESSAGES", 5);`

Das Stud.IP an der Universität Oldenburg bietet die Möglichkeit Texte auf Englisch anzuzeigen.
Um dies auch in der Entwicklung zu berücksichtigen, sollte Englisch als Sprache hinzugefügt werden.
Dazu kann die folgende Zeile der Konfigurationsdatei hinzugefügt werden.  
`$CONTENT_LANGUAGES['en_GB'] = array('picture' => 'lang_en.gif', 'name' => 'English');`

Falls alle nötigen und gewünschten Einstellungen getroffen worden sind, sollte die Stud.IP Testumgebung nun verwendbar sein.


### Mac OS

// TODO

## Aktuelle Version besorgen

Die Stud.IP-Entwickler verwenden SVN für Versionsverwaltung, alle offiziellen Versionen sind anonym und öffentlich lesbar.
Einen einfachen Einblick bietet die Gitlab unter https://gitlab.studip.de

**Wichtig:** 

Es gibt immer genau EINE Stud.IP-Version, an der aktiv entwickelt wird. 
Die liegt im Git-Repository unter [main](https://gitlab.studip.de/studip/studip/-/tree/main).
Alle 6 Monate wird aus dem dann aktuellen Repository ein Release geschnürt. A
lte Releases werden z.T. noch mit Bugfixes versorgt, ansonsten arbeiten alle Entwickler immer im main.

*Lesen* darf jeder: Mit deinem Git-Client kannst du unter https://gitlab.studip.de/studip/studip.git  den kompletten Code auschecken. 
Es gibt eine ganze Reiher verschiedener Branches, die zum Teil sehr speziell sind. Du brauchst vor allem folgende Infos:


git-Kommando zum Auschecken der aktuellen Entwickler-Version:
```shell
git clone https://gitlab.studip.de/studip/studip.git
```

*Schreiben* darf allerdings nicht jeder. 
Zwar freuen wir uns über jeden, der eigene Entwicklungen, Bugfixes und Verbesserungen zu Stud.IP beitragen möchte, aber ohne sorgfältige Qualitätssicherung sollte natürlich kein Code in die aktuelle Version gelangen. 
Deshalb dürfen nur ausgewählte Entwickler Code in das Repository einchecken. 
Solange du noch nicht dazu gehörst, führt dein Weg über das Developer-Board. 

## Installation und Konfiguration

Alle wichtigen Hinweise zur Stud.IP-Installation sind in der [Admins/Installationsanleitung](Admins/Installationsanleitung) aufgeführt. 
Da du keine Release-Version installierst, sondern eine SVN-Version, musst du folgende Unterschiede beachten:


## Entwicklungsumgebung

### Einfacher Texteditor

Minimalanforderung ist ein einfacher Texteditor, z.B. vi oder nano.

### Erweiterter Texteditor

Erweiterte Texteditoren wie z.B. Kate bieten wesentlich mehr Komfort als vi oder nano, da sie Syntaxhervorhebung, geteilte Ansichten (mehrere Dateien in einem Fenster) und automatische Texteinfügung besitzen.

### IDE (PHPSTORM)
// TODO

## Änderungen einchecken

*Schreiben* darf wie gesagt nicht jeder im SVN. Zwar freuen wir uns über jeden, der eigene Entwicklungen,
Bugfixes und Verbesserungen zu Stud.IP beitragen möchte, aber ohne sorgfältige Qualitätssicherung sollte natürlich kein Code in die aktuelle Version gelangen. Deshalb dürfen nur ausgewählte Entwickler Code in das Repository einchecken. Solange du noch nicht dazu gehörst, führt dein Weg über das Developer-Board. 
