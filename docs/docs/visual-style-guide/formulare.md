---
title: Formulare
sidebar_label: Formulare
---


Formulare sollen in Stud.IP gemäß LifTers014 einheitlich gestaltet werden. Ein Standard Formular wird wie folgt definiert:

```php
<form class="default" ...>
    ...
</form>
```


## Allgemein
Lange Erklärungstexte am Anfang des Formulars sollten vermieden werden. Erklärungen können über Tooltips an den Elementen (siehe unten) oder ggf. Texte in der Hilfelasche realisiert werden.

## Gruppierung der Formularfelder
Formularfelder (oder auch Input-Elemente) sollen gruppiert werden, wenn sie inhaltlich oder funktional zusammenhängen, damit dieser Zusammenhang deutlich wird. Jede Gruppe sollte eine passende Überschrift haben.

```php
<form class="default" ...>
      <fieldset>
          <legend>Gruppenüberschrift 1</legend>
          ...
      </fieldset>
      <fieldset>
          <legend>Gruppenüberschrift 2</legend>
          ...
      </fieldset>
</form>
```

Auch Formulare mit lediglich einer Gruppierung sind zulässig. In Dialogen wird eine einzelne Gruppierung jedoch entfernt!

#### Ein-/Ausblenden von Gruppen

Einzelne Gruppen können aus- bzw. eingeblendet werden, indem entweder dem `fieldset` (für eine spezielle Gruppe) oder dem gesamten Formular (für alle Gruppen innerhalb) die Klasse `collapsable` gegeben wird. Dadurch wird durch einen Klick auf die `legend` des `fieldset`s die Gruppe versteckt bzw. wieder angezeigt. Soll die Gruppe bei der initialen Darstellung ausgeblendet sein, muss das `fieldset` zusätzlich mit der Klasse `collapsed` ausgezeichnet werden.

```php
<form class="default" ...>
      <fieldset>
          <legend>Gruppenüberschrift 1</legend>
          ...
      </fieldset>
      <fieldset class="collapsable collapsed">
          <legend>Gruppenüberschrift 2</legend>
          ...
      </fieldset>
</form>
```

### Labels
Generell sollte analog zu LifTers010 das HTML-Markup `<label>` verwendet werden. Beispiel:

```html
<form class="default">
       <fieldset>
           <legend>Beschriftung</legend>
           ...
           <label>Eingabe A
           <input name="eingabe_a" type="text" placeholder="Texteingabe A" required>
           </label>
           ...
       </fieldset>
 </form>
```

* Das erste Wort des Labels sollte mit einem großen Anfangsbuchstaben geschrieben werden.
* Das Label sollte nicht mit einem Doppelpunkt abgeschlossen werden.

Wording:
* Es sollen aussagekräftige Labels gewählt werden.
* Fachbegriffe sollen vermieden werden.
* Keine ganzen Sätze.

### Nicht änderbare / deaktivierte Eingabefelder

Falls in einem Formular im aktuellen Kontext ein Feld nicht geändert werden darf, so muss das Attribut `disabled` an das Eingabefeld gehängt werden. Es ist nicht zulässig, einfach nur den Text OHNE Formularelement auszugeben!

Beispiel aus lib/classes/StudipSemTreeViewAdmin.class.php
```php
<form class="default" ...>
    <fieldset>
        <legend><?= _("Bereich editieren") ?></legend>

        <label>
            <?= _("Name des Elements") ?>
            <input type="text" name="edit_name"
                <?= $this->tree->tree_data[$this->edit_item_id]['studip_object_id'])? 'disabled' : * ?>
                   value="<?= htmlReady($this->tree->tree_data[$this->edit_item_id]['name']) ?>">
        </label>
    ...
    </fieldset>
</form>
```

### Ausrichtung der Formularfelder

Schmale Formularfelder dürfen in mehreren Spalten angeordnet werden. Bei schmaleren Anzeigen brechen diese Felder bei korrekter Anwendung automatisch um.

Untereinander angeordnete Formularfelder sollten linksbündig angeordnet sein. Wenn mehrere Formularfelder eine logische Folge bilden oder aus anderen Gründen direkt zusammengehören, sollten Sie in einer Horizntalen Gruppierung (hgroup) angeordnet werden.

#### Reguläre nebeneinader angeordnete Elemente

