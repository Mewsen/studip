---
id: studip-form
title: Stud.IP-Formulare
sidebar_label: Stud.IP-Formulare
---

In Stud.IP werden grundsätzlich HTML-Formulare verwendet. Das Stylesheet von Stud.IP bietet die Möglichkeit, dem Formular ein gewisses Standardaussehen aufzulegen. Dadurch fühlt sich ein Formular für die Nutzer immer irgendwie stimmig und optisch passend an.

Uns ist dabei klar, dass Formulare nicht in ein starres Korsett gedrängt werden sollten. Was ist mit Drag&Drop Formularelementen? Was ist mit komplexen Multiselects, die Bilder enthalten sollen? Das sind Spezialfälle, die wir nicht vorhersehen können. Deshalb beschränkt sich das Stylesheet von Stud.IP auf die Grundelemente eines Formulars und versucht diese ansprechend zu gestalten. Für alles, was darüber hinaus geht, muss der Entwickler dann wieder selbst Hand anlegen.

# Struktur

```XML
<form class="default">
    <section>
        <legend>Grunddaten</legend>
        <label>
            Name des Objektes
            <input type="text">
        </label>
        <label>
            Typ des Objektes
            <select>
                <option>Option 1</option>
                <option>Option 2</option>
                <option>Option 3</option>
            </select>
        </label>
        <label>
            <input type="checkbox">
            Objekt sichtbar schalten
        </label>
    </section>
</form>
```

Dies ist der grundlegende Aufbau eines HTML-Formulars in Stud.IP. Maßgeblich ist dabei vor allem die Klasse (class) des Formulars. Der Klassenname sollte dabei "default" sein. Diese Klassenname gibt dem Formular überhaupt erst das Aussehen, das es hat.

Darunter kann man eine oder mehrere `fieldset`-Elemente definieren. Solche Sektionen sollen [laut HTML5](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/fieldset) verwendet werden, um mehrere Formularelemente zu gruppieren, die zusammen hängen. In Stud.IP bekommt so ein Fieldset einen blauen Rahmen und wird passend eingerückt.

Unterhalb des `fieldset`-Elementes kann es ein `legend`-Element geben, das soetwas wie eine Überschrift für die `fieldset` beinhaltet. Man kann es auch weglassen, aber es sieht meist hübscher aus mit einem `legend`-Element.

Jetzt kommen meist die eigentlichen Formularelemente, die jeweils durch ein `label`-Element umschlossen werden. Diese Labels sind enorm wichtig, um das Formular **barrierearm** zu halten. Denken Sie dabei immer an die Menschen, die zum Beispiel einen Screenreader verwenden, weil sie halb- oder ganz blind sind! Auch diese Menschen können studieren und müssen Stud.IP fast vollumfänglich benutzen können. In der Praxis ist es nicht leicht, sich in einen solchen Menschen mit eingeschränktem Sehvermögen hineinzuversetzen. Aber das müssen Sie als Programmierer auch nicht. Sie müssen nur daran denken: jedes Formularelement wie `<input>` oder `<textarea>` muss ein Label haben. Auch ein placeholder-Attribut an einem `input`-Element ist kein Ersatz für ein Label.

Der Einfachheit halber schreiben Sie die `@<label>@s` wie oben angezeigt. Nur Radio-Buttons und Checkboxen sollten vor dem Label-Text stehen. Ansonsten steht der Labeltext vor dem Input-Element. Bei komplexen Eingabemöglichkeiten wie Komboboxen oder `<input>`s in einer Tabelle kann es sinnvoll sein, Label und Input-Element voneinander räumlich zu trennen. Das ist nicht verboten. Mit dem `for`-Attribut an dem Label können Sie dennoch Label-Text und `<input>` miteinander verbinden, sodass das Formular trotzdem barrierearm bleibt.

# Besonderheiten / Goodies

In Stud.IP haben wir bisher einige besondere Funktionen eingebaut, die einfach nett sind und dem Nutzer die Verwendung des Formulars einfacher machen.

## Dateiauswähler

Es ist leider unmöglich, einen `<input type="file">`-Dateiauswähler komplett umzustylen, dass er auf allen Geräten gleich aussieht - es sei denn, man lässt ihn verschwinden. Daher haben wir das genau so gemacht! Allerdings braucht es eine weitere CSS-Klasse, um das zu erzeugen. Schreiben Sie dafür solch eine Struktur

```XML
<label class="file-upload">
    <input type="file">
    <?= _('Neues Avatarbild hochladen') ?>
</label>
```

