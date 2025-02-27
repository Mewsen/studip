---
title: Seitenaufbau
sidebar_label: Seitenaufbau
---

Jede Seite von Stud.IP ist auf gleiche Art und Weise aufgebaut und enthält folgende Elemente:

## Kopfzeile 

Einleitende Zeile, die eine systemweite Suche beinhaltet. Wird die Hauptnavigation durch Scrollen aus dem Sichtbereich verschoben, wird diese in kompakter Form in der Kopfzeile aufgenommen. Die Kopfzeile kann vom Betreiber erweitert werden.

## Hauptnavigation

Sie leitet jede Seite ein und ist das feststehende Navigationselement, das die Systembereiche miteinander verbindet. Die Zusammenstellung der Kopfzeile hängt von den globalen Rechten des Benutzers ab. Die Kopfzeile repräsentiert die 1. Navigationsebene.
Scopes: Scopes verbinden bestimmte Funktionen eines Hauptbereiches, etwa dem Nachrichtensystem oder alle Funktionen innerhalb von Veranstaltungen. Ein Scope besitzt einen Bereich bzw. ein Icon in der Hauptnavigation und verweist auf mehrere Funktionen. Ein Scope repräsentiert bzw. beinhaltet stets die 2. Navigationsebene.

## Sidebar 
Diese befindet sich am linken Bildschirmrand und enthält in definierter Form mehrere Widgets, etwa 
Navigation (der gewählten Funktion im gewählten Scope), Aktionen, Ansichten, Export und ggf. weitere Widgets.

### Navigationswidget 
Dieses Widget erscheint stets als erstes Widget und repräsentiert, wenn vorhanden, die 3. Navigationsebene.

### Inhaltsbereich
Hier werden sämtliche Inhalte dargestellt. Ein Inhaltsbereich wird aus Tabellen und ContentBoxen 
bzw. Eingabefeldern gebildet. Für den Inhaltsbereich existieren feste Elemente, aus denen dieser gestaltet werden muss.
Der Inhaltsbereich umfasst alle jene Inhalte, die von der jeweiligen Funktion angezeigt oder bearbeitet werden.
In diesem Bereich finden alle Objektmanipulationen und die Inhaltsanzeige statt. Entscheidend ist, dass in diesem Bereich eigentlich nur Objekte (die als solche gekennzeichnet sind, siehe später), sie manipulierende Methoden und verschiedene weitere (Meta-)Informationen zu diesen Objekten platziert werden sollten. Erklärungstexte, verweise auf andere Systemteile und andere Navigationselemente dürfen nicht in diesem Bereich erscheinen.
Für die Gestaltung sollten standardisierte grafische Elemente verwendet werden, Funktionen, die bereits in ähnlicher Weise im System vorhanden sind, müssen sich in der Bedienung daran anlehnen. Gerade im Inhaltsbereich muss es das erklärte Ziel sein, mit bekannten Elementen zu arbeiten, um dem Nutzer eine vertraute Umgebung &#8211; auch bei neuen Funktionen &#8211; zu bieten.

Einige grundsätzliche Hinweise zur Gestaltung des Inhaltsbereiches:
* Vermeiden Sie, im Inhaltsbereich der Seiten Texte frei zu platzieren. Es gibt eine Reihe von grafischen Gestaltungsmöglichkeiten, die im folgenden beschrieben werden, mit denen Sie jedwede Inhalte innerhalb des Inhaltsbereiches markieren und jeweils von anderen Objekten abgrenzen können.


## Fußzeile
Diese enthält weitere Links und Verweise, die analog zur Kopfzeile vom Betreiber erweitert werden kann.

//TODO: Screenshot einer idealtypischen Seite

Die eigentliche Seite setzt sich aus Sidebar und Inhaltsbereich zusammen. Beide Bereiche werden vom einem
Seitentitel eingeleitet. Im Gegensatz zum Design bis Stud.IP 3.5 bringt der Inhaltsbereich nun keinen eigenen Titel
(bisher teilweise als h1-Objekt gestaltet) mit.

## Seitentitel

* Der Titel muss namensgleich mit dem Eintrag in der Navigation in der Sidebar sein
* Bei Veranstaltungen wird automatisch der Name der Veranstaltung mit ausgegeben (gleiches gilt für den Einrichtungsbereich bei gewählter Einrichtung)

## Weitere Vorgaben

* Jede Seite enthält zwingend eine Sidebar.
* Jede Sidebar enthält mindestens ein Schmuckbild.
* am rechten Rand ist der Zugriff auf die Hilfe (Fragezeichen-Icon) als Abschluss des Seitentitels vorgesehen.
* Aktionen der Sidebar (zu finden im gleichnamigen Widget) werden in Dialogen ausgeführt.

Weitere Informationen zur Sidebar: siehe Abschnitt [Sidebar](seitenaufbau#sidebar)