Um Elemente bei passend großem Bildschirm nebeneinander anzuzeigen, werden diese in Spalten angeordnet. Es gibt insgesamt 6 Spalten und man Elementen eine Breite von 1 - 6 Spalten zuordnen. Dafür gibt es die Klassen col-1 bis col-5 - keine Angabe bedeutet dabei ganze Breite (enstpräche einem col-6).

Diese Elemente werden dann bei schmaleren Anzeigen automatisch unterienander angezeigt.

```php
<form class="default" ...>
    <label class="col-3">
        Vorname
        <input type="text" name="first-name">
    </label>

    <label class="col-3">
        Nachname
        <input type="text" name="last-name">
    </label>
</form>
```


#### Horizontale gruppiert angeordnete Elemente

Um Elemente in einer Zeile horizontal zu gruppieren, benötigt es ein Wrapper-Element mit der Klasse `.hgroup`. Dieses Element nimmt die gleichen Größen wie die Elemente an und verteilt den Platz innerhalb von sich selbst erstmal gleich, aber die einzelnen Elemente können auch wiederum durch die bekannten Größenangaben beeinflusst werden.

Die hgroup ist lediglich zulässig für kombinierte Eingabefelder, wie Telefonnummern, Datumsangaben etc. sowie RadioButtons mit sehr  kurzen Labels (z.B. Geschlecht: m/w/kA, Schalter: ja/nein/kA, etc.). Es dürfen keine zu großen Felder und/oder zu lange Label-Texte bei der horizontalen Gruppierung verwendet werden!

```php
<form class="default" ...>

     <!-- ... -->

        <div>
            <?= _('Geschlecht') ?>
        </div>

        <section class="hgroup">
            <label>
                <input type="radio" <? if (!$geschlecht) echo 'checked' ?> name="geschlecht" value="0">
                <?= _("unbekannt") ?>
            </label>

            <label>
                <input type="radio" <? if ($geschlecht == 1) echo "checked" ?> name="geschlecht" value="1">
                <?= _("männlich") ?>
            </label>

            <label>
                <input type="radio" name="geschlecht" <? if ($geschlecht == 2) echo "checked" ?> value="2">
                <?= _("weiblich") ?>
            </label>
        </section>
     <!-- ... -->
</form>
```

Es gibt noch eine zweite Variante, die eingesetzt werden darf, wenn es sich bei dem Titel tatsächlich um das Label eines nachfolgenden Form-Elementes handelt. Beispiel aus der Nutzerverwaltung:

```php
<label for="inaktiv">
    <?= _('inaktiv') ?>
</label>

<section class="hgroup">
    <select name="inaktiv" class="size-s" id="inaktiv">
    <? foreach(array('<=' => '>=', '=' => '=', '>' => '<', 'nie' =>_('nie')) as $i => $one): ?>
        <option value="<?= htmlready($i) ?>" <?= ($request['inaktiv'][0] === $i) ? 'selected' : * ?>>
            <?= htmlReady($one) ?>
        </option>
    <? endforeach; ?>
    </select>

    <label>
        <input name="inaktiv_tage" type="number" id="inactive" value="0">
        <?= _('Tage') ?>
    </label>
</section>
```

#### Kombinierte Variante mit col- und hgroup-Angaben

Es ist ebenfalls möglich und zulässig, horizontal gruppierte Element in Spalten einzuteilen:

```php
<label class="col-3">
    Telefonnummer
    <section class="hgroup">
        + <input type="text" size="3">
        <input type="text" maxlength="5" class="no-hint" size="5"> /
        <input type="text" maxlength="10" size="10">
    </section>
</label>

<label class="col-3">
    Fax
    <section class="hgroup">
        + <input type="text" size="3">
        <input type="text" maxlength="5" class="no-hint" size="5"> /
        <input type="text" maxlength="10" size="10">
    </section>
</label>
```

### Ausrichtung der Labels
Die Labels sollen linksbündig und oberhalb der Eingabefelder angebracht sein. Dies erleichtert die Lesbarkeit der Beschriftungen und verdeutlicht den Zusammenhang zwischen den Feldbeschriftungen und den Eingabefeldern.

Attach::formlabel2015.png

Wenn der Platz in der Vertikalen beschränkt ist, sollen die Beschriftungen linksbündig und links neben den Formularfeldern angebracht sein. Dies erhält die Lesbarkeit und spart Platz in der Vertikalen. In diesem Fall sollten die Labels so gewählt werden, dass sie sich in ihrer Länge möglichst wenig unterscheiden, damit die Lücken zwischen den Labels und den Eingabefeldern nicht zu groß werden.

