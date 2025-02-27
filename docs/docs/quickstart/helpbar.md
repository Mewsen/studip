---
id: helpbar
title: Einbindung von Hilfe-Inhalten
sidebar_label: Hilfe-Inhalten
---


In der Helpbar werden die Hilfe-Inhalte angezeigt. Dazu gehören:

*der Link zum Hilfe-Wiki
*Kurze Hilfetexte, die in früheren Versionen in der Infobox angezeigt wurden
*Touren

## Hilfe-Texte
Die Hilfe-Texte werden aus der Datenbank geladen und sind stets für eine Route gültig.

Es ist möglich, für die Anzeige das Vorhandensein von Request-Parametern zur Bedingung zu machen, z.B.: "wiki.php?view=edit".

## Hilfe-Touren
Auch Touren können für eine Route definiert werden. Eine Tour besteht aus einzelnen Schritten, die (wie Tooltips) auf Elemente einer Seite bezogen sind.

Es gibt zwei verschiedene Arten von Touren (tour und wizard): die modale *tour* erlaubt keine Eingaben, beim *wizard* bleibt Stud.IP aktiv bedienbar. Es ist möglich, Touren so zu konfigurieren, dass sie automatisch beim (ersten) Aufruf der Seite gestartet werden.

Zum Wechseln zwischen Tour-Schritten gibt es eine Kontrollleiste mit fest platzierten Bedienelementen (Weiter, Zurück, Beenden).
Touren können über mehrere Stud.IP-Seiten führen.

## Hilfelasche für Plugins

Derzeit ruft man dafür einfach `Helpbar::get()->addPlainText` auf. Siehe [Beispiel](https://gist.github.com/luniki/2ca7d97317c697702795)

## Hilfelasche ausblenden

Um die Hilfelasche auszublenden, ruft man `Helpbar::get()->shouldrender(false);` auf
