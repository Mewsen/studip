---
id: db-klassen
title: Dateibereichs-Klassen
sidebar_label: Dateibereichs-Klassen
---

# Funktionsumfang

Bei dem neuen Dateibereich ab der Version 4.0 gibt es viele Dinge zu beachten. 
Der Dateibereich ist zwar hauptsächlich wie zuvor eine einfache Ablage von Dateien in Stud.IP. 
Aber nebenbei gibt es jetzt auch viele Zusatzfeatures wie Hausaufgabenordner (zeitgesteuert oder nicht), Themenordner, Anbindung an Cloud-Services wie OwnCloud, Verlinkung von Dateien statt Kopien, massenweises Ersetzen mehrerer Dateien. Um diese Features zu ermöglichen, ist die Klassenstruktur etwas komplizierter als zuvor.

# Datenstruktur

Es gibt mehrere Entitäten, von denen die Rede ist:

| Klasse | Beschreibung |
| ---- | ---- |
| File | Eine Datei, die im Dateisystem von Stud.IP liegt wie ein PDF-Dokument oder ein Bild. |
| FileRef | Eine Repräsentation einer Datei. Was ein Nutzer in einem Ordner in Stud.IP sieht, ist in erster Linie immer ein FileRef-Objekt. Üblicherweise steckt hinter dem FileRef-Objekt auch ein File-Objekt, in dem die Datei tatsächlich steckt. Wenn man das Bild am Ende sieht, sieht man das File-Objekt. Aber aufgerufen hat man es, indem man auf ein FileRef-Objekt "geklickt" hat. Ein File-Objekt kann überdies auch mehreren FileRef-Objekten zugewiesen sein, wenn es im System mehrfach verlinkt ist. Das spart einerseits Speicherplatz, andererseits macht es das leichter, mehrere Dateien auf einmal zu verändern. |
| Folder | Der Ordner in der Datenbank. Es gibt die Tabelle folders, in der gespeichert wird, welche Ordner in der Stud.IP-Datenbank liegen. |
| Interface FolderType | Dies ist die Logik-Maschine hinter dem Ordner. Der FolderType verwaltet den Ordner und definiert, wer ihn sehen und bearbeiten kann, aber auch welche Dateien der Ordner besitzt oder momentan anzeigt. Normalerweise denken wir bei dem Begriff "Ordner" immer an den Standard-Ordner (StandardFolder), in dem wir alle Dateien sehen, die er hat, und wo alle etwas hochladen können. Aber Hausaufgabenordner (HomeworkFolder) sind spezielle Ordner, in den zwar jeder etwas hochladen kann, aber wo nur der Lehrende sehen kann, was hochgeladen worden ist. Diese Art von Logik wird immer vom FolderType verwaltet. FolderType selbst ist ein Interface und keine Klasse. Erst der HomeworkFolder ist eine echte Klasse. In der Tabelle folders wird zu jedem Ordner gespeichert, welchen folder_type er hat. Aber es gibt auch FolderTypes, die keinen passenden Eintrag in der folders-Tabelle haben wie virtuelle Ordner in der OwnCloud. Diese Ordner werden nicht in der Stud.IP-Datenbank abgespeichert, sondern nur bei Bedarf durch ein Plugin ausgelesen und als spezielle FolderTypes zurück gegeben. Das reicht, weil der Stud.IP-Code alle Aufgaben an den FolderType deligiert und möglichst nie das Folder-Objekt direkt fragt. |


Als Entwickler kann man sich also merken: Wenn es darum geht, Klassen und Objekte zu verwenden, verwendet immer FileRef und FolderType-Klassen bzw. Objekte. 
File und Folder sollten möglichst nicht benutzt werden, auch wenn das in manchen Situationen möglich wäre. 
In anderen Situationen wie bei OwnCloud-Plugins führt sowas zu Fehlern. 

**Nur wer konsequent FolderType und FileRef anspricht, umgeht diese Probleme.**
