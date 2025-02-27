---
title: Inhaltselemente
sidebar_label: Inhaltselemente
---

Generell gilt: Alle Elemente im Inhaltsbereich müssen durch passende Objekte eingefasst werden. 
Meistens sind dies Content-Boxen (bzw. Fieldareas in Formularen) oder Tabellen. 
Texte und Eingabemöglichkeiten dürfen nicht frei auf dem (weißen) Hintergrund gesetzt werden.

## Text
Fließtext sollte in Stud.IP durch die Verwendung semantisch entsprechender HTML Attribute strukturiert werden. Dies gilt auch für die Formatierung des Textbildes. Die inhaltliche und logische Struktur des Textes wird somit auf den Quellcode übertragen. Dadurch wird der Text nicht nur lesbarer für den Entwickler, sondern auch zugänglicher für Screenreader.

### Übersicht der HTML Markups

### Überschriften
Überschriften werden seit Stud.IP 4.0 im Inhaltsbereich nicht mehr verwendet. Entsprechende Auszeichnungen von Überschriften dürfen nur noch im Content der jeweiligen Funktion (etwa Wiki-Texte, Informationsseite oder Foren-Beiträge) verwendet werden, dienen aber nicht mehr der Gliederung oder Beschreibung des Inhaltsbereiches.

### Einfache Listen und Aufzählungen
Um einfache Listen in Stud.IP darzustellen wird das `<ul>` - Markup verwendet.
Entsprechende Listenelemente werden mittels `<li>` eingefügt. Auch für Listen gilt, dass diese durch gliedernde Elemente (in der Regel Content-Boxen) eingefasst werden.

Beispiel:

```html
<ul>
<li> Eintrag 1</li>
<li> Eintrag 2</li>
...
<li> Eintrag N</li>
</ul>
```

## Audio / Video
TODO

## Lightbox
Einfache Bildergallerien können in Stud.IP erzeugt werden, indem eine Vorschau des Bilds eingebunden und verlinkt wird. Dieser Link erhält das Attribut `data-lightbox`, wodurch das verlinkte Bild anschliessend in einer Dialog-ähnlichen "Lightbox" angezeigt wird. Sollen mehrere Bilder zusammengefasst werden, so ist das Attribut `data-lightbox` aller verlinkten Bilder mit dem eindeutigen Namen der entsprechenden Lightbox zu füllen, bspw. `data-lightbox="blubber"`.

## Tabellen

Stud.IP verfügt über ein einheitliches und einfach gehaltenes Tabellenlayout, das für alle tabellarischen Darstellungen verwendet werden soll. Kernelemente sind dabei ein sehr einfach zu verwendendes CSS und eine angenehme und unaufdringliche grafische Gestaltung.

Eine Tabelle ist ungefähr so aufgebaut, wie in diesem Beispiel der Teilnehmerseite:

![image](../assets/30d74c57a521f139c0050de6d55866a7/image.png)

### Aufbau & Elemente
Jede Tabelle setzt sich zusammen aus einer Beschriftung (Label) für die gesamte Tabelle, die Kopfzeile mit den Spaltenbeschriftungen, optionale Trenner-Zeilen, um Segmente in Tabellen gegeneinander abzugrenzen, eine Fußzeile und die normalen Tabellenzeilen. Tabellen selbst sind transparent, die Hintergrundfarbe (in Stud.IP einfaches Weiß) scheint durch.

Grundsätzlich bauen sich die Spalten wie folgt auf:

