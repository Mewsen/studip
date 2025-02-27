---
title: UTF-8
---

Seit Version 4.0 verwendet Stud.IP UTF-8 als Standardkodierung. 
Für Kern- und Plugin-Entwickler gibt es einige Dinge zu beachten, die sich in der Entwicklung ändern. Betreiber sollten beim Umstieg ein paar Dinge beachten, dieses werden am Ende aufgeführt.

## Datenbank
Folgende Punkte sollte man wissen und beachten:
* Die Verbindung von PHP zur Datenbank via PDO wird nun mit charset=utf-8 realisiert.
* Die Datenbankkodierung wird mittels einer Migration umgestellt. Das Encoding ist utf8mb4, die Collation bei Textspalten utf8mb4_unicode_ci.
* Index Spalten mit MD5-Hashes als Schlüssel verwenden aus Performancegründen latin1_bin als Kodierung. Bei eigenen Tabellen muss man unbedingt darauf achten, dass dort für MD5-Index-Spalten ebenfalls latin1_bin verwendet wird, da sonst JOIN-Befehle auf Grunde unterschiedlicher Collations fehlschlagen!
* Spalten, die vorher *_bin waren, sind auf utf8mb4_bin umgestellt.
* Bei der Migration werden außerdem in allen Textspalten die htmlentities durch ihre UTF-8 Repräsentation ersetzt.

 **In der InnoDB-Konfiguration muss außerdem zusätzlich zu den bisherigen Optionen folgende gesetzt sein:** 
* `innodb_large_prefix=1` (Bis MariaDB 10.2)
* `innodb_file_formate=Barracuda`

Diese beiden Einstellungen werden von der Migration überprüft.

## Code - Kern und Plugins

Folgende Dinge sind bei der Entwicklung im Kern zu beachten:
* Alle PHP- und JS-Dateien müssen nun standardmäßig UTF-8 sein.
* Die String-Funktionen sind ihrer mb_*-Variante zu verwenden
* `studip_utf8(en|de)decode` existiert nicht mehr und wird auch nicht mehr benötigt

### Plugins

Um Plugins für Stud.IP 4.0 vorzubereiten sind zuerst die gleichen Regeln wie für die Entwicklung im Kern zu beachten.

Das Vorgehen zur Umstellung eines Plugins ist wie folgt:

#### Alle Quelldateien umkodieren auf UTF-8
Am einfachsten geht das, indem man sich ein Konvertierungsskript mit folgendem Befehl erstellen lässt:

```shell
find . -name '*.php' -exec printf "iconv -f windows-1252 -t utf-8 {} > {}.new \n mv {}.new {}\n" >> convert_files.sh \;
```

Dies konvertiert alle PHP-Dateien. Falls nötig, kann man dies für andere Dateitypen wiederholen durch anpassen dieses Befehls.

#### Datenbank

Vorhandene Datenbankinhalte werden normalerweise direkt via der Migration aus dem Kern umgestellt, d.h. eine weitere Migration ist nicht nötig. Bei der Erstellung neuer Tabellen ist zu beachten, dass dort dann das korrekte neue Encoding (utf8mb4) angegeben oder besser ganz weggelassen wird.


