---
title: CSS
sidebar_label: CSS
---

### Interaktionselemente
Text muss noch aktualisiert werden

* Was soll anklickbar sein?
    * Navigationselemente
    * Buttons mit Text
    * Textlinks: Textlinks sollen nicht im Fließtext von Systembestandteilen (zB. in Onfoboxen) erscheinen, auch wenn dies in der Vergangenheit häufig so gemacht wurde. Textlinks erscheinen nur im Fließtext eines Feldess (Nutzereingabe oder Systemfeld) und werden durch die Formatierungsfunktionen in diesem Fall mit dem Link-Icon versehen. Nur in diesem Fall kann der Nutzer diesen Link leicht vom Fließtext unterscheiden.
    * Icons: Grundsätzlich sollen Icons nur in der blauen Variante klickbar sein (die Farbe entspricht der normalen Linkfarbe), Ausnahmen gibt es derzeit auf der Seite "Meine Veranstaltungen" und bei einigen Objekticons. Die Ausnahmen sollen Ausnahmen bleiben, dh. neue klickbare Icons sind grundsätzlich in Blau zu halten. Für Aktionen wie Anlegen, löschen oder Verschieben gibt es Zusätze (in der Regel rot), die auf die besondere Funktion dieses Icons hinweisen. (siehe unter [Icons](Visual-Style-Guide#Icons))

* Wann benutzt man zur Interaktion einen Textlink, wann einen Tab, wann einen Button mit Text und wann ein Icon?
    * Tab: Umschalten zwischen verschiedenen Ansichten/Aspekten eines Objekts
    * Textlink: Navigation zu einer anderen Seite, keine "Aktion" i.e.S.
    * Icon: Auslösen einer Aktion
    * Button: Auslösen einer Aktion
    * Wann benutzt man ein Icon und wann einen Button?

* Wie sollen Buttons benannt werden?
    * Beispiel: "übernehmen" vs. "abschicken" vs. "OK" vs. "speichern" vs. ...

* Welche Icons stehen für welche Aktionen?
    * Beispiel: gelber Doppelpfeil (sortieren? Objekte verschieben? Objekte kopieren? Weiterblättern?)
    * Müsste für alle Icons durchgegangen werden.
    * IconListe

* Grundlegendes
    * Wozu dienen Icons?
    * Wann sollen interaktive Icons, wann Buttons, wann Text-Links verwendet werden?
    * Buttons
        * zum Auslösen von Aktionen
            * im Inhaltsbereich
    * Icons
        * zum Auslösen von Aktionen
        * außerhalb des Inhaltsbereichs
        * oder dort, wo für Buttons nicht genug Platz ist (z. B. in Tabellen)
    * Textlinks
        * zum Wechsel auf andere Seiten innerhalb von Stud.IP oder aus Stud.IP heraus
        * nicht zum Auslösen von Aktionen
        * überall (im Inhaltsbereich, in Infoboxen usw.)

### Buttons

Stud.IP verwendet klar erkennbare Buttons zum Bestätigen und Abschließend von Aktionen sowie gelegentlich zur Initiierung von Hauptaktionen im System.  Haupteinsatzzweck von Buttons ist das Abschließen von Aktionen, insbesondere am Ende/Fuß eines Dialoges oder beim Löschen von Objekten.
Als Faustformel, wann ein ein Button und wann ein Icon verwendet werden kann gilt:

* Initiierung: In der Rel werden die meisten Aktionen in Stud.IP durch Icons (teilweise mit daneben gesetztem Text) initiiert. Typische Beispiele sind die Sidebar, Icons in Tabellenköpfen oder -Zeilen und sämtliche Icons in der Hauptnavigation. Dies ist einerseits durch Größeneinschränkungen begründet (Icons benötigen signifikant weniger Platz) oder das Vorhandensein einer Vielzahl an Aktionen (drei oder mehr Icons neben oder gar untereinander sind für die Usabilty besser darstellbar, als die gleiche Anzahl an Buttons). Es gibt jedoch auch Kontexte, in den Buttons zur Initiierung verwendet werden können. Dies ist insbesondere dann sinnvoll, wenn die Umgebung der Seite ausreichend Platz bietet und keine Standardelemente (Tabellen oder Content-Boxen nutzen in der Regel ausschließlich Icons) verwendet werden. Gleichzeit können Buttons verwendet werden, wenn eine Aktion besonderes Gewicht bekommt (zB. das Löschen eines Objektes).
* Bestätigung und Abschließen: Ein guter Einsatzzweck eines Buttons ist Stets das Bestätigen und Abschließen einer Aktion, etwa am Ende eines Dialoges nach dem Eingeben einer Vielzahl von Daten. Auch das Löschen, am Ende eines Dialoges oder eines Formular sowie das Abbrechen von Dialogen sind typische Einsatzzwecke eines Buttons.

Zu Beachten ist, dass ein Button stets eine Aktion als ausgeschriebenes Wort, jedoch in der Regel ohne Icon zeigt. Icons hingegen sind oft lediglich in ihrer grafischen Form zu sehen, werden jedoch häufiger (Sidebar, Aktionsmenu) auch mit Text ergänzt. Dennoch ist ein Button stets dass "gewichtigere" Interaktionselement, da er größer ist, einen Hover-Effekt bietet und auch (im Verhältnis zu Icons) durch seien große Fläche leichter zu bedienen ist. Das gilt insbesondere für Touch-Geräte.

#### Beschriftung und Labelling

Buttons werden in der Regel mit der Aktion des Namens als Substantive beschriftet (das "Abschließen", das "Abbrechen" und das "Speichern"). Substantiv-Verb-Konstruktionen sind zu vermeiden ("Speichern" wäre dem Button "Datei speichern" vorzuziehen). Ganze Sätze ("Diese Datei speichern.") sind nicht zulässig. Ideal ist also ein Button, der lediglich ein Wort enthält.
Dabei soll die Beschriftung die Funktion des Buttons möglichst klar beschreiben. Es soll erkennbar sein, was passieren wird, wenn man den auf den Button drückt.
Bei der Wortwahl sind jedoch spezifische substantivierte Verben zu verwenden. "OK" ist kein guter Button, da nicht klar erkennbar ist, was passiert. "Speichern" hingegen beschreibt einen Button korrekt.
Im Standarddesign wird die Beschriftung automatisch zentriert ausgegeben.


#### Erscheinungsbild
Buttons erscheinen in Stud.IP als weiße Buttons mit starken, dunkelblauen Rand. Buttons werden als knickbares Objekt grundsätzlich Blau eingefärbt. Rote oder Grüne bzw. anders gefärbte Buttons sind nicht erlaubt. Eine gefährliche Aktion (zB. "Löschen") muss einerseits durch geeignete Platzierung des Buttons und durch weitere Absicherungen (Warndialog) geschützt werden, nicht jedoch durch Warnfarben des Buttons.
In einigen Fällen sind noch Icons in Buttons zu sehen (Haken, X oder ähnliches). Diese Ergänzung eines Buttons durch Icons ist nicht mehr zulässig.

#### Platzierung und Ausrichtung der Buttons

Buttons dürfen nicht im Textfluss platziert werden und müssen immer freistehendend platziert werden. Idealerweise gibt es keine weiteren Objekte links und rechts eines oder mehrerer Buttons im gestaltbaren Bereich (zB. Dialog, Tabellenzeile, Content-Box).
In Formularen sind Buttons linksbündig zu setzen, in Dialogen mittig. Allerdings gibt es Ausnahmen, wenn die Funktion des Buttons eines bestimmte Position impliziert. So sollten "Zurück" und "Weiter"-Buttons innerhalb eines Dialoges entsprechend linksbündig und rechtsbündig gesetzt werden.

#### Reihenfolge mehrerer Buttons nebeneinander:
Für Buttons soll eine bestimmte Reihenfolge eingehalten werden:
1. Positiver, bestätigender Button ("Speichern", "Übernehmen", "Ja")
2. Negativer Button ("Nein")
3. Harmloser/abbrechender Button, der den Zustand nicht verändert ("Abbrechen", "Schließen")

### Verhalten
Stud.IP kennt neben aktiven Buttons auch inaktive Buttons. Diese sind ausgegraut und können nicht geklickt werden, weisen aber darauf hin, dass unter anderen Umständen dieser Button klickbar wäre (hier kann ein Info-"i"-Icon mit Tooltip daneben erklären, warum der Button nicht klickbar ist).
Sollte ein Default-Button definiert sein, der bei Tastendruck (z.B. Enter) aktiviert wird, muss dies stets ein harmloser Button ("Nein", "Abbrechen" oder "Schließen") sein. Für den Fall, dass bereits Daten in einem zugehörigen Formular erfasst wurden, gibt es (zumindest in Dialogen) eine Eigenschaft, die das Schließen des Dialoges nach Eingabe ohne eine weitere Bestätigung verhindert. Generell ist darauf zu achten, dass eine Default-Option keinen Datenverlust nach sich ziehen kann.

----

**Allgemein** \\
Nutze Bestätigungs-Buttons nach den folgenden Design-Patterns:



|      **Pattern**       |       **Commit buttons**       | 
| ---- | ---- |
| Frage-Dialog (mit Buttons) | Eine der folgenden spezifischen Bezeichnergruppen: Ja/Nein, Ja/Nein/Abbrechen, [Do it]/Abbrechen, [Do it]/[Don't do it], [Do it]/[Don't do it]/Abbrechen| 
| Auswahl-Dialoge | **Modaler Dialog:** OK/Abbrechen oder  (Do it)/Abbrechen
| |**Nicht-modaler Dialog**: Schließen-Button in der Dialogbox und der title bar|   

## Aktionsmenüs

![image](../assets/3dd1c5b5758d79e52a77165d721e1665/image.png)

Ein Aktionsmenü verkapselt eine Liste von kontextbezogenen Aktionen und kann an folgenden Stellen verwendet werden:
* Bei Aktionen für ein Element in einer Liste oder Tabelle.
    * Beispiele: Teilnehmer, Veranstaltungen, Fragebögen
* Bei Aktionen für einen Bereich, der einen Inhalt umschließt und keine eigenen Aktions-Buttons hat.
    * Beispiele: Tabellen, Gruppen von Personen, Widgets auf der Startseite

In diesem Fall steht das Aktionsmenü ganz rechts, wo sonst die Aktions-Icons einzeln aufgeführt sind. Generell sollte ein Aktionsmenü anstelle einer Auflistung von Icons eingesetzt werden, wenn mehr als drei Aktionen direkt nebeneinander stehen (dabei zählen auch inaktive bzw. ausgeblendete Aktionen mit) oder die Icons nicht selbsterklärend sind und durch Text erklärt werden sollen.

Allgemeine Richtlinien bei der Verwendung eines Aktionsmenüs:
* Die primäre Aktion eines Elements (z.B. Bearbeiten, Anzeigen, Aufklappen) ist nicht Teil des Menüs.
* Die primäre Aktion ist immer durch Klick auf das Element selbst (ggf. auch dessen Icon) zugänglich.
* Wenn das Element aufklappbar ist, ist aufklappen bzw. zuklappen immer die primäre Aktion.
* Inaktive Aktionen sollten nicht komplett versteckt, sondern nur inaktiv (grau, nicht anklickbar) angezeigt werden.
* Ist die primäre Aktion nicht verfügbar, so ist das Element nicht anklickbar, es gibt dann nur die Aktionen im Menü.
* Es dürfen bis zu zwei Icons außerhalb des Menüs (d.h. davor) platziert werden, falls diese oft verwendet werden.
* Wenn eine Spalte einer Tabelle das Aktionsmenü verwendet, sollten konsistent alle Zeilen der Tabelle dieses nutzen.
* Icons im Aktionsmenü sollten nicht verwendet werden, um den Status eines Objekts anzuzeigen.

Vorgeschlagene Reihenfolge der Aktionen im Menü, sofern die einzelnen Aktionen vorhanden sind:
* Anzeigen (oder Vorschau)
* Herunterladen
* Aktualisieren
* Bearbeiten
* Hinzufügen (oder Zuordnen)
*
* Verschieben
* Kopieren
* Exportieren
*
* Löschen (oder Austragen)

Offene Fragen:
* Sollte bei hinreichend breiten Displays oder durch eine Nutzereinstellung das Menü in einzelne Aktions-Icons aufgelöst werden?
* Soll auf Mobilgeräten bzw. "kleinen" Anzeigebreiten das Aktionsmenü auch bei drei oder weniger Aktionen auftauchen?
    * Schließt das dann auch die ggf. explizit vor dem Menü plazierten (bis zu zwei) Icons ein?
* Sollen die Aktions-Icons generell einen Hover-Effekt bekommen? Beispiel: Cliqr