In den meisten Fällen sollte der Dateiwähler dann recht gut aussehen und zu Stud.IP passen. Es wird ein zusätzliches Icon angezeigt und die ausgewählte Datei wird grau daneben angezeigt, damit der Nutzer sieht, ob er/sie irrtümlich eine falsche Datei ausgewählt haben könnte.

## Einklappbare Fieldsets

Nichts ist schlimmer als Unordnung. Gerade in sehr großen Formularen wird es manchmal hektisch. Die Fieldsets geben schon eine gute Ordnung vor. Aber eventuell will man ganze Fieldsets einklappen und erst zeigen, wenn das gewünscht ist. Das geht mit der zusätzlichen Klasse `collapsable`, die man entweder an das Fieldset hängen kann oder gleich an das ganze Formular, sodass es für alle Fieldsets darin gilt.

```XML
<form class="default collapsable">
    <section>
        <legend>Grunddaten</legend>
        <label>
            Name des Objektes
            <input type="text">
        </label>
    </section>
    <section>
        <legend>Erweiterte Daten</legend>
        <label>
            Typ des Objektes
            <select>
                <option>Option 1</option>
                <option>Option 2</option>
                <option>Option 3</option>
            </select>
        </label>
        <label>
            <input type="checkbox">
            Objekt sichtbar schalten
        </label>
    </section>
</form>
```

## Maximale Textlänge

Sowohl an `<textarea>` als auch an einem `<input>` kann man das Attribut `maxlength` anhängen. Der Browser beschränkt dann die Zeichenanzahl automatisch auf den angegebenen Wert. Stud.IP zeigt rechts unten des Formularelements zudem automatisch die verbleibenden Zeichen an, die einem bei der Eingabe noch bleiben. Die Anzeige der verbleibenden Zeichen kann durch die Angabe der CSS-Klasse `no-hint` unterdrückt werden.

```XML
<input type="text" maxlength="160">
```

## Horizontale Anordnung

Stud.IP zeig die `<label>` Elemente und generell fast alles im Formular untereinander an. Das hat den Vorteil, dass wenig Platz in der Breite verwendet wird und das Formular sich sogar auf Smartphones gut benutzbar ist. Der Nachteil ist allerdings, dass man jetzt viel mehr scrollen muss als zuvor. Besonders bei Radio-Buttons mit wenig Text kann das absurd aussehen und nicht gewünscht sein. Bisher bietet Stud.IP dazu lediglich die Möglichkeit, ein Container-Element wie ein `<div class="hgroup">` einzubauen. In so einer `hgroup` werden alle Kindelemente horizontal angeordnet und nicht vertikal wie sonst.

## Einfache Auswahl mehrerer Checkbox-Elemente [data-shiftcheck]

Über das Attribut `data-shiftcheck` an dem Form-Element kann angegeben werden, dass es möglich sein soll, eine Menge von Checkboxen zu markieren, indem man die erste Checkbox aktiviert/deaktiviert, die Shift-Taste gedrückt hält und die letzte zu markierende Checkbox klickt. Alle Checkboxen zwischen der ersten und der letzten werden dann auf den Status gesetzt, den die letzte Checkbox erhält.

# Weitere Möglichkeiten

Nicht mit der CSS-Klasse `form` verbunden sind folgende Möglichkeiten:

## Bestätigung einer Aktion [data-confirm]

Über das Attribut `data-confirm` an einem Link, Button oder einem Formular können Sie sich eine Bestätigung der Aktion einholen. Der Wert des Attributes sollte die Frage sein, die dem Anwender gestellt wird.

Beachten Sie, dass dies nicht die serverseitige Validierung der Eingaben ersetzen sollte. Weiterhin ist zu beachten, dass sämtliche Variablen, die in `data-confirm` gesteckt werden, mit `htmlReady()` bearbeitet werden müssen.

## Datepicker [data-date-picker, data-time-picker, data-datetime-picker]

Sie können einem `<input type="text">` die Möglichkeit geben, einen Datepicker zu bekommen, um Datumsangaben besser eingeben zu können, indem Sie dem Element das Attribut `data-date-picker` mitgeben. Analoges gilt für Timepicker (`data-time-picker`) bzw. Datetimepicker (`data-datetime-picker`).

