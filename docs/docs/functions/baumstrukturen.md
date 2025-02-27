---
id: baumstrukturen
title: Baumstrukturen
sidebar_label: Baumstrukturen
---

Mit Stud.IP 5.4 wurden die alten Implementierungen für die Darstellung und Verwaltung der Studienbereiche und Einrichtungshierarchie neu implementiert und eine generische Lösung für die Abbildung von Baumstrukturen geschaffen.

## PHP
Grundlage ist das neue Interface `StudipTreeNode`. Dieses bietet generische Methoden zum Zugriff auf Bäume:
- `static getNode($id)` liefert den Knoten mit der angegebenen ID
- `hasChildNodes()` zeigt an, ob der aktuelle Knoten Kindknoten hat
- `getChildNodes()` liefert die Kindknoten des aktuellen Knotens
- `static getCourseNodes($course_id)` liefert die Knoten, denen die angegebene Veranstaltung zugeordnet ist.
- getter-Methoden für ID, Name, Beschreibung, Bild/Icon
- `countCourses($semester_id, $semclass, $withChildren)` zählt die Veranstaltungen, die diesem Knoten (oder je nach der Einstellung `withChildren` auch den Unterknoten) zugeordnet sind, gefiltert nach Semester und Kategorie
- `getCourses($semester_id, $semclass, $searchterm, $withChildren)` liefert die Veranstaltungen, die diesem Knoten (oder je nach der Einstellung `withChildren` auch den Unterknoten) zugeordnet sind, gefiltert nach Semester, Kategorie und/oder Suchbegriff
- `getAncestors` liefert eine Liste aller "Vorfahren" in der Hierarchie, in der Form `{ id, name }`

Implementierende Klassen sind aktuell `StudipStudyArea` und `RangeTreeNode`.

## Vue
Zentrale Komponente ist hier `StudipTree`. Ein Baum kann auf verschiedene Arten dargestellt werden, dafür gibt es weitere Vue-Komponenten:
- `StudipTreeTable` zeigt die Ebenen des Baums analog zum Dateibereich als Tabelle an
- `StudipTreeList` zeigt die Ebenen des Baums analog zur alten Veranstaltungssuche als Liste von Kacheln an
- `StudipTreeNode` zeigt den Baum als aufklappbare Hierarchie

`StudipTree` ist vielfältig konfigurierbar, um die Ausgabe zu steuern:
- `viewType` definiert die Art der Darstellung, entweder 'table', 'list' oder 'tree'
- `startId` ID des Startknotens zur Anzeige (die IDs sind von der Form 'Klassenname_ID')
- `title` Anzuzeigender Titel für den Baum
- `openNodes` Liste bereits offener Knoten (nur sinnvoll für Anzeige als Baum)
- `openLevels` Allgemeine Zahl geöffneter Ebenen (nur sinnvoll für Anzeige als Baum)
- `withChildren` Unterebenen anzeigen?
- `withCourses` Zugeordnete Veranstaltungen anzeigen?
- `semester` Voreingestelltes Semester im Sidebarfilter
- `semClass` Voreingestellte Kategorie im Sidebarfilter
- `breadcrumbIcon` Icon für die Brotkrumennavigation
- `itemIcon` Icon für die Unterebenenen in der Tabellenanzeige (aktuell fest auf "Ordner" verdrahtet)
- `withSearch` Zeige eine Veranstaltungssuche an?
- `withExport` Zeige einen Exportlink für vorhandene Veranstaltungen/Suchergebnisse?
- `editUrl` URL zum Bearbeitungsformular eines existierenden Knotens
- `createUrl` URL zum Anlegedialog eines neuen Knotens
- `deleteUrl` URL zum Löschen eines existierenden Knotens
- `showStructureAsNavigation` Zeige zusätzlich zur regulären Darstellung noch eine Baumstruktur als Inhaltsverzeichnis? (Nur sinnvoll bei Tabellen- oder Listendarstellung)
- `assignable` Sind die Knoten zuweisbar?

## JSON-API
Es wurden neue Routen zum Konstruieren der Baumstruktur bereitgestellt:
- `/tree-node/{id}` Hole den Knoten mit der angegebenen ID
- `/tree-node/{id}/children` Hole die Kindknoten der angegebenen ID
- `/tree-node/{id}/courseinfo` Hole Informationen über die Anzahl der zugeordneten Veranstaltungen
- `/tree-node/{id}/courses` Hole die zugeordneten Veranstaltungen
- `/tree-node/course/pathinfo/{classname}/{id}` Hole die Pfade im Baum, denen die angegebene Veranstaltung zugeordnet ist
- `/tree-node/course/details/{id}` Hole Informationen der angegebenen Veranstaltung, die einem Baumknoten zugeordnet ist (Lehrende, Semester, Termine)

## Verwendung
An jedem Element mit dem Attribut `data-studip-tree` wird automatisch eine Baumanzeige erzeugt, wenn dort entsprechend die Vue-Komponente `StudipTree` vorhanden ist.
