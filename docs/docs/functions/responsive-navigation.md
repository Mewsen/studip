---
id: responsive-navigation
title: Responsive Navigation
sidebar_label: Responsive Navigation
---


Ab Stud.IP 5.3 wurde die responsive Navigation komplett refaktorisiert und mir ihr auch ein Vollbildmodus für das gesamte System realisiert.

# Benutzung
In Stud.IP gibt es vier Stufen der auflösungsabhängigen Darstellung, diese sind in `resources/assets/stylesheets/scss/breakpoints.scss` (und analog in `resources/assets/stylesheets/less/breakpoints.less`) definiert und in `resources/assets/stylesheets/scss/visibility.scss` zur Steuerung von Sichtbarkeiten eingesetzt:
- 576px (small)
- 768px (medium)
- 1024px (large)
- 1280px (xlarge)
- 1600px (xxlarge)

Unterhalb einer Auflösung von 768 Pixeln wird automatisch zur responsiven Ansicht gewechselt und entsprechend die responsive Navigation verwendet.

Darüber hinaus gibt es in der Desktopansicht (>= 768 Pixel) einen Button zum Wechsel in den Vollbildmodus. Hier wird ebenfalls die responsive Navigation und die angepasste Seitenansicht mit ausgeblendeter Sidebar und Contentbar über jeder Seite eingesetzt.

# Technische Details
Die responsive Navigation wurde mit VueJS implementiert. Alle neuen Komponenten liegen unter `resources/vue/components/responsive`.

Die Komponente wird in `templates/header` eingebunden, aber nur bei Bedarf angezeigt. "Bei Bedarf" bedeutet hier:
- Die Auflösung ist kleiner als 768 Pixel, damit bekommt das HTML-Tag automatisch die Klasse `responsive-display`
- Der Vollbildmodus ist aktiv, damit bekommt das HTML-Tag automatisch die Klasse `fullscreen-mode`

Beim Einhängen der Komponente wird ads Hilfeicon in die obere blaue Leiste verschoben, ebenso wird eine Contentbar erzeugt (oder eine bestehende im DOM umgehängt), um den aktuellen Seitentitel und das Icon zum Ein-/Ausblenden der Sidebar aufzunehmen.

Das Icon zum Aktivieren des Vollbildmodus wird via MountingPortal neben das Hilfeicon eingehängt und wandert im Vollbildmodus ebenso in die blaue Topleiste.

Die Einträge der Navigation werden in der Klasse `lib/classes/ResponsiveHelper.php` erzeugt und als JSON vorgehalten. Neu ist hier, dass alle eigenen Veranstaltungen aus dem aktuellen Semester (sowie ggf. eine gerade geöffnete Veranstaltung bei Admins und Roots) ebenfalls direkt als Navigationseinträge inkl. Unternavigation verfügbar sind. 

Die Fußzeile ist ausgeblendet, deren Navigation ist als Unternavigation "Impressum & Information" im Menü aufrufbar.

Ebenso werden die Skiplinks der Seite umgebaut. Jeder Skiplink kann nun angeben, ob er im Vollbildmodus gültig ist (default `true`). Alle ungültigen Skiplinks werden beim Mounten der responsiven Navigation ausgeblendet und es werden neue eingebaut, die zur Navigation springen, den Vollbildmodus beenden oder das Icon zum Ein-/Ausblenden der Sidebar fokussieren.

# Kompakte Navigatoin und Vollbildmodus

Stud.IP ist eine Software, die sowohl auf verschiedenen Endgeräten/Gerätegrößen möglichst den vollständigen Funktionsumfang anzubieten versucht als auch für unterschiedliche Zielgruppen auf diesen verschiedenen Geräten möglichst gut bedienbar sein soll.

**Unterstützte Geräteklassen und deren Kennzeichen:**

Die Geräteklassen, auf die Stud.IP optimiert ist lassen sich wie folgt kategorisieren und entsprechend den Breakpoints im Resposiven Design.