Bedenken Sie, dass HTML5 dafür eigentlich ein `<input type="date">` vorsieht. Dieses Element wäre normalerweise die bessere Alternative. Aber dabei gibt es leider Probleme, dass es in einigen Browsern nicht unterstützt wird. Daher kann man sich noch nicht darauf verlassen, dass die Eingabe auch immer gut funktioniert. Auf keinen Fall sollten Sie eine Kombination aus dem jQuery-Datepicker oben mit `<input type="date">` probieren. Bei Browsern, die `<input type="date">` verstehen, werden die Datepicker beide auftauchen und den Nutzer komplett verwirren.

Der Wert des Attributs darf leer sein. Sie können aber auch für jedes der `date-...-picker`-Attribute ein JSON-Objekt als Wert angeben, in welcher Relation dieses Element zu einem anderen Objekt stehen soll. Es kann angegeben werden, ob der Wert des aktuellen Elements kleiner, kleiner gleich, größer gleich oder größer als der eines anderen Elements sein muss. Dies wird bei der Auswahl innerhalb des Pickers dann entsprechend berücksichtigt und die entsprechende Zeitspanne davor/danach ist nicht wählbar. Ebenso werden ungültige Werte in dem verbundenen Element angepasst.

Die entsprechenden Elemente werden durch einen CSS-Selector angegeben. Folgend ein Beispiel für den Beginn der Veranstaltungszeit, welcher zwischen Semesterstart und Semesterende liegen muss:

```XML
<label>
    <?= _('Semesterstart') ?>
    <input type="text" name="semester-start" id="semester-start"
           data-datepicker='{"<":"#semester-end"}'>
</label>

<label>
    <?= _('Semesterende') ?>
    <input type="text" name="semester-end" id="semester-end"
           data-datepicker='{">":"#semester-start"}'>
</label>

<label>
    <?= _('Vorlesungsbeginn') ?>
    <input type="text" name="lecture-start" id="lecture-start"
           data-datepicker='{">="semester-start","<":"#semester-end"}'>
</label>
```

## Proxy-Elemente [data-proxyfor]

Über das Attribut `data-proxyfor` an einer Checkbox kann ein CSS-Selector angegeben, der bestimmt, für welche anderen Checkboxen dieses Elements als "Proxy" dienen soll. So können mehrere Checkboxen über eine einzelne aktiviert oder deaktiviert werden.

## Aktivieren/Deaktivieren von Elementen [data-activates, data-deactivates]

Über das Attribut `data-activates` bzw. `data-deactivates` an einer Checkbox/Radiobox kann ein CSS-Selector angegeben werden, der bestimmt, welche anderen Element durch den Status dieses Elements aktiviert bzw. deaktiviert werden. `data-activates` kann auch an ein Select-Element gehängt werden und kann somit ein Element aktivieren sobald ein Wert ausgewählt wurde, der ungleich dem leeren String ist.

An den Elemente, die so aktiviert bzw. deaktiviert werden sollen, kann der Status noch feiner gesteuert werden über das Attribut `data-activates-condition` bzw. `data-deactivates-condition`, welche einen CSS-Selector erwarten und den Status nur dann setzen, wenn dieser Selector mindestens einen Treffer hat.

## Vergleich zweier Werte [data-must-equal]

Über das Attribut `data-must-equal` an einem Element kann sichergestellt werden, dass die Werte in zwei Elementen identisch sind (beispielsweise bei einer Passworteingabe). An das zweite Bestätigungsfeld setzt man das Attribut mit einem CSS-Selector als Wert, der das Element bestimmt, welches identisch sein muss:

```XML
<label>
    <?= _('Passwort') ?>
    <input type="password" name="password" id="password">
</label>

<label>
   <?= _('Passwort bestätigen') ?>
   <input type="password" name="password-confirm" data-must-equal="#password">
</label>
```

## Formsaver (data-confirm)

Mit dem Attribut "data-secure" können Formulare oder Formularelementen durch Anzeige einer Warnung beim Verlassen der Seite geschützt werden, wenn es ungespeicherte Änderungen gibt.

Fügen Sie das Datenattribut "data-secure" zu einem beliebigen "form"- oder "input"-Element hinzu und wenn die Seite neu geladen wird oder der umgebende Dialog geschlossen wird, erscheint ein Bestätigungsdialog.

Es gibt zwei Konfigurationsoptionen für dieses Attribut:

* always: Sichert das Element unabhängig von seinem Zustand. Wenn ein Formular immer gesichert werden soll, verwenden Sie diese Option. Wenn Sie ein Element von der Sicherheitsprüfung ausschließen möchten, setzen Sie dort den Attributwert auf "false" (aber Sie sollten die Kurzform `data-secure="false"` verwenden).

