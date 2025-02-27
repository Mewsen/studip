---
title: Elementlisten
sidebar_label: Elementlisten
---

In Stud.IP werden verschiedenste Objekte/Elemente in Listenform ausgegeben: Personen (z.B. Veranstaltungsteilnehmer), Veranstaltungen (z.B. auf der Seite »Meine Veranstaltungen«), Einrichtungen, News, Votings, Studiengänge und vieles mehr.

Durch die konsequente Verwendung der neuen Stud.IP-Tabellen ist die Einheitlichkeit der Darstellung bereits stark verbessert worden. Einige alte Seiten müssen noch auf das neue Design umgestellt bzw. templateisiert werden (Stand August 2015)


## Darstellung
* Die einzelnen Elemente einer Liste werden in einzelnen Zeilen **untereinander** angeordnet.
* In den Zeilen einer Elementliste findet **kein Zeilenumbruch** statt.
* Jede Zeile enthält den Namen des Elements bzw. andere, möglichst wenige Informationen, die das Element identifizieren (bei Terminen z. B. Datum und Uhrzeit).
* Sofern die Elemente einander hierarchisch untergeordnet werden sollen, wird diese **Hierarchie durch Einrückungen** dargestellt
* als Design gilt das aktuelle Tabellendesign von Stud.IP (siehe dort).
* Jede Elementliste sollte **tabellarisch** aufgebaut sein. Dies erhöht die Lesbarkeit der Inhalte.
    * Zu einer Tabelle gehört ein **Tabellenkopf**, der für jede Spalte der Tabelle eine Überschrift liefert.
    * Die **horizontale Ausrichtung** innerhalb einer jeden Spalte ist abhängig von den Inhalten und deren Zweck sinnvoll zu wählen.
    * Die Ausrichtung in Tabellenkopf und Tabellenkörper sollte gleich sein.
    * Bei langen Elementlisten kann es sinnvoll sein, die Liste durch **Zwischenüberschriften** zu unterbrechen. In solchen Fällen ist der Tabellenkopf über jeder Teil-Liste zu wiederholen.
* Wenn die Elementliste für die Darstellung auf einer einzigen Seite zu lang ist, ist eine **Paginierung** vorzusehen.
    * Zum Blättern werden rechts unterhalb der Elementliste die Seiten angezeigt, auf die sich die gesamte Liste verteilt.
    * Bei einem Klick auf eine Seitennummer gelangt man auf die jeweilige Seite
    * Zusätzlich gibt es links neben den Seitennummern einen Link "zurück" (außer auf Seite 1) sowie rechts neben den Seitennummern den Link "weiter" (außer auf der letzten Seite).
    * (zu weiteren Details siehe Paginierung auf score.php)