* Bereich für Bulk-Aktionen: Wenn Bull-Aktionen vorgesehen sind, nehmen diese die erste Spalte auf. Die erste Spalte besteht in diesem Fall aus Chdeckboxen, in der Kopfzeile ist eine Checkbox für das Aktivieren aller Chechboxen vorzusehen.
* Icon: Das passende Icon für das Objekt
* Name/Bezeichnung: Der Name des Objektes, dass die Tabellenzeile repräsentiert. Üblicherweise ist der Name klickbar, wenn dadurch der Zugriff auf das Objekt ermöglicht wird (etwa Download im Dateibereich, Link in die Veranstaltung auf der Seite "Meine Veranstaltungen"
* weitere Spalten mit Namen des Autors/Erstellers, weitere Metadaten eines Objektes
* Aktionsspalte: Diese nimmt entweder bis zu drei Aktionselemente (in Form von Icons) auf, oder, wenn mehr als drei Aktionen möglich sind, das Aktionsmenu. Dieses hier ist hier definiert:

Der Klick auf den Header einer Spalte sortiert diese, sofern sinnvoll möglich. Ein weiterer Klick kehrt diese Sortierung um.

Aktionselemente finden sich außerdem an folgenden Stellen:

* Elemente, die sich auf die gesamte Tabelle beziehen: Oberhalb der Tabelle (Label-Zeile) in Form von Icons/Aktionsmenu.
* Elemente die sich auf eine Zeile beziehen: Pro Spalte auf der rechten Seite in Form von Icons/Aktionsmenu.
* Elemente, die sich auf ausgewählte Zeilen beziehen: Im Footer der Seite mit dazu gehörigen Checkboxen auf der linken Seite in jeder Tabellenzeile (Die Checkbox in der Tabellenkopfzeile neben den Überschriften der Spalten markiert alle sichtbaren Zeilen der Tabelle).

### CSS

Das folgende Beispiel verdeutlicht eine simple Tabelle, die nach aktuellen Vorgaben aufgebaut ist:

```html
<table class="default">
  <caption>
      <span class="actions">
         <!-- Bereich für Aktions-Icons, die die gesamte Tabelle umfassen -->
      </span>
    TutorInnen
  </caption>
  <colgroup>
      <col style="width: 20%">
      <col>
      <col style="width: 80%">
  </colgroup>
  <thead>
    <tr class="sortable">
      <th>Nr.</th>
      <th>Nachname, Vorname</th>
      <th style="text-align: right">Aktionen</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td style="text-align: right">01</td>
      <td>Kater, Cornelis</td>
      <td>
        <!-- Bereich für Aktions-Icons, die die Zeile Tabelle umfassen -->
      </td>
    </tr>
  </tbody>
  <tfoot>
    \\
    <tr>
      <td colspan="3">
        <!-- Bereich für Aktions-Icons, die die gesamte Tabelle umfassen -->\\
      </td>
    </tr>
  </tfoot>
</table>
```


Das Beispiel zeigt, das relativ wenige CSS-Stile verwendet werden müssen, um das Standard-Design von Stud.IP zu erhalten. Die Tabelle wird als Default-Klasse definiert, dadurch ergibt sich bereits der größte Teil des Aussehens.
Im Beispiel nicht gezeigt wird ist eine eigene Klassen namens `collapsable`, die in einem `<tboby>`-Element zugewiesen 
wird, wenn Tabellen in sich gegliederte (und auch zuklappbare) Bereiche aufweisen.

Weitere Hinweise:

* Jede Tabelle muss ein Label führend, das die Tabelle klar benennt
* Weitere Gestaltungselemente sollen nicht eingeführt werden (ggf. bitte Rücksprache mit der GUI-Gruppe halten)
* Tabellenbereiche können auf- und zuklappbar gestaltet werden
* Hierarchische Tabellenstrukturen sind zukünftig nicht mehr vorgesehen. Stattdessen soll das das Auswählen eines Knotens (der in der Regel einer Zeile entspricht) die nächste Ebene springen, die dann vollständig angezeigt wird (übergeordnete Ebene werden ausgeblendet). Beispiel dafür ist die Umsetzung des Dateibereiches ab der Version Stud.IP 4.0
* Dieses neue Tabellenlayout gilt lediglich für rein tabellarische Darstellungen. Systembereiche, die bisher Tabellen genutzt haben, um den allgemeinen Seitenaufbau zu beeinflussen, dürfen nicht au die diese Stile umgestellt werden. Hier empfiehlt sich, entweder das bestehe Aussehen (zunächst) beizubehalten oder ohne HTML-Tabellenstrukturen neue aufzubauen. In der Regel sind Forms hier die bessere Alternative.
