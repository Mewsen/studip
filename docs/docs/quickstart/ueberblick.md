---
title: Überblick
slug: /quickstart/
---

Herzlichen Glückwunsch! Wenn du diese Seite aufgerufen hast, bist du
kurz davor, mit der Stud.IP-Entwicklung loszulegen. Ja, du! Nicht verschämt weggucken oder gar Angst vor der großen Aufgabe haben: 
Wer sich grundlegend mit PHP und MySQL auskennt und bereit ist, sich zunächst mit ein paar Grundsätzen der Stud.IP-Entwicklung vertraut zu machen, 
bringt alles mit, was nötig ist.

## Entwicklungsforum

Stud.IP ist ein Open-Source-Projekt. Das heißt bei uns: Die Entwickler*innen bestimmen frei und aus eigener Motivation, 
wo's langgeht und organisieren sich selbst. 
Der allererste Schritt: Besorge dir einen Account auf dem [Developer-Server](http://develop.studip.de/studip). 
Da laufen alle Diskussionen rund um die Weiterentwicklung. Dein erster Anlaufpunkt ist das "Developer-Board". 
Hier kannst du Fragen loswerden und deine Ideen präsentieren. Nur Mut, einfach mal Hallo sagen.

## Berechtigungen

Open-Source heißt nicht automatisch: Chaos. Auch bei uns gibt es Menschen, die das letzte Wort haben dürfen und 
dafür mehr Verantwortung tragen müssen als andere. 

**Das ist die Core-Group**. 

Zur Zeit sind das ca. 16 Menschen, die schon lange dabei sind und durch anhaltendes persönliches Engagement bewiesen haben, 
dass ihnen Stud.IP am Herzen liegt. Die meisten werden von ihrem Arbeitgeber, z.B. einer Universität, die Stud.IP einsetzt, 
dafür bezahlt, dass sie an Stud.IP mitarbeiten. 
Voraussetzung ist das aber nicht und die Mitgliedschaft ist an Personen gebunden, nicht an Arbeitgeber: 
Wie die Bundestagsabgeordneten sind die Core-Group-Mitglieder nur ihrem Gewissen verpflichtet.

Die Core-Group-Mitglieder entscheiden demokratisch, welche Weiterentwicklungen in das Stud.IP-Release aufgenommen 
werden und welche Regeln für die Entwicklung gelten. Dafür sind sie verpflichtet, sorgfältig zu testen, regelmäßig 
auf +dem Developer-Server mitzulesen und sich mit allen Fragen der Weiterentwicklung zu beschäftigen.

Am Anfang deiner Entwicklungstätigkeit wirst du noch nicht zur Core-Group gehören. Das heißt: Andere schauen sich an, was du entwickelst und entscheiden darüber, ob es so wie's ist, in die offizielle Stud.IP-Version aufgenommen wird. Das geschieht natürlich transparent und für dich jederzeit nachvollziehbar. Du wirst, wenn du die Hinweise hier befolgst, nicht mit einer fertigen, arbeitsreichen Entwicklung dastehen und hören: "Nein, das wollen wir nicht." Aber das heißt auch: Du musst dich nicht um alles kümmern, sondern kannst dir die Bereiche aussuchen, in denen du dich besonders kompetent fühlst oder die dir besonders viel Spaß machen. Wenn du durch Engagement und brauchbare Ideen und Beiträge auffällst, wirst du über kurz oder lang gefragt werden, ob du mehr Verantwortung übernehmen und Core-Group-Mitglied werden willst.

## Personengruppen

Diese Anleitungen beschreibt die Aspekte der Stud.IP-Entwicklung, die im engeren Sinne als "Programmieren" bezeichnet werden.
Das Stud.IP-Projekt braucht aber nicht nur Informatiker*innen und
PHP-Hacker, sondern ganz dringend auch engagierte Menschen mit anderen Interessen und Fähigkeiten. Das sind z.B.:

| Gruppe | Beschreibung |
| ---- | ---- |
| Tests | Ständig entsteht neues und die Programmierer selbst sind oft schlechte Tester. Zu eng ist der Blick auf das selbst entwickelte, als dass die Art und Weise, wie der "normale Nutzende" mit dem System umgeht immer gleich gut berücksichtigt werden könnte. Wenn du Spaß daran hast, neue Funktionen auf Herz und Nieren zu testen und gründlich zu meckern: Im Developer-Board melden!|
| Grafik und Design | Stud.IP kommt mit dem Anspruch daher, ansprechend gestaltet zu sein. Dafür brauchen wir Menschen, die sich gern mit Farben, Icons, Bedienelementen, Fotos und Schriftarten beschäftigen. Wenn du Verbesserungsvorschläge hast, dich etwas gewaltig stört oder du dich mit Bildbearbeitung und Webdesign beschäftigen willst: Im Developer-Board melden! |
| Sprachkunst | Hinweistexte, Fehlermeldungen und andere Texte in Stud.IP müssen prägnant und treffend formuliert werden. Dazu kommt die Hilfe und die Übersetzung ins Englische oder andere Sprachen. Wenn du dich auf dieser Spielwiese tummeln möchtest: Im Developer-Board melden! |
| Didaktik und Pädagogik |  Stud.IP ist eine E-Learning-Anwendung. Studierende, Lehrende und andere Anwender*innen sollen es als Werkzeug nutzen, um Lehr- und Lernprozesse zu gestalten. Wenn du dazu Ideen, Verbesserungsvorschläge und Anregungen hast: Im Developer-Board melden! |


Der Rest dieser Anleitung ist tatsächlich für diejenigen gedacht, die
selbst im Dreck wühlen möchten, also mit PHP, JavaScript und SQL umgehen wollen. 
Alle anderen fühlen sich bitte nicht ausgegrenzt, sondern ins Developer-Board verwiesen.

## Technische Grundlage

Stud.IP ist eine **PHP**-Anwendung, die eine **MySQL/MariaDB*-Datenbank verwendet. 
Wer mitentwickeln will, braucht also vor allem PHP-Kenntnisse, muss sich etwas mit SQL auskennen und ein bisschen über Apache- oder Nginx-Konfiguration wissen. 
Und, wie immer bei Webanwendungen: Alle Ausgaben geschehen in HTML, formatiert durch CSS (SCSS und LESS). 
Einige Funktionen verwenden zudem JSON als Zwischenformat, JavaScript und AJAX sind ebenfalls an vielen Stellen präsent. 
Wenn all das keine Fremdwörter für dich sind, bis du gut gerüstet.

## Git-Repository

Da viele Entwickelnde am verschiedensten Orten an Stud.IP
mitentwickeln, wird ein Versionsverwaltungssystem eingesetzt. Wir
haben uns für Git (Gitlab) entschieden. Alle Infos, die für den Start wichtig sind, findest du auf der Seite [Entwicklungssystem aufsetzen](Entwicklungsumgebung).

## BIESTs, StEPs und Lifters

Qualitätssicherung wird bei Stud.IP großgeschrieben. Die Entwickelnden haben sich ein umfangreiches Regelwerk gegeben, mit dem sie die verschiedenen Anforderungen bei der Weiterentwicklung überschaubar machen. Drei Begriffe stehen dabei für die wesentlichsten Vorgänge:

| Typ | Beschreibung |
| ---- | --- |
| BIESTs | Ein Biest ist ein erkannter Fehler in der Software. Fies und unbedingt zu elimieren. Im "Bug-Board" auf dem Developer-Server werden alle Fehler gesammelt und warten dann auf ihre Erledigung. Wenn du glaubst, einen Fehler entdeckt zu haben, kannst du ihn dort über ein Formular melden. | 
| StEPs | Weiterentwicklung heißt: Neue Funktionen hinzufügen, vorhandene ändern. Wer eine Idee dazu hat, formuliert sie in einem "Stud.IP Enhancement Proposal" (kurz: StEP). Im "StEP-Forum" auf dem Developer-Server werden die StEPs dann diskutiert: Ist der Vorschlag sinnvoll, ist alles bedacht und gibt es einen sinnvollen Plan zur Umsetzung? Schau dich dort im Wiki um, um die aktuellen StEPs, d.h. die konkret geplanten Weiterentwicklungen, anzuschauen. Wenn du einen eigenen Vorschlag einbringen willst, musst du dir einen "Paten" aus der Core-Group suchen. Das ist keine Schikane, sondern hilft uns, Ideen zu sortieren und Neueinsteigern bei der Orientierung zu helfen. |
| Lifters | Manche Umbauten lassen sich nicht auf einen Schlag erledigen. StEPs müssen immer zu einem bestimmten Release vollständig umgesetzt sein, grundlegendere Umbauarbeiten brauchen aber manchmal länger. Beispiel: Die vollständige Umstellung auf ein Template-System. Am Anfang wirst du vermutlich keine eigenen Lifters ("Laufende Technikrenovierung für Stud.IP") formulieren, aber die vorhandenen Lifters sind für alle Entwickler verbindlich. In dieser Anleitung wird jeweils auf die Lifter-Dokumentation verwiesen, wenn du eine Konvention bei Entwicklung beachten musst. |