**A. Smartphone (medium)**: Diese Geräte sind dadurch gekennzeichnet, dass
die maximale Breite sehr schmal ist (bis zu 767 Pixel bei regulärer Auflösung),
üblicherweise das Scrollen leicht möglich ist, die Seiten also durchaus lang werden dürfen, die Geräte ausschließlich per Touch, also mit dem Finger bedient werden und auf diesen Geräten keine komplexen Inhalte erstellt werden. Die übliche Display-Ausrichtung ist hochkant.

**B. Tablet/kleine Desktopgeräte (large)**: Diese Geräte sind dadurch gekennzeichnet, dass die maximale Breite begrenzt ist (bis zu. 1024 Pixel bei regulärer Auflösung),
diese in der Regel per Touch bedient werden (Mausbedienung sollte ebenfalls möglich sein), auf diesen Geräten selten komplexe Inhalte erstellt werden. Die überwiegende Display-Ausrichtung ist quer, hochkant in einigen Anwendungsfällen.

**C. Desktop (xlarge)**: Diese Geräte sind dadurch gekennzeichnet, dass die Breite mehr als 1024 Pixel aufweist und diese ganz überwiegend per Maus bedient werden. Die übliche Display-Ausrichtung ist quer.

**weitere Varianten** Es gibt weitere Varianten, die bisher jedoch nur minimale Optimierungen auf die jeweiligen Gerätegrößen enthalten und weniger auf spezifischen Geräteklassen gestaltet wurden. Diese Varianten haben bei kleineren (unter 576 Pixel/small) bzw. größeren (ab 1280 Pixel/xlarge und ab 1600 Pixel/xxlarge) Displays Breakpoints.

Siehe hierzu auch die neuen Darstellungsstufen unter https://gitlab.studip.de/studip/studip/-/wikis/Responsive-Navigation.

**Exemplarische UseCases und zugeordnete Nutzendengruppen:**

Neben verschiedenen Geräteklassen gibt es zwei wichtige Nutzendengruppen mit unterschiedlichen UseCases. Zwischen den Gruppen gibt es teils fließende Übergänge und Schnittmengen. Letztlich stellen beide Gruppen daher unterschiedliche Pole dar, für die es jeweils nun einen eigen Darstellungsmodus gibt. Beide Modus bauen dabei aufeinander auf.

**1. Erstellung und Administration komplexer Inhalte**
- Die üblichen Stud.IP-Gruppen für diesen UseCases sind **Admins** und (eingeschränkt) auch Lehrende
- Der UseCase ist geprägt dadurch, dass umfangreich Inhalte erstellt oder komplexe Inhalte bearbeitet werden
- typische genutzte Elemente sind große Tabellen (viele Elemente, viele Spalten, viele mögliche Aktionen) und umfangreiche Inhalte bestehend aus mehreren Medien-Objekten (Fließtext, Film, inaktive Elemente) die zudem in sich gegliedert sind (zB. durch ein Inhaltsverzeichnis oder Überschriften)
- Die vollständige Bedienung (insbesondere Navigation) des Systems und Nutzung von Kommunikationsfunktionen wird weiterhin erwartet und bleibt möglich
- Funktionen/Systembereiche können gewechselt, Aktionen der Sidebar ausgeführt und Kommunikationsfunktionen können aufgerufen werden
- Wichtige Anforderungen: Möglichst viel Platz für die zu bearbeitenden Elemente bei gleichzeitig noch möglicher Navigation