* exists: Dynamisch hinzugefügte Knoten können nicht erkannt werden und werden daher niemals berücksichtigt, wenn sich deren Inhalt geändert hat. Geben Sie einen CSS-Selektor an, der präzise alle erforderlichen Elemente identifiziert.

Diese Optionen können als json-kodiertes Array wie dieses übergeben werden:

```XML
<form data-secure='{always: false, existiert: "#foo > .bar"}'>
```

Da Sie aber wahrscheinlich nie beide Optionen gleichzeitig benötigen werden, können Sie entweder nur einen booleschen Wert an das "data-secure" Attribut zum Setzen der Option "always" oder einen anderen Nicht-Objektwert für die Option "exists" verwenden:

```XML
<form data-secure="true">
```

 ist gleichwertig mit

```XML
<form data-secure='{always: true}'>
```

und

```XML
<form data-secure="#foo .bar">
```

 ist gleichwertig mit

```XML
<form data-secure='{exists: "#foo .bar"}'>
```

# Die Form-Klasse (ab 5.2)

In Stud-IP gibt es jetzt die Form-Klasse bzw. die Klasse heißt inklusive Namespaces `\Studip\Forms\Form`. Sie ist bestens geeignet, wenn man eines oder mehrere Objekte vom Typ `SimpleORMap` (SORM) hat, und diese bearbeiten bzw. speichern will. Aber die Form-Klasse kann auch Formulare darstellen und speichern, die nichts mit SORM-Objekten zu tun haben. In den meisten Fällen will man eine Mischung haben, auch das kann die Form-Klasse tun. Die Vorteile sind dabei auf einen Blick:

* Als Programmierer:in musst Du Dir dabei in den meisten Fällen keine Gedanken zur Formvalidierung und Barrierefreiheit machen.
* Es sieht fast immer gut aus und fügt sich perfekt in das Design von Stud.IP ein.
* Die Standardfälle von Inputs lassen sich einfach behandeln.
* Und für die Spezialfälle und in Plugins kann man die Klasse entsprechend mit eigenen Input-Klassen erweitern.

## Interner Aufbau des Formulars

Ein Formular besteht aus mehreren Input-Elementen und den Strukturelementen wie einem Fieldset (die blauen Kästen um die Eingabefelder) oder einer H-Group (ein `<div class="hgroup">`, in dem alle Inputs nebeneinander bzw. horizontal angeordnet werden). Solche Strukturelemente können auch beliebig verschachtelt werden. Sehr beliebt ist es zum Beispiel, eine H-Group in ein Fieldset einzubauen.

Wenn das Form-Element dann irgendwann alle Inputs haben möchte, etwa um sie zu speichern, holt es sich das mit der Methode `getAllInputs`, mit der alle Parts rekursiv durchsucht werden nach Input-Elementen.

Jedes Input Element ist damit von einer von der abstrakten Klasse `\Studip\Forms\Input` abgeleiteten Klasse. Jedes Input Objekt kennt seinen Namen im Formular, hat eventuell sogar noch Funktionen, die aufgerufen werden beim Speichern oder für das Data-Mapping.

## Beispiele
```php
    $form = \Studip\Forms\Form::fromSORM(
        User::findCurrent(),
        [
            'without' => ['password', 'chdate', 'user_id'],
            'types' => ['lock_comment' => 'datetimepicker'],
            'legend' => _('Nutzerdaten von mir')
        ]
    )->setURL($this->url_for('mycontroller/save'));
```

Man könnte das auch anders schreiben:

```php
    $form = \Studip\Forms\Form::fromSORM(
        User::findCurrent(),
        [
            'fields' => [
                'username' => _('Anmeldekennung'),
                'vorname' => _('Vorname'),
                'nachname' => _('Nachname'),
                'email' => _('Emailadresse'),
                'lock_comment' => [
                    'label' => _('Sperrdatum'),
                    'type' => 'datetimepicker'
                ]
            ],
            'legend' => _('Nutzerdaten von mir')
        ]
    )->setURL($this->url_for('mycontroller/save'));
```
In diesem Beispiel wird der angemeldete Nutzer bearbeitet. In dem Array gibt es den Eintrag `fields`, in dem die Felder, die im Formular dargestellt werden sollen, benannt werden. In diesem Fall ist der Index gleich dem Feldnamen im SORM-Objekt aber auch dem Input-Namen im Formular bzw. im Request. Und der Value ist entweder ein Array mit Attributen oder in Kurzschreibweise einfach nur ein String, der dann dem sichtbaren Label des Feldes entspricht.

