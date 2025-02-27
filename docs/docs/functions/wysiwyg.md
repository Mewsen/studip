---
id: wysiwyg
title: Wysiwyg-Editor
sidebar_label: Wysiwyg-Editor
---

## Der Wysiwyg-Editor in Stud.IP

### Allgemeines

%rfloat margin-left=1ex% Attach:wysiwyg-demo.png
In Stud.IP gibt es auch einen Wysiwyg-Editor, der zur Eingabe von formatierten Inhalten (Text, Bilder, Links, Tabellen usw.) verwendet werden kann. Dieser Editor ersetzt HTML-Textfelder durch eine grafische Oberfläche, die gängigen Textverarbeitungssystemen ähnelt (z.B. [LibreOffice](http://www.libreoffice.org/)). Das Akronym WYSIWYG steht für "*What You See Is What You Get*", auf Deutsch: "*Was Du siehst, ist das, was Du bekommst*". Die von uns verwendete Komponente ist der [**ckeditor**](http://ckeditor.com) und kann als Alternative zur Eingabe von Stud.IP-Formatierung verwendet werden. Inhalte im System sind entweder klassische Stud.IP-Formatierung oder HTML-Inhalte aus dem Wysiwyg-Editor. Es gibt keine Vermischung: Stud.IP-Formatierung wird in mit dem Editor erstellten Inhalten nicht ausgewertet (mit Ausnahme spezieller Markup-Plugins) und HTML-Inhalte werden in Stud.IP-Formatierung nicht ausgewertet. 

### Konfiguration

%rfloat margin-left=1ex% Attach:wysiwyg-konfig.png
Der Editor kann systemweit über die Einstellung `WYSIWYG` in der globalen Konfiguration eingeschaltet werden. Dabei ist zu beachten, daß mit dem Editor erstellte Inhalte als HTML in der Datenbank landen, die beim Ausschalten des Editors zurückbleiben. Wenn der Editor systemweit eingeschaltet ist, wird er standardmäßig für alle Nutzer angeboten - diese können ihn aber für sich individuell wieder deaktivieren. Es gibt also aus Entwicklersicht keine Garantie, daß bei systemweit aktiviertem Editor auch tatsächlich alle neu erstellten Inhalte in HTML vorliegen. Daher ist die PHP-API so konstruiert, daß sie mit den verschiedenen Konstellationen weitgehend transparent umgehen kann.

### Verwendung

Wenn ein Eingabefeld den Editor verwenden soll, ist dieses mit der CSS-Klasse "`wysiwyg`" auszuzeichnen (ggf. zusätzlich zu "`add_toolbar`" für die klassische Formatierungs-Toolbar). Der Inhalt muß für das *textarea*-Element mit der Funktion `wysiwygReady()` aufbereitet werden, die analog zu `htmlReady()` funktioniert, aber ggf. Stud.IP-Formatierung vor dem Bearbeiten in HTML übersetzt.

Beispiel:
```php
<textarea class="add_toolbar wysiwyg" name="content">
    <?= wysiwygReady($content) ?>
</textarea>
```

Der ensprechende Code im Controller, der die Eingabe entgegennimmt und weiterverarbeitet, sollte die Nutzereingabe durch den *HTMLPurifier* laufen lassen. Dazu gibt es die Funktion `Studip\Markup::purifyHtml()`, die eine entsprechende Filterung vornimmt, sofern die Eingabe tatsächlich HTML ist:

Beispiel:
```php
$content = Studip\Markup::purifyHtml(Request::get('content'));
```

Die Verwendung des Editors für einzeilige Eingabefelder (d.h. `<input>`) wird derzeit allerdings nicht unterstützt.

#### Weitere Funktionen der Klasse `Studip\Markup`

In den allermeisten Fällen sollte die oben beschriebene API ausreichend sein. Für spezielle Einsatzfälle gibt es aber noch weitere Funktionen in der `Markup`-Klasse, die hier kurz beschrieben sind:

* `Studip\Markup::editorEnabled()`\\
  Diese Funktion liefert `true` zurück, wenn der Editor systemweit und auf Nutzerebene aktiviert ist.

* `Studip\Markup::isHtml($text)`\\
  Diese Funktion liefert `true` zurück, wenn der übergebene Inhalt von Stud.IP als HTML interpretiert wird.

* `Studip\Markup::markAsHtml($text)`\\
  Markiert einen Inhalt als HTML. Ist der Inhalt bereits entsprechend markiert, wird er nicht verändert.

* `Studip\Markup::purifyHtml($html)`\\
  Falls der Inhalt als HTML markiert ist, wird er mit dem *HTMLPurifier* gefiltert. Andere Inhalte werden nicht verändert.

* `Studip\Markup::markupToHtml($text, $trim = true, $mark = true)`\\
  Konvertiert Inhalte aus Stud.IP-Formatierung in HTML, damit diese z.B. im Editor bearbeitet werden können. War der Inhalt bereits HTML, wird er nur durch den *HTMLPurifier* gefiltert. Normalerweise wird das Resultat auch gleich als HTML markiert, dieses Verhalten kann aber abgeschaltet werden.

* `Studip\Markup::removeHtml($html)`\\
  Entfernt alle HTML-Elemente aus dem Inhalt, z.B. um diesen anschließend wieder ohne Wysiwyg-Editor bearbeiten zu können. Ist der Inhalt kein HTML, wird er nicht verändert.

Beispiel 1: Vordefinierte Inhalte als über den Editor bearbeitbares HTML generieren, z.B. als Vorbelegung für ein Eingabefeld mit Wysiwyg-Editor:

```php
$html = Studip\Markup::markAsHtml('<h1>' . htmlReady($title) . '</h1>');
```

Beispiel 2: Zusammenfügen von formatierten Inhalten, so daß das Resultat je nach Nutzereinstellung des Editors Text oder HTML ist (ein Beispiel dafür ist das Zusammenfügen einer in Editor erstellen Nachricht mit der Signatur des Nutzers):

```php
if (Studip\Markup::editorEnabled()) {
    $result = Studip\Markup::markupToHtml($part1) . Studip\Markup::markupToHtml($part2);
} else {
    $result = Studip\Markup::removeHtml($part1) . Studip\Markup::removeHtml($part2);
}
```

#### Javascript-API

Neben der regulären Javascript-API des ckeditor, die auch in Stud.IP verwendet werden kann (natürlich nur, sofern der Editor aktiviert ist), gibt es noch eine kleine Anzahl von Einstellungen und Hilfsfunktionen in Stud.IP:

* `STUDIP.wysiwyg_enabled`\\
  Diese Property ist `true`, wenn der Editor systemweit aktiviert ist.

* `STUDIP.editor_enabled`\\
  Diese Property ist `true`, wenn der Editor im aktuellen Kontext aktiv ist - d.h. er ist systemweit aktiviert, der Nutzer hat ihn nicht ausgeschaltet und der Editor funktioniert auf dem Client (oder glaubt zumindest, daß er es tut).

* `STUDIP.wysiwyg.isHtml(text)`\\
  Diese Funktion liefert `true` zurück, wenn der übergebene Inhalt von Stud.IP als HTML interpretiert wird.

* `STUDIP.wysiwyg.markAsHtml(text)`\\
  Markiert einen Inhalt als HTML. Ist der Inhalt bereits entsprechend markiert, wird er nicht verändert.

Beispiel:

```javascript
posting = jQuery('textarea[name=content]').val();
if (STUDIP.editor_enabled) {
    posting = STUDIP.wysiwyg.markAsHtml(posting);
}
```