## Aktionen
* Einzelaktionen
    * Einzelaktionen sind Aktionen, die sich auf einzelne Elemente einer Liste beziehen.
    * Bei Einzelaktionen ist zwischen Standardaktionen und erweiterten Aktionen zu unterscheiden.
    * Standardaktionen sind Aktionen, die auf die meisten Arten von Listenelementen angewendet werden können. Sie werden durch Icons (nicht durch Buttons!) in der Überschriftszeile des Listenelements ausgelöst. Standardaktionen sind
        * Löschen
        * Auf- und Zuklappen
        * Sortieren/Reihenfolge ändern
    * Erweiterte Einzelaktionen sind Aktionen, die über die Standardaktionen hinausgehen. Sie sind nur für einzelne Arten von Listenelementen anwendbar und/oder erfordern umfangreichere Formulare o.ä., die allein schon vom Platz her nicht in die Überschriftszeile passen würden. Beispiele für erweiterte Aktionen sind das Einstellen der Laufzeit von Evaluationen oder das Buchen eines Raumes für einen Termin.
    * Um erweiterte Einzelaktionen ausführen können, muss der Benutzer das jeweilige Listenelement erst aufklappen. Unter dem Listenelement öffnet sich dann ein Bereich, in dem die Interaktionselemente angezeigt werden, mit denen die Aktion ausgeführt werden kann.
    * Löschen
        * Das Löschen eines Listenelements wird durch den Klick auf ein Mülleimersymbol rechts in der Titelzeile des Listenelements durchgeführt (nicht durch einen Löschen-Button, der erst nach dem Aufklappen sichtbar wird).
    * Auf- und Zuklappen
        * Oft braucht man die Möglichkeit, ein Listenelement aufzuklappen. Dies ist regelmäßig dann der Fall, ...
            * ... wenn man zu einem Element zusätzliche Informationen einblenden möchte, die nicht in die Überschriftszeile des Listenelements passen (z.B. Informationen über Teilnehmer einer Veranstaltung)
            * ... wenn man umfangreiche Bearbeitungsmöglichkeiten bereitstellen möchte, die nicht in die Überschriftszeile des Listenelements passen (z.B. bei Umfragen oder Evaluationen)
            * ... wenn man weitere Listenelemente einblenden möchte, die dem Listenelement hierarchisch untergeordnet sind (z.B. in der Veranstaltungshierarchie)
            * ... wenn man Objekte einblenden möchte, die in dem Listenelement enthalten sind (z.B. Personen in einer Statusgruppe)
            * ... wenn man Objekte einblenden möchte, die dem Listenelement auf die andere Art zugeordnet sind (z.B. Einzeltermine eines regelmäßigen Termins)
        * Zum Auf- und Zuklappen ist ein ">"-Icon am linken Ende der Titelzeile anzuzeigen. Ein Klick darauf klappt das jeweilige Listenelement auf. Das Icon wird dabei gegen eines ausgetauscht, dessen Spitze nach unten zeigt.
        * Bei aktiviertem JavaScript sollte das Auf- und Zuklappen ohne Page Reload realisiert werden.
        * Grundsätzlich sollte es durch das Aufklappen eines Listenelements möglich sein, die Grundinformationen zu ändern, die in der Titelzeile des Listenelements enthalten sind. Dazu wird in der Titelzeile dort, wo im zugeklappten Zustand die jeweilige Information/Eigenschaft ausgegeben wird, ein entsprechendes Formularfeld angezeigt, das mit den aktuellen Werte gefüllt ist.
        * Manchmal ist es sinnvoll, alle Listenelemente auf einmal auszudrucken. Dies wird durch ein Icon realisiert, das einen Pfeil nach oben und einen nach unten zeigt. Dieses Icon wird in einer Zeile zwischen dem Tabellenkopf und dem Tabellenkörper eingefügt.
    * Sortieren/Reihenfolge ändern
        * Eine Elementliste sollte grundsätzlich sortierbar sein. Dadurch wird es den Benutzern ermöglicht, die Inhalte ihren Wünschen und Nutzungszielen entsprechend darzustellen. Von einer Sortierbarkeit kann bei hierarchischen oder geschachtelten Listen verzichtet werden.
        * Die Liste sollte nach den Kriterien sortierbar sein, die durch die Überschriften im Tabellenkopf repräsentiert sind, sofern dies sinnvoll möglich ist.
        * Beim ersten Aufruf der Liste sollte diese bereits nach einem sinnvollen Kriterium sortiert sein.
        * Nach welchem Kriterium und in welcher Richtung (auf- oder absteigend) eine Liste sortiert ist, wird durch ein kleines blaues Dreieck rechts neben der jeweiligen Überschrift im Tabellenkopf angezeigt. Eine aufsteigende Sortierung wird durch ein nach oben zeigendes Dreieck, eine absteigende Sortierung durch ein nach unten zeigendes Dreieck angezeigt.
        * Die Sortierung der Liste erfolgt durch den Klick auf die jeweilige Überschrift im Tabellenkopf oder auf das gelbe Dreieck. Ist die Liste bereits nach dem
          Kriterium sortiert, das man anklickt, so wird die Reihenfolge der Sortierung umgekehrt.
        * Die Möglichkeit des Klicks auf den Namen wird durch blaue Schrift (Standardfarbe für Links) dargestellt, die Farbe entspricht der Farbe des Dreiecks.
    * In bestimmten Fällen kann es sinnvoll sein, die Reihenfolge der Elemente manuell festzulegen (also keine Sortierung nach einem Kriterium).
        * Hat der Benutzer in seinem Brwoser JavaScript aktiviert, so sollte er die Möglichkeit haben, die Reihenfolge per Drag and Drop festzulegen.
        * (spezifizieren)
        * Wenn im Browser des Benutzers JavaScript ausgeschaltet ist, sollten gelbe Sortierpfeile im rechten Bereich der jeweiligen Zeile zur Verfügung stehen, mit denen die Reihenfolge festgelegt werden kann. Diese sind in zwei Spalten angeordnet: Die Pfeile, die nach unten zeigen, befinden sich in der linken Spalte, die Pfeile, die nach oben zeigen, in der rechten Spalte. Die oberste Zeile enthält nur einen Pfeil nach unten, die unterste Zeile nur einen pfeil nach oben.
        * Wenn die Elementliste sehr lang ist, kann es für den Benutzer schwierig werden, einzelne Listenelemente an eine weiter entfernte Position innerhalb der Liste zu befördern. In so einem Fall können dem Benutzer zusätzlich Optionsfelder und gewinkelte Pfeile zur Verfügung gestellt werden, mit denen einzelne Listenelemente ausgewählt und an eine bestimmte Stelle einsortiert werden können (Beispiel: Nutzerverwaltung in Einrichtungen).