Die Form-Klasse analysiert die Datenbank und versucht, die meisten Angaben zu vervollständigen, was den `type` angeht zum Beispiel. Die Idee ist, dass man als Programmierer möglichst wenige Angaben machen muss, um ein schickes Formular zu bekommen. Aber die Spezialfälle machen es aus.

Es gibt auch noch eine objektorientierte Variante, um die Angaben zu machen, die dann so aussehen würde:

```php
        ...
        'Vorname' => \Studip\Form\TextInput::create(
            'Vorname',
            _('Vorname'),
            User::findCurrent()->vorname
        )->setRequired()
        ...
```
Diese Variante hat den Vorteil, dass die IDE weiß, was der Typ (bzw. `type`) des Formularfeldes ist, und man wie mit setRequired weiter arbeiten kann. Die Array-Notation ist zwar schlanker und übersichtlicher, aber man muss wissen, was die Parameter bedeuten. Grundsätzlich wird aus dem beispiel `type` in der Array-Schreibweise ein Objekt vom Typ `\Studip\Form\BeispielInput` - dabei ist es egal, ob die Klasse `BeispielInput` im Kern von Stud.IP vorhanden ist oder von einem Plugin bereitgestellt wird. Auf diese Weise kann ein Plugin auch eigene Formularfeldtypen mitbringen und anzeigen. So eine Klasse `BeispielInput` wäre dann abgeleitet von der abstrakten Klasse `\Studip\Form\Input`.

## Die Formularfeldtypen im Kern

Die folgenden Klassen liegen alle unter `lib/classes/forms` bzw. im PHP-Namespace `\Studip\Form`.

**TextInput**: Dies ist der gängigste Input-Typ, der einfach einem `<input type='text'>` ohne große Schnörkel entspricht. Dennoch gibt es ein paar mögliche Angaben, die man bei allen Input-Klassen machen kann:

1. Zu Erwähnen wäre da das obige setRequired (in der objektorientierten Version) bzw. `'required' => true` als Array-Parameter, um zu sagen, dass die Angabe dieses Feldes nicht leer sein darf. Eine Checkbox, die `required` ist, muss angehakt sein.
2. Der Parameter `'permission' => $GLOBALS['perm']->have_perm('admin)`, mit dem man definiert, dass dieses Formularfeld nur angezeigt und ausgewertet werden darf, sofern hier ein `true` eingegeben wird, man also die Permission dazu hat.
3. Der Parameter `if` bzw. die Methode `setIfCondition`, mit dem man definiert, dass dieses Formularfeld nur angezeigt werden soll, sofern eine Bedingung erfüllt ist. Diese Bedingung wird immer per Javascript überprüft, während das Formular ausgefüllt wird. So könnte man eine Checkbox anzeigen, und nur wenn diese Checkbox angehakt ist, tauchen weitere Formularelemente auf. Dieser Parameter `if` ist nicht dazu da, dass eine Validierung durchgeführt wird oder ein Sicherheitscheck, sondern ausschließlich zu Zwecken der Übersichtlichkeit des Formulars. In dieser Bedingung kann auch eine Javascript-Auswertung stehen wie `'if' => 'age > 18'`. Das Formular kennt im Javascript dann die Werte der anderen Formularfelder, wie sie gerade ausgefüllt worden sind, und zeigt dementsprechend die Felder an.
4. Mit dem Parameter `value` bzw. dem Aufruf im Konstruktor der `\Studip\Form\Input`-Klasse setzt man den Wert des Formularfeldes. Normalerweise wird das der Wert des SORM-Objektes sein, aber es könnte auch ein ganz anderer Wert gesetzt werden. Falls das Formular gar nichts mit einem SORM-Objekt zu tun hat, wäre das natürlich sogar notwendig. meist wird das aber nur bei einzelnen Formularfeldern gesetzt.
5. Mit dem Parameter `store` bzw. der Methode `setStoringFunction` kann man eine PHP-Funktion einsetzen, die sich dann um das Speichern der Formularwerte kümmert, wenn das Formular abgeschickt worden ist. Falls das Formular ein SORM-Element speichert, ist diese Funktion immer so gesetzt, dass der Wert der SORM-Klasse gespeichert wird. Hier müssen Sie also nichts weiter definieren. Man könnte aber hier auch eine Funktion definieren, die einen Wert zum Beispiel in die UserConfig schreibt.
6. Mit dem Parameter `mapper` bzw. der Methode `setMapper` kann man eine Funktion definieren, die den Wert aus dem Formular umwandelt, bevor dieser gespeichert wird. So könnte man aus einer schriftlichen Datumsangabe '7.3.2012' zum Beispiel einen Unix-Timestamp (Anzahl der Sekunden seit 1970) umwandeln, als der er dann in der Datenbank gespeichert wird (aber dies ist ein schlechtes Beispiel, weil der Typ datetimepicker die Umwandlung schon in Javascript macht und nur der Unix-Timestamp übertragen wird und nicht die lesbare Entsprechung des Datums). Die angegebene Mapper-Funktion bekommt als Parameter zuerst den Wert aus dem Formular übergeben und als zweiten Parameter das SORM-Objekt, sofern denn eines existiert. Oft sieht man den `mapper` Parameter in Kombination mit dem `'type' => 'no'`, dem `NoInput`. Das ist ein Eingabefeld, das dem Benutzer gar nicht angezeigt wird, aber die Mapper-Funktion schreibt beim Speichern trotzdem den Wert der Mapper-Funktion in die Datenbank wie zum Beispiel den Namen der bearbeitenden Person.

