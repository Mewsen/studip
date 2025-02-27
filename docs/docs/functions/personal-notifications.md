---
id: personal-notifications
title: Persönliche Benachrichtigungen
sidebar_label: Persönliche Benachrichtigungen
---

Ab der Version 2.4 ist es sehr einfach, den Nutzer in Echtzeit über neue Inhalte oder spannende Aktionen, die ihn betreffen, zu informieren. Gedacht ist das für den Fall, dass er eine persönliche Nachricht bekommt oder jemand auf einen von ihm eingestellten Inhalt antwortet. Auf jeden Fall soll stets ein direkter Bezug zum Nutzer vorhanden sein. 
In diesen Fällen möchte man vielleicht sofort sehen, dass sich was getan hat und nicht erst beim nächsten Seitenneuladen. Gerade wenn Stud.IP in einem von 20 Browsertabs offen ist, kann das schon mal dauern. Da möchte man sofort reagieren können. Für Chat-artige Kommunikation zum Beispiel wäre das Gift. Die persönlichen Benachrichtigungen sind daher DAS Element, um Echtzeitereignisse zu ermöglichen.

Über das Feature, wie der Nutzer das sieht, und wie er/sie darauf reagieren und das Feature einstellen kann, steht mehr in der Anwenderdoku.

Hier soll es hauptsächlich um die Frage gehen: ich bin ein Entwickler und will da eine Benachrichtigung an den Nutzer abgeben. Was muss ich tun?

Eigentlich nur eine Zeile einfügen. Nehmen wir an, Nutzer A würde gerne über alle Änderungen einer bestimmten Wiki-Seite benachrichtigt werden wollen. Und ein anderer Nutzer B ändert diese Seite. So muss man beispielsweise folgende eine Zeile in den Code einfügen:

```php
PersonalNotifications::add(
      $user_A_user_id, //id of user A or array of ´multiple user_ids
      $url_of_wiki_page, //when user A clicks this URL he/she should jump directly to the changed wiki-page
      "User B changed wiki-page xyz", //a small text that describes the notification
      "wiki_page_1234", //an (optional) html-id of the content of the wiki page. If the user is looking at the content already, the notification will disappear automatically
      Icon::create("wiki", "clickable") //an (optional) icon that is displayed next to the notification-text
);
```


Man braucht also nur die user_id von Nutzer A, die URL der Wikiseite, einen ganz kurzen Beschreibungstext (bitte nicht zu lang werden lassen, das sprengt sonst das Layout) und optional noch eine HTML-ID und ein Bild.

Wozu die HTML-ID? Gerade bei einem Chat zum Beispiel will man nicht am Ende jeden eingegangenen Satz vom Gegenüber nochmal als Notification wegklicken. Da entstand der Gedanke, wenn man etwas schon gesehen hat auf der Seite, dann soll sich die Benachrichtigung auch von alleine erledigen. Wenn also Nutzer A per Zufall eh auf die Seite geht (durch ein rotes Icon in der meine_seminare-Übersicht) und die geänderte Wikieseite so sieht, dann muss er/sie nicht nochmal zusätzlich die Benachrichtigung wegklicken. Stattdessen wird automatisch ein AJAX-Request abgefeuert, der mitteilt, dass der Nutzer den Inhalt schon gesehen hat. 

Um das alles muss der Programmierer nicht besonders kümmern. Es muss nur beim `add`-Aufruf entweder eine HTML-ID angegeben werden oder eben nicht. So einfach geht das.
