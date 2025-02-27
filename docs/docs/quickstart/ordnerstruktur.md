---
title: Ordnerstruktur
---

Der Stud.IP Verzeichnisbaum beherbergt eine Menge von Dateien in einigen Unterordnern des Hauptverzeichnisses. Die Funktion der einzelnen Unterordner (und ggf. deren Unterordner) werden in diesem Artikel erläutert.

### app

Hier sind bereits auf [Trails](Trails) umgestellte Seiten enthalten.

#### controllers

Der Unterordner "controllers" in "app" beherbergt Trails-Controller für alle Stud.IP-Seiten, welche über Trails geladen werden.

#### views

Zu jedem Controller gehört eine Ansicht (view), welche in diesem Unterordner von "app" gespeichert wird.

### cli

PHP-Skripte zur Benutzung von Stud.IP auf der  Kommandozeile sind in diesem Ordner enthalten.

### config

Konfigurationsdateien, inklusive der Vorlagen der beiden Haupt-Konfigurationsdateien config_local.inc.php und config.inc.php werden hier abgelegt.

### data

Hierdrin werden Dateien abgespeichert, welche nicht im Web-Root des Webservers liegen sollten und somit nicht direkt über den Webserver abrufbar sind.

### db

Hierin befinden sich SQL-Skripte, mit denen eine Stud.IP-Datenbank neu aufgesetzt werden kann. Zusätzlich sind Skripte mit Demo-Daten und Migrationsskripte für ältere Stud.IP-Versionen enthalten.

### doc

Dokumentationen zur Installation von Stud.IP.

### lib

Module und Bibliotheken von Stud.IP sind hierin enthalten. Dieser Ordner hat eine Reihe wichtiger Unterordner:

#### classes

Enthält Klassendefinitionen für Objekte, welche nicht in der Datenbank abgelegt werden.

#### models

Hier sind die meisten SimpleORMap (SORM) Datenbankmodelle abgespeichert.

#### navigation

Die verschiedenen Arten von Navigationsobjekten sind in diesem Ordner abgelegt.

#### plugins

Die Definitionen der Plugin-Schnittstelle sind hierin enthalten.

#### locale

Dieser Ordner enthält die Übersetzungsdateien von Stud.IP, sowie Skripte für die Unix-Shell, welche das automatische Erstellen der Übersetzungsdateien für Stud.IP erleichert.

#### public

Hier sind Dateien enthalten, welche direkt über den Webserver geladen werden können. Außerdem sind die wichtigsten Skripte (dispatch.php, plugins.php, ...) des Stud.IP-Systems in diesem Ordner enthalten. Der Ordner hat drei Unterordner.

##### assets

In diesem Unterordner von "public" sind Schriftarten, Bilder (inklusive Icons), JavaScript-Dateien, Sounddateien und Stylesheet-Dateien enthalten, welche beim Laden einer Stud.IP-Seite einfach mitgeladen werden können.

##### pictures

Verschiedene Hintergrundbilder für Seitenleisten oder bestimmte Elemente auf einer Seite.

##### plugins_packages

Hier werden Plugins abgelegt. Für jede Herkunftsbeschreibung eines Plugins ("origin" in der plugin.manifest Datei) wird ein eigener Unterordner angelegt, in welchem dann das Plugin abgelegt wird.

#### templates

Templates für Seiten, welche noch nicht auf [Trails](Trails) umgestellt wurden.

#### vendor

Bibliotheken, welche von externen Entwicklern entwickelt wurden und in Stud.IP benötigt werden, sind in diesem Ordner enthalten.