* Sammelaktionen
    * Eine Sammelaktion ist eine Aktion, die auf mehrere Listenelemente gleichzeitig angewendet wird.
    * Das Auswählen der Listenelemente für eine Sammelaktion erfolgt mittels Checkboxes links in den Titelzeilen der Listenelemente.
    * Unterhalb der Elementliste steht eine Dropdown-Box zur Verfügung, aus der man die gewünschte Aktion auswählen kann. Mit einem Klick auf einen Button "OK" wird die Sammelaktion ausgeführt.
    * Zusätzlich stehen in der Dropdown-Box Optionen zur Veränderung der Auswahl zur Verfügung (mindestens "alle auswählen", "keine auswählen" und "Auswhl umkehren").

## Auf- und Zuklappen

* Sofern es zu einem Element mehr Informationen gibt, als man es in einer Textzeile darstellen kann, soll dies durch Auf- und Zuklappen des Elements dargestellt werden. Hierzu ist ganz links in der entsprechenden Zeile ein nach rechts weisendes Dreieck auszugeben. Ein Klick auf dieses Dreieck bewirkt, dass unterhalb des gewählten Elements die Detailinformationen eingeblendet werden, ggf. mit Möglichkeiten, diese zu bearbeiten. Die Elemente unter dem aufgeklappten Element rutschen entsprechend weiter nach unten. Das Dreieck, dessen Anklicken das Aufklappen bewirkt hat, weist nach unten, wenn das Element aufgeklappt ist. Ein weiterer Klick darauf "schließt" das aufgeklappte Element wieder. Das Auf- und Zuklappen soll zusätzlich durch Klicken auf den Namen des Elements ermöglicht werden.
*  Wie soll das Bearbeiten der Elementeigenschaften umgesetzt werden? Soll es immer identisch funktionieren? Falls nicht: Welche Abweichungen sollen erlaubt sein, und unter welchen Bedingungen sollen sie erlaubt sein?
*  Variante 1: Beim Aufklappen bleibt die Titelzeile unverändert, sämtliche Informationen (also auch die in der Titelzeile eingeblendeten) werden unterhalb der Titelzeile in Formularfelder eingeblendet und können mit einem Klick auf den Button "übernehmen" gespeichert werden. Temporär widersprechen sich also die Angaben aus der Titelzeile und dem Formularfeld, in dem man diese Information gerade ändert. (Beispiel: Ablaufplan, Gruppen/Funktionen in Einrichtungen)
*  Variante 2: Beim Aufklappen ist zunächst noch nichts bearbeitbar. Dazu muss man erst auf den Button "bearbeiten" klicken (der erst durch Aufklappen sichtbar wird). Die in der Titelzeile enthaltenen Attribute werden in der Titelzeile bearbeitbar gemacht; unterhalb dieser werden zuätzliche Informationen bearbeitbar gemacht. Ein Klick auf den Button "übernehmen" (unterhalb der bearbeitbaren Informationen) speichert alles. (Beispiel: Dateibereich, Forum)
*  Variante 3: Wie Variante 2, aber die Informationen aus der Titelzeile werden nicht in der Titelzeile selbst, sondern (wie in Variante 1) unterhalb der Titelzeile bearbeitbar gemacht. (Beispiel: Literaturverwaltung)
*  Variante 4: Durch Aufklappen werden bereits alle Informationen bearbeitbar gemacht. Die Information aus der Titelzeile wird daselbst bearbeitbar gemacht. Ein Klick auf den Button "übernehmen" unterhalb sämtlicher Informationen speichert die Änderungen und schließt das Element. (Beispiel: Einzeltermin auf Raumzeitseite)
*  Variante 5: Durch Aufklappen (oder durch Klick auf ein Bearbeiten-Icon in der Titelzeile) wird die Information aus der Titelzeile bearbeitbar gemacht und durch Klick auf den Button "übernehmen" (der sich innerhalb der Titelzeile befindet) gespeichert. Gleichzeitig wird das Element zugeklappt. (Beispiel: regelmäßige Zeit auf Raumzeitseite)
*  Variante 6: Durch Aufklappen werden alle Informationen bearbeitbar gemacht. Die Information aus der Titelzeile wird daselbst bearbeitbar gemacht, zusätzlich werden unterhalb der Titelzeile weitere Informationen bearbeitbar eingeblendet. Der Klick auf den Button "übernehmen" (unterhalb sämtlicher Informationen) speichert die Änderungen und schließt das Element. (Beispiel: Gruppierungs- und Fragenblöcke in Evaluationen)
*  Variante 7: Durch Aufklappen werden Zusatzinformationen und -eigenschaften eingeblendet und bearbeitbar gemacht und durch Klick auf den Button "übernehmen" gespeichert, wobei das Element gleichzeitig geschlossen wird. Die Information aus der Titelzeile kann hierbei jedoch nicht geändert werden. Dazu muss man auf den Button "bearbeiten" in der Titelzeile klicken. Dies führt zu einer neuen Seite, auf der man die Information aus der Titelzeile, aber auch eine Reihe weiterer Informationen/Eigenschaften des Elements bearbeiten kann. (Beispiel: Evaluationen und Evakuationsvorlagen)
*  Variante 8: Wie Variante 7, nur wird hier durch bloßes Aufklappen nichts bearbeitbar gemacht. Vielmehr muss man zum Bearbeiten sämtlicher Informationen auf den Button "bearbeiten" in der Titelzeile klicken, wodurch man auf eine andere Seite gelangt. (Beispiel: Votings)
*  Variante 9: Das Element ist nicht aufklappbar. Um die Elementeigenschaften zu ändern, klickt man auf den Button "bearbeiten" in der Titelzeile. Dieser führt auf eine neue Seite, auf der man sämtliche Eigenschaften bearbeiten kann. Ein Klick auf den Button "übernehmen" (oder auf einen Text-Link "zurück" o. ä.) führt zurück zur vorherigen Seite. (Beispiel: News, Klausuren und Übungsblätter in Vips)
*  Variante 10: In der Titelzeile befindet sich ein Bearbeiten-Icon. Ein Klick darauf verändert die Hintergrundfarbe der Titelzeile, um anzuzeigen, dass es sich im Bearbeiten-Modus befindet. Oben auf der Seite wird ein bereits bestehendes Formular (das zum Festlegen der Eigenschaften neu anzulegender Elemente verwendet wird) durch ein ähnliches Formular ersetzt, das mit den Werten des ausgewählten Elements befüllt wird, wodurch man sie bearbeiten kann. Ein Klick auf den Button "speichern" innerhalb dieses Formulars übernimmt die Änderungen, setzt die Hintergrundfarbe der Titelzeile sowie das Formular zurück. (Beispiel: Gruppen/Funktionen in Veranstaltungen, Gruppenverwaltung in Vips funktioniert ähnlich)

