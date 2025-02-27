---
title: Tipps & Tricks
slug: /a11y/tips
sidebar_label: Tipps & Tricks
---

Im Folgenden werden mit Codeschnipseln Lösungen für Probleme gezeigt, die bei der
Programmierung möglichst barriererarmer Lösungen in Stud.IP auftreten können.

### Elemente tastaturbedienbar machen

HTML-Elemente, die standardmäßig nicht tastaturbedienbar sind, können über das Attribut „tabindex“ tastaturbedienbar gemacht werden. Hier als Beispiel für ein LABEL-Element:

```html
<!-- Positivbeispiel: So sollte es gemacht werden. -->
<label for="some-element" tabindex="0">
....
</label>
```

Das „tabindex“-Attribut mit dem Wert 0 fügt das LABEL-Element in die Liste mit fokussierbaren Elementen ein.

Der Wert für das „tabindex“-Attribut soll auf 0 gesetzt sein, wenn ein standardmäßig nicht fokussierbares Element fokussierbar gemacht werden soll. Für den umgekehrten Fall, bei dem ein fokussierbares Element nicht fokussierbar gemacht werden soll, beträgt der Wert des „tabindex“-Attributes -1.

Andere Werte als 0 und -1 sollten für tabindex nicht verwenden werden, weil sie in die „natürliche“ Reihenfolge der fokussierbaren Elemente eingreifen und zu unerwarteten Sprüngen führen können, wenn zwei Elemente in unterschiedlichen Seitenbereichen eine feste Nummer im Tabindex bekommen haben. Beispiel:

```html
<!-- Negativbeispiel. So sollte es nicht gemacht werden. -->
<main>
    <div tabindex="2">E1</div>
    ...
    <a href=".../e2.php" tabindex="4">E2</a>
</main>
<footer>
    <a href=".../f1.php" tabindex="1">F1</a>
    <button tabindex="3">F2</button>
</footer>
```

Die Reihenfolge der fokussierbaren Elemente wäre hier: F1, E1, F2, E2 statt der „natürlichen“ Reihenfolge E1, E2, F1, F2.

### „click“-Event auch beim Drücken der Eingabetaste auslösen