So, nun aber wirklich zu der Liste der Input-Klasse, die es im Kern so gibt (in alphabetischer Reihenfolge):

**CalculatorInput**: Dies ist streng genommen kein Eingabefeld, sondern nur ein Ausgabefeld. Hier wird eine Berechnung von Werten angegeben. Beim Bearbeiten der Ankündigung gibt dieses Feld per Javascript dem Nutzer aus, wieviele Tage zwischen Beginn der Ankündigung und deren Ende liegen, sodass man sich bei der Eingabe sicher sein kann, zum Beispiel mindestens 14 Tage dazwischen zu haben. In dem Parameter `'value' => "Math.floor((expire - date) / 86400)"` steht dann eine Javascript-Formel, die vom Formular permanent während des Ausfüllens ausgewertet wird. Hier sind einfache Angaben in Javascript machbar aber keine Steuerungsangaben wie While-Schleifen.

**CheckboxInput**: Hier wird ein `<input type='checkbox'>` dargestellt. Der `value` lautet 1 oder 0, wie er auch in der Datenbank eingetragen wird.

**DatetimepickerInput**: Dieses Feld ist eine Datums- und Zeitangabe und entspricht einem Unix-Timestamp. In Stud.IP nutzen wir fast überall Unix-Timestamps, die Anzahl der Sekunden vom 1.1.1970 Greenwich Zeit), um ein Datum in der Datenbank zu speichern. Falls in der Datenbank ein anderes Datumsformat wie ISO-Datum speichern will, sollte man die Parameter folgendermaßen setzen: `'value' => strtotime($obj['zeitfuermich'])` und  `'mapper' => function ($val) { return date('c', $val); }`.

**HiddenInput**: In diesem Feld ist zwar ein Wert im Formular vorhanden, aber man sieht nichts und kann auch nichts eingeben. Vermutlich will man den Wert lediglich nutzen, damit man ihn in anderen Formularelementen in der `if` Klausel oder mit dem CalculatorInput auswerten kann.

**I18n_formattedInput**: Mit dieser Input-Klasse wird ein WYSIWYG-Editor angezeigt - allerdings in mehreren Sprachen, sofern in der `config_local.inc.php` Datei entsprechend weitere `$CONTENT_LANGUAGES` eingetragen worden sind. Diese Klasse kümmert sich dabei um die `$CONTENT_LANGUAGES` und die Frage, ob sie eingetragen worden sind und wie viele. Falls nämlich keine weiteren `$CONTENT_LANGUAGES` eingetragen wurden, wird auch gar kein Sprachwähler angezeigt, wie es in Stud.IP üblich ist. Und wenn der WYSIWYG-Editor in dem Stud.IP ausgeschaltet ist, wird auch nur ein normales Textfeld mit Toolbar abgezeigt. Das macht diese Klasse alles automatisch. Das entsprechende Feld der SORM-Klasse **muss** ebenfalls als i18n Feld deklariert werden in der configure-Methode in dieser Form `$config['i18n_fields']['feldname'] = true;`.