## Löschen

Sofern es möglich sein soll, einzelne Listenelemente zu löschen, so soll dies durch ein Mülleimer-Icon symbolisiert werden. Dieses Icon befindet sich ganz rechts in der jeweiligen Zeile. Ein Klick darauf bewirkt, dass das Element gelöscht bzw. aus dem jeweiligen abstrakten "Container" (Veranstaltung, Statusgruppe usw.) entfernt wird. Das Mülleimer-Icon steht sowohl im zu- als auch im aufgeklappten Zustand zur Verfügung.

## Sortieren

### Frei
* Sofern die Reihenfolge der Elemente dauerhaft geändert werden soll (wenn also nicht nur die momentane Darstellung von z. B. Suchergebnissen verändert werden soll), sind Interaktionselemente vorzusehen, mit denen man dies bewerkstelligen kann.
* Beispiele für sortierbare Elemente: Personen, Dateien, Forumsbeiträge, Veranstaltungen, Literatur, Termine, Themen, Ressourcen, Statusgruppen, News, Votings, Evaluationen, Nachrichten, Raumanfragen, Studienbereiche, ...
* JavaScript aktiviert
    * Drag and Drop: Hierfür ist ein Anfasser-Icon am linken Rand der jeweiligen Zeile anzuzeigen. (Befindet sich dort ein Auf- und Zuklapp-Dreieck, so wird der Anfasser direkt rechts daneben angezeigt.) Klicken und Festhalten dieses Icons bewirkt, dass man die jeweilige Zeile in gerader Linie nach oben oder unten bewegen und durch Loslassen an einer andere Stelle einsortieren kann. Dieses Drag and Drop steht sowohl im auf- als auch im zugeklappten Zustand zur Verfügung.