Innerhalb eines Kontextes sollten die Beschriftungen einheitlich angeordnet werden.

### Placeholder/Platzhalter
Das placeholder-Attribut dient zum Befüllen von Eingabefeldern mit kurzen Hinweisen. Dieser Inhalt verschwindet, sobald ein Nutzer in das Eingabefeld klickt.
* Placeholder sollten nicht als Alternative zum Label verwendet werden.
* Placeholder sollten sparsam verwendet werden.

Beispiel für ein korrekt verwendetes placeholder-Attribut:
TODO: Screenshot


Beispiel für ein **falsches** placeholder-Attribut:
Attach::wronglabel.png


## Art der Formularfelder
Die Art der Eingabefelder soll so gewählt werden, dass man an ihr erkennen kann, welche Eingaben möglich sind. Ein Textfeld dient zur freien Eingabe von Zeichen ohne Beschränkungen (außer in der Zeichenanzahl). [Checkboxen](Checkboxen), [Radio Buttons](Visual-Style-Guide#RadioButtons) oder [Drop-Down Listen](DropDown) werden verwendet, um die Anzahl der Optionen einzuschränken oder für Einträge, wo sich Nutzer leicht vertippen.


## Größe der Formularfelder
Eingabefelder sollen groß genug sein, um typische Eingaben entgegen zunehmen, ohne dass man "über den rechten Rand hinausschreibt". Die Größe der Formularfelder soll so gewählt werden, dass sie deutlich machen, welche Eingaben dort möglich sind. Beispiel: Das Eingabefeld für die Veranstaltungsnummer sollte kürzer sein als das für den Veranstaltungstitel.

Das Stud.IP-Stylesheet schlägt standardmäßig drei Größen vor (CSS-Klassen "size-s","size-m" und "size-l"):

* size-s: 10em (gedacht für kurze Eingaben wie z.B. Zahlen)
* size-m: 48em
* size-l: 100%

```php
<form class="default" ...>
...
    <label>
        Kurze Eingabe
        <input type="text" class="size-s">
    </label>

    <label>
        Mittlere Eingabe
        <input type="text" class="size-m">
    </label>

    <label>
        Längere Eingabe
        <input type="text" class="size-l">
    </label>
...
</form>
```

Attach::formsizes2015.png

Die Voreinstellung ist "size-m". Ausnahme: Für die Input-Typen "number" und "date" ist die Voreinstellung "size-s".
```php
<form class="default narrow" ...>
    ...
</form>
```

### Schmale Formulare

Manchmal ist es notwendig, ein Formular standardmäßig besonders platzsparend anzubieten (siehe z.B. Admin > Standort > Veranstaltungshierarchie).
Dafür kann dem Formular die Klasse "narrow" hinzugefügt werden. Dies sorgt dafür, dass die einzelnen Formularelemente etwas enger zusammenrücken, um ein frühzeitiges umbrechen zu vermeiden.

Attach::narrow_form.png

## Kennzeichnung von Pflichtfeldern

```php
<form class="default" ...>
       <fieldset>
           <legend>Beschriftung</legend>
           ...
           <label>
               <span class="required">Eingabe A</span>
               <?= tooltipIcon(_('Bitte geben Sie hier nur eine Zahl ein')) ?>
               <input type="number">
           </label>
           ...
       </fieldset>
 </form>
```


Pflichtfelder müssen mit einem hochgestellten roten Stern rechts neben der Feldbeschriftung gekennzeichnet werden. Die kann in einem Label mittels `<span class="required">` im Quelltext umgesetzt werden.

### Hinweistexte zu den Formularfeldern [#Hinweistexte](#Hinweistexte)

Da die Beschriftung eines Formularfelds möglichst kurz sein sollte, ist es möglich, dass weitere Informationen oder erläuternde Hinweise zum entsprechenden Feld nötig sind. Ein erforderlicher Hinweis- oder Beschreibungstext zu einem Formularfeld wird mittels Tooltip realisiert. Der Tooltip wird über die vorhandene Logik `<?= tooltipIcon(_('...'))?>` rechts neben dem Label und ggf. hinter der Kennzeichnung eines Pflichtfeld positioniert.

Attach:formtooltip2015.png


## Formatvorgaben und Eingabevalidierung
Wenn Eingaben nur in einem bestimmten Format erfolgen dürfen, soll dies kenntlich gemacht werden, entweder durch
* entsprechende Wahl bzw. Gestaltung der Formularfelder,
* eine "intelligente" Interpretation der Eingaben (z.B. Erkennung von 15 oder 1500 als Uhrzeit 15:00 Uhr) oder
* Hinweise beim Eingabefeld [siehe Hinweistexte](#Hinweistexte).
* Verwendung entsprechender Input-Types (siehe [Eingabevalidierung](Howto/Eingabevalidierung))

Die Eingabevalidierung soll, wenn möglich, direkt nach Verlassen des jeweiligen Eingabefeldes erfolgen. Zu jedem nicht ausgefüllten Pflichtfeld bzw. zu jedem sonstwie falsch ausgefüllten Eingabefeld soll der Korrekturhinweis direkt bei dem jeweiligen Eingabefeld erfolgen, so dass die Aufmerksamkeit des Benutzers direkt auf die noch zu vorzunehmenden bzw. zu korrigierenden Eingaben gelenkt werden.

Weitere Informationen: [Eingabevalidierung](Howto/Eingabevalidierung)

## Buttons
Der Button zum Abschicken/Speichern/Übernehmen der eingegebenen Daten ("primäre Aktion") sollte linksbündig mit den Formularfeldern abschließen und sich direkt unterhalb des Formulars im `<footer>`-Element befinden. Damit wird deutlich, welche Daten durch einen Klick auf diesen Button übernommen werden.

Ein Button zum Abbrechen oder Zurücksetzen ("sekundäre Aktion") soll vermieden werden. Wenn er erforderlich ist, soll er sich visuell von dem Button für die primäre Aktion unterscheiden.

```php
<form class="default" ...>
...
    <footer>
        <?= \Studip\Button::createAccept(_("Speichern")) ?>
        <?= \Studip\Button::createCancel(_("Abbrechen")) ?>
    </footer>
</form>
```



Attach:formfooter2015.png




* TODO: Genauere Vorgaben für die Gestaltung von Buttons für sekundäre Aktionen formulieren

#### Ausnahme: Buttons bei Wizards
* wo sollten die Buttons für "zurück" und "weiter" bei mehrseitigen Formularen platziert werden
  ** zentriert ausgerichtet, wie groß der Abstand zwischen beiden Buttons?

Bei längeren Formularen (die über eine Bildschirmseite gehen): Buttons "verdoppeln", also oben und unten auf der 
Seite anzeigen z. B. "zurück" und "weiter" Buttons

* http://patternry.com/p=multiple-page-wizard/
* weitere Recherche zu Buttons bei Wizards
  ** Attach:labelsonform.pdf
  ** Quelle: http://de.slideshare.net/cjforms/labels-and-buttons-on-forms/

### Weiterführende Informationen

#### Allgemein

* Cheat Sheet For Designing Web Forms http://uxdesign.smashingmagazine.com/2011/10/07/free-download-cheat-sheet-for-designing-web-forms/ Attach:formsheet.pdf
* http://uxdesign.smashingmagazine.com/2011/11/08/extensive-guide-web-form-usability/
* http://www.formsthatwork.com/Articles
* http://www.slideshare.net/cjforms/labels-and-buttons-on-forms
* [Paper](http://www.intechopen.com/download/pdf/10814) "Simple but Crucial User Interfaces in the World Wide Web: Introducing 20 Guidelines for Usable Web Form Design"

#### Placeholder
* http://mentalized.net/journal/2010/08/05/dont_use_placeholder_text_as_labels/
* http://dev.w3.org/html5/spec/single-page.html#the-placeholder-attribute
* http://laurakalbag.com/labels-in-input-fields-arent-such-a-good-idea/


## Checkboxen

### Verwendung
Checkboxen werden verwendet, um Optionen zu aktivieren bzw. zu deaktivieren.

### Aussehen
* Checkboxen sollten möglichst untereinander angeordnet werden. Dadurch können Sie einfacher gelesen werden.
* Die Bezeichnung ist rechts vom Kästchen zu platzieren.
* Kästchen und Bezeichnung sind linksbündig untereinander anzuordnen.

### Beschriftung
Negative Beschriftungen sollten vermeiden werden:
* markierte Checkboxen aktivieren Einstellungen und deaktivieren diese nicht

```html
  <form ... >
        <fieldset>
            <legend>Beschriftung</legend>

            <fieldset>
                <legend>Checkboxengruppe</legend>
                <input class="studip_checkbox" id="cb1" type="checkbox" name="cb" value="1">
                <label for="cb1">Antwortmöglichkeit 1</label>
                <input class="studip_checkbox" id="cb2" type="checkbox" name="cb" value="2">
                <label for="cb2">Antwortmöglichkeit 2</label>
                <input class="studip_checkbox" id="cb3" type="checkbox" name="cb" value="3">
                <label for="cb3">Antwortmöglichkeit 3</label>
            </fieldset>
            ...
        </fieldset>
  </form>
```
### Reihenfolge der Checkboxen
Wenn mehrere Checkboxen auf einer Seite vorhanden sind, sollten diese in einer logischen Reihenfolge aufgelistet sein, z. B. die Optionen zuerst, die am häufigsten verwendet werden.

## Radio Buttons [#RadioButton](#RadioButton)

### Verwendung
Mit Hilfe von Radio Buttons können Nutzer genau eine Option von sich gegenseitig ausschließenden Alternativen wählen, z. B. E-Mail als Text oder in HTML versenden.

Wenn es mehr als vier/sechs Optionen gibt, ist eine [Drop-Down-Liste](Visual-Style-Guide#DropDown) die bessere Wahl.

### Verhalten
Wenn möglich, sollte eine sinnvolle Default-Option vorausgewählt sein.

```html
<form class="default" ... >
        <fieldset>
            <legend>Beschriftung</legend>

            <fieldset>
                <legend>Checkboxengruppe</legend>
                <input class="studip_checkbox" id="cb1" type="checkbox" name="cb" value="1">
                <label for="cb1">Antwortmöglichkeit 1</label>
                <input class="studip_checkbox" id="cb2" type="checkbox" name="cb" value="2">
                <label for="cb2">Antwortmöglichkeit 2</label>
                <input class="studip_checkbox" id="cb3" type="checkbox" name="cb" value="3">
                <label for="cb3">Antwortmöglichkeit 3</label>
            </fieldset>
            ...
        </fieldset>
  </form>
```

#### Aussehen
Radio Buttons sollten möglichst untereinander angeordnet werden. Dadurch können Sie leichter überflogen werden.
Die Bezeichnung sollte rechts daneben sein.

## Drop-Down Listen [#DropDown](#DropDown)

Mit Hilfe von Drop-Down Listen können Nutzer genau eine Option aus zwei oder mehreren sich gegenseitig ausschließenden Optionen wählen. Sie werden anstelle von [Radio Buttons](Visual-Style-Guide#RadioButtons) für lange Listen mit Optionen verwendet.

### Sortierung der Optionen
Die Optionen sollten aufgabenlogisch oder natürlich angeordnet werden z. B. bei Wochentagen zuerst Montag, Dienstag. Falls es keine logisch sinnvolle Reihenfolge gibt, sollten die Optionen alphabetisch (bzw. alphanumerisch) angeordnet werden.

http://uxmovement.com/forms/stop-misusing-select-menus

### Verhalten
Drop-Down Listen sollten möglichst einen voreingestellten Wert haben.

## List Boxen
### Verwendung
List Boxen können als Alternative zu einer Reihe von [Radio Buttons](Visual-Style-Guide#RadioButton), die es ermöglichen, genau eine Option aus einer Reihe von sich gegenseitig ausschließenden Optionen zu wählen. Oder als eine Alternative zu [Checkboxen](Checkboxen) dienen, die es ermöglichen, eine beliebige Anzahl von Auswahlmöglichkeiten, aus einer Liste von Optionen auszuwählen. Sie benötigen weniger Platz auf dem Bildschirm als eine Liste von Radio-Buttons oder Checkboxen.

List Boxen sollten nur sehr sparsam verwendet werden.

## Datumseingaben
* Veranstaltungstermin anlegen/bearbeiten
* Termin im Terminkalender anlegen
* Zeitbereich für Export im Terminkalender definieren
* Regelmäßige Zeit anlegen/bearbeiten
* Anzuzeigendes Datum im Belegungsplan definieren
* Ressourcenbelegung eintragen/bearbeiten
* Gültigkeitsdauer von
  * News
  * Votings
  * Evaluationen
* Anmeldezeitraum für Veranstaltungen definieren
* Eigene "Veranstaltung" in Stundenplan eintragen
* generische Datenfelder vom Typ "Datum"?