Seit der Behebung von [BIESt 106](https://gitlab.studip.de/studip/studip/-/issues/106 "Wiki: Inhaltsverzeichnis nicht per Tastaturnavigation erreichbar") gibt es ein Stück generellen JavaScript-Code, der bei allen Elementen aktiv wird, die der Klasse „enter-accessible“ angehören. Wird solch ein Element fokussiert und die Eingabetaste gedrückt, wird das click-Event ausgelöst, das normalerweise nur bei Mausklicks ausgelöst wird. Das LABEL-Element aus dem Beispiel von oben müsste so erweitert werden:

```html
<label for="some-element" class="enter-accessible" tabindex="0">
....
</label>
```

### „button“-Rolle vermeiden, stattdessen BUTTON-Elemente nehmen

Mit der ARIA-Rolle „button“ können Elemente als „Schalter“ (button) ausgezeichnet werden, die das normalerweise nicht sind. So können zum Beispiel Links, die eigentlich eine Aktion ausführen statt auf eine andere Seite zu verweisen, mit der „button“-Rolle ausgezeichnet werden, damit sie von Screenreadern als Schalter statt als Verknüpfung vorgelesen werden:

```html
<a href="#" aria-role="button">
    Hinzufügen
</a>
```

Das Problem in diesem Beispiel ist, dass das A-Element für Anker an der Stelle schon unpassend ist, da es sich nicht um einen Anker oder Link handelt. Die bessere Lösung besteht darin, im HTML direkt ein BUTTON-Element zu verwenden:

```html
<button type="button">Hinzufügen</button>
```

### Checkboxen und Radio-Buttons verstecken

Wenn man Checkboxen bzw. Radio-Buttons verstecken möchte, sollte man das nicht mit display:none oder visibility:hidden tun, denn dann sind die dazugehörigen Inhalte nicht mehr mit der Tastatur fokussierbar.

Eine browserübergreifende Lösung ist Folgendes:

```css
position: fixed;
opacity: 0;
pointer-events: none;
```

Es ist auch nicht ratsam, anstelle der regulären Checkboxen eigene Grafiken zu verwenden.


### input-Elemente: minlength-Attribut wird von Screenreadern nicht beachtet

Wenn input-Elemente mit einem minlength-Attribut ausgestattet sind, wird dieses von Screenreadern nicht beachtet. Deswegen muss mit anderen Mitteln darauf hingewiesen werden, dass eine Mindestmenge von Zeichen eingegeben werden muss.

Im Falle, dass zu wenig Zeichen eingegeben wurden, sollte auch ein barrierefreier Hinweis erfolgen, der auf dieses Problem hinweist. Dies kann mit einer aria-live Region und JavaScript gemacht werden, das beim Verlassen
des Feldes die Länge des eingegebenen Textes prüft und dann eine Meldung über die aria-live Region ausgibt.

Die Prüfung auf die Einhaltung des maxlength-Attributes kann auf die gleiche Art und Weise erfolgen.

### Barrierearme Formulare programmieren

Wenn möglich, sollte man den Formularbaukasten nutzen, der hier dokumentiert ist: https://gitlab.studip.de/studip/studip/-/wikis/StudipForm#die-form-klasse-ab-52

Mit diesem Baukasten ist das Bauen eines Formulars vielleicht etwas ungewohnt; das Ergebnis wird aber stets barrierearm sein, weil die Standardelemente dieses Baukastens barrierearm und gut zu bedienen sind.

### Akkordeonelemente barrierearm programmieren

Akkordeonelemente, wie die Auswahl der Nutzungsbedingungen im Dateibereich, sollten so programmiert werden,
dass sie auch per Tastatur genutzt werden können und ihr Inhalt von Screenreadern vorgelesen werden kann.
Üblicherweise haben solche Elemente Radio-Buttons, die durch eine Grafik ersetzt werden, um dem Stud.IP-Design
zu entsprechen. Wenn die Radio-Buttons aus dem Grund versteckt werden sollen, darf dies nicht durch die CSS-Angabe
„display: none“ geschehen, weil das Element dann nicht per Tastatur erreichbar ist. Stattdessen sollte „opacity: 0“
verwendet werden. Damit ist solch ein Radio-Button noch per Tastatur erreichbar, aber trotzdem unsichtbar.

Das Beispiel aus dem Stud.IP-Dateibereich sieht für einen Eintrag der Nutzungsbedingungen nach dem
Rendern folgendermaßen im Quellcode aus:

```html
<input type="radio" name="content_terms_of_use_id" value="SELFMADE_NONPUB" id="content_terms_of_use-SELFMADE_NONPUB"
       checked="" aria-description="(Beschreibung der Nutzungsbedingungen)">
<label for="content_terms_of_use-SELFMADE_NONPUB">
    <div class="icon">
        <img src="(Icon der Nutzungsbedingung)" alt="" class="icon-role-clickable icon-shape-own-license" width="32" height="32">
    </div>
    <div class="text">(Name der Nutzungsbedingung)</div>
    <img class="arrow icon-role-clickable icon-shape-arr_1down" src="(Aufklapp-Icon)" alt="" width="24" height="24">
    <img class="check icon-role-clickable icon-shape-check-circle" src="(Ausgewählt-Icon)" alt="" width="32" height="32">
</label>
<div class="terms_of_use_description">
    <div class="description">
        <div class="formatted-content">(Beschreibung der Nutzungsbedingung)</div>
    </div>
</div>
```

Wird ein Radio-Button ausgewählt, ist kein zusätzliches JavaScript notwendig, um die Lösung barriereärmer zu machen.
Dadurch, dass der Radio-Button die gesamte Beschreibung der Nutzungsbedingungen enthält, muss nicht auf aria-live
Regionen zurückgegriffen werden, um den Text vorlesen zu lassen.


### Barriereärmeres Drag & Drop zur Sortierung von Elementen

Soll eine Sortierung per Drag & Drop nicht nur in der GUI, sondern auch per Tastatur unter Nutzung eines Screenreaders möglich sein,
so kann mit relativ wenig Aufwand dafür gesorgt werden, dass die Sortierung barriereärmer wird.
Für eine Tabelle mit sortierbaren Zeilen sieht ein Teil der Lösung unter Nutzung von vue.js folgendermaßen aus:
````vue
<span aria-live="assertive" class="sr-only">{{ assistiveLive }}</span>


<draggable v-model="elements" handle=".drag-handle" :animation="300" @end="dropElement" tag="tbody" role="listbox">
    <tr v-for="(element, index) in elements" :key="index">
        <td>
            <a v-if="elements.length > 1" class="drag-link" role="option" tabindex="0" :title="$gettextInterpolate($gettext('Sortierelement für Element %{node}. Drücken Sie die Tasten Pfeil-nach-oben oder Pfeil-nach-unten, um dieses Element in der Liste zu verschieben.'), {node: element.name})" @keydown="keyHandler($event, index)" :ref="'draghandle-' + index">
                <span class="drag-handle"></span>
            </a>
        </td>
        ...
    </tr>
</draggable>
````
Die entscheidenden Dinge sind hier:

- `role="listbox"` am Container und `role="option"` an den einzelnen Elementen. Ohne diese Rollenzuweisung funktionieren die Pfeiltasten nur innerhalb von Formularen, weil sonst der Screenreader die Pfeiltastenaktion abfängt.
- `@keydown="keyHandler($event, index)"` Die Methode reagiert auf Drücken einer Taste (innerhalb der Methode wird weiter darauf geprüft, ob es sich um "Pfeil nach oben" (`keyCode 38`) oder "Pfeil nach unten" (`keyCode 40`) handelt), löst die Neusortierung aus und setzt einen entsprechenden Hinweistext über `assistiveLive`. Dabei sollte auch vorgelesen werden, wie die neue Position des Elements in der List ist, à la "Element ist an Position X von Y".

````vue
switch (e.keyCode) {
    case 38: // up
        e.preventDefault();
        this.decreasePosition(index);
        this.$nextTick(() => {
            this.$refs['draghandle-' + (index - 1)][0].focus();
                this.assistiveLive = this.$gettextInterpolate(
                    this.$gettext('Aktuelle Position in der Liste: %{pos} von %{listLength}.'),
                    { pos: index, listLength: this.children.length }
                );
            });
            break;
        ...
````

An der passenden Stelle muss der umschließende Bereich, in dem eine Sortierung möglich sein soll,
mit der ARIA-Rolle „application“ versehen werden, damit Screenreader wissen, dass dieser Bereich
eine eigene Tastatursteuerung hat, die vom Screenreader nicht verändert werden soll. Ansonsten kann
es vorkommen, dass die Eventbehandler für die Pfeiltasten nicht ausgeführt werden, weil der
Screenreader mit den Pfeiltasten-Events bereits etwas anderes macht, wie zum Beispiel auf das
nächste Element mit Text zu springen, um dieses vorzulesen.

Quelle: https://medium.com/salesforce-ux/4-major-patterns-for-accessible-drag-and-drop-1d43f64ebf09