**I18n_textareaInput**: Diese Klasse stellt ein `<textarea>` dar, das wie gerade eben auch eventuell einen Sprachwähler hat, sodass man den Wert in mehreren Sprachen eingeben kann. Wenn man eine SORM-Klasse bearbeitet, wird diese Klasse automatisch als `type` ausgewählt, wenn in der Datenbank das Feld den Typ `TEXT` und die SORM-Klasse in der configure-Methode sowas stehen hat wie: `$config['i18n_fields']['feldname'] = true;`

**I18n_textInput**: Diese Klasse stellt ein `<input type='text'>` dar, das wie gerade eben auch eventuell einen Sprachwähler hat, sodass man den Wert in mehreren Sprachen eingeben kann. Wenn man eine SORM-Klasse bearbeitet, wird diese Klasse automatisch als `type` ausgewählt, wenn in der Datenbank das Feld den Typ `VARCHAR` und die SORM-Klasse in der configure-Methode sowas stehen hat wie: `$config['i18n_fields']['feldname'] = true;`

**InputRow**: Dies ist eigentlich keine Input-Klasse, sondern eine Erweiterung der Klasse `\Studip\Form\Part`. Wie dem auch sei, man kann mit dieser Klasse mehrere Eingabefelder horizontal gruppieren. Die Angabe dazu würde in etwa so aussehen:

    'row' => new \Studip\Forms\InputRow(
        [
            'name' => 'feld1',
            'label' => _('Feld 1')
        ],
        [
            'name' => 'feld2',
            'label' => _('Feld 2')
        ],
        [
            'name' => 'feld3',
            'label' => _('Feld 3')
        ]
    )
Man sieht, man muss hier bei den Eingabefeldern noch den Parameter `name` mit angeben, der ansonsten der Index ist. Ansonsten kann man an den Konstruktor von `InputRow` eine beliebige Anzahl von Eingabefeldern bzw. Input-Objekten übergeben, die dann nebeneinander angezeigt werden.

**MultiselectInput**: Dies ist ein Input, mit dem man üblicherweise eine Relation eines SORM-Objektes anlegt. Besonders ist hier eigentlich nur, dass im Request an PHP bzw. im $value der Mapper-Methode und der Store-Methode ein Array drin steht anstatt eines Strings, wie es sonst üblich ist. Falls man eine SORM-Relation damit bearbeiten will, sollte man die Parameter `value` und `mapper` (oder `store`) mit setzen, damit das auch funktioniert. In SORM selbst braucht man eigentlich nur eine SimpleORMapCollection als den neuen Wert des SORM-Objektes zu setzen. Die SimpleORMap-Klasse weiß dann schon selbst, welche Objekte hinzugefügt und welche gelöscht werden müssen. Ein Beispiel aus dem Bearbeiten der Ankündigung ist folgender:

```php
    'newsroles' => [
        'permission' => $GLOBALS['perm']->have_perm('admin'),
        'label' => _('Sichtbarkeit'),
        'value' => $news->news_roles->pluck('roleid'),
        'type' => 'multiselect',
        'options' => array_map(function ($r) {
            return $r->getRolename();
        }, RolePersistence::getAllRoles()),
        'store' => function ($value, $input) {
            $news = $input->getContextObject();
            NewsRoles::update($news->id, $value);
        }
    ]
```

Über den Parameter `options` wird gesteuert, welche Auswahlmöglichkeiten der Benutzende hat.

**NewsRangesInput**: Diese Input-Klasse ist sehr spezifisch für die Ankündigungen und weist die Ankündigung den Bereichen in Stud.IP zu wie der Startseite, den Einrichtungen, Veranstaltungen und persönlichen Homepages. Vermutlich wird niemand jemals diese Klasse für etwas anderes als zum Bearbeiten von Ankündigungen verwenden können. Aber das zeigt auch, dass der Formularbaukasten auch mit sehr speziellen Formularelementen umgehen kann. Und innerhalb der NewsRangesInput gibt es immerhin eine Vue-Komponente `EditableList`, die man wiederverwenden kann.

**NoInput**: Hier gibt es nichts zu sehen! Stimmt, es wird eigentlich nur ein `<input type='hidden'>` in dem Formular platziert. Man kann dieses nutzen, um Auswertungen in Javascript für `if`-Bedingungen oder für den CalculatorInput zu ermöglichen. Und wer weiß, was man noch damit so anstellen kann?

**NumberInput**: Hiermit wird einfach eine Ganzzahl angegeben. Sinnvoll ist hier auch oft die Angabe von anderen HTML-Attributen wie min oder max, wobei da auch der RangeInput zum Einsatz kommen könnte. Aber wenn nicht, würde man diese Attribute als weitere Parameter angeben wie `'max' => 20'`.

