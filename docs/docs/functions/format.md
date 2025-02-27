---
id: format
title: Formatierungsengine für Stud.IP
sidebar_label: Formatierungsengine
---

Vorhandene Funktionen:

| Funktion | Datei | Beschreibung |
| ---- | ---- | ---- |
|htmlReady|visual.inc.php|Keine Formatierung, nur simple HTML-Ersetzung|
|quotes_decode|visual.inc.php|Extrahiert quotes aus Text|
|quotes_encode|visual.inc.php|Liefert Quote-Code für übergebenen Text|
|format_help|visual.inc.php|Helper für formatReady, code-Handling|
|formatReady|visual.inc.php|Formatiert String mit allen Formatierungsmöglichkeiten. Special-modes: trim=yes/no, extern=yes/no, wiki=yes/no, comments=icon/none/full|
|wikiReady|visual.inc.php|Abkürzung für formatReady, setzt wiki=TRUE|
|wiki_format|visual.inc.php|Ersetzt Kommentarformatierungen|
|format_wiki_comment|visual.inc.php|Formatiert einen einzelnen Kommentar entsprechend Parameter|
|latex |visual.inc.php|Rendert LaTeX-Formel und bindet Bild ein|
|decodeHTML|visual.inc.php|Ersetzt HTML-Entitities durch entsprechende Character (iso latin-1)|
|format|visual.inc.php|Wendet reguläre Ausdrücke für Formatierungen an|
|preg_call_format_text|visual.inc.php|Helper für big/small-Formatierung|
|preg_call_format_list|visual.inc.php|Helper für Listenformatierung|
|preg_call_format_table|visual.inc.php|Helper für Tabellenformatierung|
|preg_call_rss_include|visual.inc.php|RSS-Feed includen|
|xss_remove|visual.inc.php|Gefährliche HTML-Tags entfernen|
|kill_format|visual.inc.php|Formatierungen für ASCII entfernen|
|FixLinks|visual.inc.php|Ersetzt Links durch HTML-Links|
|preg_call_link|visual.inc.php|Helper für Linkformatierung|
|idna_link|visual.inc.php|Geniert punycode für Umlaut-URLS|
|smile|visual.inc.php|Ersetzt Smiley-Ausdrücke|
|symbol|visual.inc.php|Ersetzt Smiley-Kurzformen|


Verarbeitungsreihenfolge:

* Normal: latex -> format -> FixLinks -> rss_include -> smile -> symbol
* Wiki: latex -> format -> FixLinks -> rss_include -> smile -> symbol -> wiki_format