**2. Konsum und Interaktion mit Inhalten ohne diese zu verändern („Lernen“)**
- Die üblichen Stud.IP-Rechtestufen dieser Gruppe sind **Studierende** und (eingeschränkt) auch Lehrende
- Der UseCase ist geprägt dadurch, dass über einen längerer Zeitraum Inhalte rezipiert (Texte gelesen, Filme geschaut) werden
- typische Elemente sind umfangreiche Fließtexte, Medienobjekte (Audio oder Video) und interaktive Elemente (Fragen, Quizzes, Prüfungen)
- Die vollständige Bedienung tritt in den Hintergrund, üblicherweise wird über längere Zeit der gleiche Kontext dargestellt
- Zentrales Ziel ist: Möglichst keine (optische) Ablenkung durch Elemente des Systems, die über eine längere Zeit nicht benötigt werden (dabei auch keine Ablenkung durch Interaktionselementen, die Aufmerksamkeit binden) bei gleichzeitig möglich viel Platz für die Interaktion
- Es bleibt kein Platz für die gemeinsame Darstellung des Contents und der Bedienelemente/Navigation
- Zentrale Anforderung: Ausblenden aller störenden oder ablenkende Elemente und möglichst viel Platz für den Content


**Darstellungsmodus zur Unterstützung der beiden UsesCases**


**I. Kompakte Navigation**

Dieser Modus steht auf allen Seiten zur Verfügung, um die Nutzungsgruppe 1 (Admins/Lehrende) bei der Erstellung und Bearbeitung durchweg zu unterstützen. Der Modus ist auf alle Geräteklassen (A, B, C) optimiert.

Kennzeichen der kompakten Navigation sind:

- Die blaue Kopfzeile bleibt eingeblendet und ermöglicht Zugriff auf die vollständige Navigation („Hamburger-Menu“) in allen Geräteklassen
- Der Browser selbst ist normal sichtbar (und damit auch alle anderen Fenster/Elemente des Betriebssystems)
- Der Footer ist ausgeblendet, alle Navigationselemente des Footers sind Teil des Hamburgermenüs
- Um möglichst viel Platz für die Bedienung zu schaffen, wird die Sidebar über ein Einblendicon sichtbar bzw. wieder unsichtbar, im Default ausgeblendet

Anzumerken ist, dass bei langen Seiten auf den Geräteklassen B und C beim Herunterscrollen im normalen Modus die Responsive Navigation durch das Einblenden des Hamburger Menüs ebenfalls verwendet wird, um ein schnelles Wechseln ohne Hochscrollen weiterhin zu ermöglichen.

**II. Vollbildmodus**

Der UseCase 2 optimierte Fokusmodus kann in der Version 5.3 nur auf Seiten mit der neuen ContentBar (derzeit Courseware, Wiki und Material im OER-Campus) aktiviert werden, da davon ausgegangen werden kann, das dieser UseCase nur auf Seiten benutzt werden kann, auf denen aktiv Inhalte rezipiert werden. Der Vollbildmodus ist insbesondere auf die Klasse B (Tablets) optimiert, da davon ausgegangen wird, dass damit das Lernen und Lesen am besten funktioniert. Aber auch auf Desktop (C) ist der Modus nutzbar, wenn ggf. weniger sinnvoll.

Kennzeichen des aktivierten Vollbildmodus sind:

- Die blaue Kopfzeile wird ausgeblendet um sowohl maximalen Platz zu schaffen als auch die (versehentliche/aktive) Navigation zu unterbinden.
- Der Browser-eigene Vollbildmodus wird ebenfalls aktiviert, da davon auszugehen ist, das auch keinerlei Ablenkungen anderer Tabs oder Bedienelemente bzw. versehentliches Antippen (auf Touchgeräten, Klasse A und B) zu verhindern. Alle anderen Fenster/Elemente des Betriebssystems werden damit (soweit wir möglich) ausgeblendet.
- Die Sidebar wird ausgeblendet und kann nicht aktiviert werden
- Der Footer ist nicht sichtbar
- Die einzigen verbleibenden Bedienelemente außerhalb des Contents sind alle Elemente, die eine Bedienung innerhalb des Contents des gewählten Kontextes ermöglichen (zB. Inhaltsverzeichnis)

Die Aktivierung des Vollbildmodus ist entweder nach Aktivierung der Kompakten Navigation als Icon ob rechts in der blauen Zeilen zu sehen (und stellt so auch einen zweiten Level der reduzierten Darstellung dar) oder kann in den Seiten, die den Modus nutzen, auch aus dem Aktionsmenü aktiviert werden.