* JavaScript deaktiviert
    * Sortierpfeile: Anstelle der Riffelung sind gelbe Doppeldreiecke anzuzeigen. Das obere und untere Element einer sortierbaren Liste enthält nur ein Doppeldreieck, das nach unten bzw. nach oben zeigt. Alle anderen Zeilen enthalten jeweils zwei Doppeldreiecke, von denen einer nach oben, der andere nach unten zeigt. Ein Klick auf ein Doppeldreieck verschiebt das jeweilige Element um eine Stelle nach oben bzw. unten, während das vormals darüber liegende Element die Position des verschobenen Elements einnimmt.
    * Radiobuttons plus Winkelpfeile: In bestimmten Kontexten ist es mitunter erforderlich, mehrere Elemente nacheinander um eine große Anzahl von Positionen zu verschieben. Hierfür kann eine alternative Sortierfunktion angeboten werden. Bei dieser Lösung markiert man einen Eintrag mittels eines Radiobuttons (links vom Auf- und Zuklapp-Dreieck) und wählt durch Anklicken eines Icons (in Form eines gewinkelten Pfeils links des jeweiligen Radiobuttons) die Stelle, an welcher der gewählte Eintrag einsortiert werden soll.
### nach Kriterium
* Manchmal möchte man Listenelemente nach einem bestimmten kriterium sortieren (Name, Dateigröße, Datum usw.). Hierzu gelten folgende Regeln:
    * Listenelemente können nur nach Kriterien sortiert werden, deren Werte an der Oberfläche sichtbar sind. Eine Liste von Dateien sollte also zum Beispiel nicht nach Datum sortiert werden können, wenn das Datum der Datei nicht auch eingeblendet ist.
    * Das Sortieren erfolgt durch das Klicken auf eine Spaltenüberschrift, unterhalb der die Werte dieses Kriteriums für jedes Listenelement aufgeführt ist.
    * Grundsätzlich soll es möglich sein, durch mehrfaches Klicken auf eine Spaltenüberschrift die Elementliste aufsteigend und absteigend nach dem jeweiligen Kriterium zu sortieren.
### Probleme/Fragen beim Sortieren:
* Wie sortiert man innerhalb hierarchischer Gliederungen die Elemente verschiedener Ebenen (Beispiel: Gruppen/Funktionen in Veranstaltungen)?
* Wie geht man mit Paginierung um? Sortiert man die gesamte Liste oder nur die sichtbaren Elemente?


# Meldungen

Wo wird Rückmeldung angezeigt?
* Nutzer befindet sich unten auf der Seite
* da macht oben die Infomeldung anzuzeigen, keinen Sinn

Rückfrage bei Löschen von Objekten, ob wirklich gelöscht werden soll?
* derzeit unterschiedlich in Stud.IP



## Sicherheitsabfragen
* als modalen Dialog?
* teilweise werden diese nicht als Dialog, sondern auf der Seite angezeigt


Quelle: http://developer.android.com/design/patterns/confirming-acknowledging.html

## Weiterführende Links
* http://patternry.com/p=feedback-messages/
* http://www.userfocus.co.uk/articles/errormessages.html
* http://uxmag.com/articles/are-you-saying-no-when-you-could-be-saying-yes-in-your-web-forms

Buch:
Designed for Use Chapter 6 über Text Usability