**QuicksearchInput**: Mit diesem Eingabefeld hat man eine Quicksearch wie die PHP-Klasse Quicksearch. Der Zweck ist, dass zwar in der Datenbank (meist) eine ID eingetragen werden soll, aber die Benutzer wollen keine ID schreiben. Stattdessen geben Sie den Namen einer Veranstaltung oder einer Person oder sonstwas ein und die Quicksearch setzt dann die ID als Wert in das Formular. Damit das Ganze funktionieren kann, muss man zusätzlich den Parameter `'searchtype' => new SQLSearch(...)` oder ähnlich angeben. Also ohne den Searchtype gibt es keine Suche und damit keine Quicksearch.

**RangeInput**: Hiermit können Ganzzahlen zwischen `max` und eventuell `min` (Standard sind zwischen 1 und 10) eingegeben werden. Die Eingabe erfolgt mittels eines Schiebereglers und der aktuelle Wert steht dann daneben.

**SelectInput**: Mit dieser Klasse wird ein `<select>` im HTML verwendet. Die darin liegenden Optionen werden wie schon im MultiselectInput über den Parameter `'options' => ['value1' => _('Der erste Wert'), 'value2' => _('Der zweite Wert')]` gesteuert. Wenn in der Datenbank ein `ENUM`-Feld steht, so wird ohne weitere Angaben von `type` oder `options` ein SelectInput mit den Werten des ENUMs erzeugt.

**TextInput**: Siehe oben.

**TextareaInput**: Eine einfache Klasse zum Erzeugen einer `<textarea>`. Diese funktioniert ganz analog zu TextInput.

## Bauen einer eigenen Input-Klasse

Wenn selbst ein Formular baut, stellt man schnell fest, dass die bisherigen Input-Klassen viel abdecken, aber recht oft dann doch notwendig ist, dass man hier und da mal ein Spezialeingabefeld hat. Für diesen Fall lassen sich auch neue Input-Klassen programmieren - entweder für ein Plugin oder für den Kern von Stud.IP. Folgende Dinge sollte man dabei beachten:

- Im Wesentlichen muss die eigene Input-Klasse von der abstrakten Klasse `\Studip\Form\Input` abgeleitet sein und zumindest die Methode `render` überschreiben. Mit der Methode `render` wird die HTML-Struktur ausgegeben, die am schlussendlich im Formular stehen soll.
- Das Element wird dann eingebettet in einer Vue-Instanz. Das bedeutet, das HTML des Eingabeelementes kann Vue-Tags wie v-model verwenden und sollte das auch tun. **Es sollte der Wert des Eingabefeldes immer sowohl im HTML (per `<input type='hidden'>` oder ähnlich) stehen als auch per v-model oder anderen mechanismen an die Vue-Instanz übergeben werden.** Immer ist beides notwendig, damit das Eingabefeld der eigenen Input-Klasse voll nutzbar ist - zum Beispiel indem man mit dem `if`-Parameter anderer Eingabefelder arbeiten kann, mit dem CalvulatorInput und mit der Formular-Validierung.
- Man kann in dem HTML des Eingabefeldes daher natürlich auch Vue-Komponenten verwenden. PHP-Klassen wie Quicksearch in PHP oder jQuery sollte man hingegen meiden, wie der Teufel das Weihwasser, weil Vue im Zweifelsfall zum falschen Zeitpunkt neu gerendered wird und dann das schöne PHP oder jQuery kaputt machen. Falls man Vue-Komponenten verwendert, muss man natürlich sicher stellen, dass diese auch korrekt geladen sind.
- Wenn im Request andere Dinge als ein String (oder sowas ähnliches wie ein String wie eine Ganzzahl) drin steht, sollte man die Methode `getRequestValue` überschreiben, die dann meist sowas wie `return \Request::getArray($this->name);` ausführt. Das bedeutet, die Klasse weiß, dass sie ein Array (oder etwas ähnliches) aus dem Request erwartet. Die `mapper`-Methode kann dann danach noch verwendet werden, um die Ausgabe der Methode `getRequestValue` zu verändern. Dadurch bleibt die Input-Klasse flexibel im Einsatzzweck.

Mehr ist dazu gar nicht zu sagen. Vermutlich muss man einfach ein bisschen was ausprobieren und aus den Beispielen im Kern abschreiben.
