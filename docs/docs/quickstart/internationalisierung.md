---
title: Lokalisierung (L10n)
---

Lokalisierung steht in der Softwareentwicklung für die Anpassung von Inhalten (Bücher, Filmkunst, Homepages), Prozessen, Produkten und insbesondere Computerprogrammen (Software) an die in einem bestimmten geographisch oder ethnisch umschriebenen Absatz- oder Nutzungsgebiet (Land, Region oder ethnische Gruppe) vorherrschenden lokalen sprachlichen und kulturellen Gegebenheiten.

Das englische Wort für Lokalisierung ist localization (amerikanisches/britisches Englisch) bzw. localisation (britisches Englisch) und wird in der Softwareentwicklung oft mit L10N abgekürzt. Die 10 ist die Anzahl der ausgelassenen Buchstaben. (Im Gegensatz dazu steht I18N für internationalization.)

> Wikipedia, L10N http://de.wikipedia.org/wiki/L10N

Lokalisierung wird an verschiedenen Stellen wichtig:
Die Übersetzungen der Texte im Code werden in unregelmässigen Abständen automatisch extrahiert 
und auf der Plattform [Transifex](https://www.transifex.com/projects/p/studip/) gemeinschaftlich übersetzt.


# Internationalisierung im PHP-Code

Stud.IP nutzt für die Internationalisierung das auch in vielen anderen Software-Projekten verwendete gettext-Paket.

Dabei erfolgt eine Trennung zwischen der Vorbereitung der lokalisierten Ausgabe von Texten (Internationalisierung, dies ist Aufgabe jedes Programmierers) und der eigentlichen Übersetzung der Texte mit Hilfe spezieller Tools wie z.B. "kbabel" (Lokalisierung, dies ist die Aufgabe des Maintainers einer Sprachdatei).

Die Ausgangssprache, die im Quellcode verwendet wird, ist bei Stud.IP deutsch.

Alle Strings im System dürfen nicht in den HTML-Teilen der Sourcedateien stehen, sondern müssen aus PHP-Abschnitten heraus geschrieben werden.
Die zu übersetzenden Zeichenfolgen werden im Programmcode in die spezielle Funktion `gettext()` eingeschlossen. Benutzt werden sollte nur die Kurzform, in PHP realisiert als `_()`.

```php
echo _("Meine Veranstaltungen")
```

In die zu übersetzenden Strings sollte reiner Text, keine HTML-Struktur der Seite und kein Programmcode wie z.B. Variablennamen eingeschlossen werden.

Falsch: 
```php
echo _("<tr><td>Meine Veranstaltungen</td></tr>");
```
Richtig:

```php
echo "<tr><td>" . _("Meine Veranstaltungen") . "</td></tr>";
```

Oder Richtig:

```php
printf("<tr><td>%s</td></tr>", _(" Meine Veranstaltungen "));
```

Falsch:

```php
print _("error§Keine Berechtigung!§");
```

Richtig:

```php
printf("error§%s§", _("Keine Berechtigung!"));
```

Falsch: 
```php
echo _("Sie haben $count neue Nachrichten.");
```
Auch falsch: 
```php
echo _("Sie haben ") . $count . _(" neue Nachrichten.");
```
Richtig: 
```php
printf(_("Sie haben %s neue Nachrichten."), $count);
```

Die in einen `gettext()` eingeschlossenen Strings sollten vollständige Sätze bzw. Informationsblöcke enthalten, also kein Zusammenstückeln aus einzelnen Teilstrings (siehe oben).

Schliessen sich die beiden vorangegangenen Vorschriften gegenseitig aus, weil z.B. ein Teil eines Satzes formatiert wird, so hat die letztere Regel Vorrang (der Übersetzer braucht sowieso html-Grundlagenkenntnisse).

Falsch: 
```php
echo _("Sie können diese Datei ") . "<b>" . _("nicht") . "</b>" . _(" löschen");
```
Richtig: 
```php
echo _("Sie können diese Datei <b>nicht</b> löschen");
```

Komplizierte html-Ausdrücke, wie z.B. ein klickbares Icon im Text sollten dagegen via `%s` aus dem String herausgezogen werden

Richtig:
```php
printf(_("Unter %s gelangen Sie zu Ihren Terminen."), "<a href><img src = \"pictures/icon-lit.gif\"></a>");
```

## Text-Buttons

Beschriftete und damit zu übersetzende Formular-Buttons werden generell nicht direkt in den Code eingebunden, 
sondern immer über die [Button-Api](Buttons) erzeugt, diese kümmert sich dann um die Lokalisierung.


# Internationalisierung im JS-Code


Um in den Genuss der vorhandenen Gettext-Übersetzungen auch in JavaScript-Code zu kommen, verwenden wir in Stud.IP einen speziellen Web-Service, der ausgesuchte Übersetzungen in JavaScript-Code umwandelt und diese für die von [Eli Grey geschriebene l10n.js-Bibliothek](http://purl.eligrey.com/l10n.js) verfügbar macht.

## Web-Service

Der Web-Service findet sich in jeder Stud.IP-Installation ab Version 2.0 unter der URL: `dispatch.php/localizations/{locale}`

Auf dem offiziellen Entwicklungsserver der Stud.IP Core Group können daher die deutschen Übersetzungen unter:

`http://develop.studip.de/studip/dispatch.php/localizations/de_DE`

und die englischen unter:

`http://develop.studip.de/studip/dispatch.php/localizations/en_GB`

erreicht werden.

Sollte man ein nicht verfügbares Länderkürzel angeben, liefert der Web-Service den Status-Code 406 (Not acceptable) und eine JSON-Liste mit den tatsächlich verfügbaren locales.

Dieser Web-Service wird von den folgenden Dateien (und damit auf nahezu jeder Seite) automatisch eingebunden, wobei das jeweils aktivierte locale verwendet wird:

* `lib/include/html_head.inc.php`
* `templates/layouts/base.php`
* `templates/layouts/base_without_infobox.php`

Diese Seiten binden auch automatisch die oben erwähnte JavaScript-Bibliothek [l10n.js](http://purl.eligrey.com/l10n.js) ein.

### JavaScript-API

Die offizielle JavaScript-Client-API enthält die Methode [`Object#toLocaleString`](https://developer.mozilla.org/en/Core_JavaScript_1.5_Reference/Global_Objects/Object/toLocaleString), die wie folgt definiert ist:

> Returns a string representing the object. This method is meant to be overriden by derived objects for locale-specific purposes.

Für Strings ruft diese Methode `String#toString` auf. An dieser Stelle setzt die Bibliothek an und definiert die vorhandene Methode um.

Wenn man jetzt also einen String in JavaScript übersetzen möchte, ruft man lediglich `toLocaleString` auf.


Beispiel:

```javascript
var aString = "suchen".toLocaleString();

// ergibt bei aktiviertem locale de_DE:
// aString === "suchen"

// ist hingegen en_GB aktiv:
// aString === "search"
```

Es werden lediglich die Strings übersetzt, die in der Liste des Web-Services enthalten sind. Nicht enthaltene bleiben, wie sie sind.


## Neue Strings aufnehmen

Neue Strings können einfach über die oben genannte `String.toLocaleString()`-Methode im JavaScript ausgezeichnet werden. 
Anschliessend sollte das CLI-Skript `extract-js-localizations.php` angestossen werden, welche diese Strings extrahier und in die Datei [`app/views/localizations/show.php`](https://develop.studip.de/trac/browser/trunk/app/views/localizations/show.php) schreibt. Dadurch steht der Übersetzungsmechanismus auch für diese Strings bereit.


# I18N

Seit Stud.IP 3.5 gibt es die Möglichkeit, Datenbankinhalte internationalisiert abzuspeichern. Die entsprechende Funktionalität kann mit SimpleORMap-Klassen einfach verwendet werden, ohne dass viel Code geschrieben werden muss. Im Folgenden wird anhand eines Beispieles gezeigt, wie bestimmte Felder einer SimpleORMap-Klasse internationalisiert werden können.

### Beispiel zur Internationalisierung

Im Folgenden wird die SimpleORMap-Klasse ResourceProperty um zwei internationalisierte Felder erweitert. 
ResourceProperty speichert Ressourceneigenschaften und besitzt die Felder "description" für eine Beschreibung der Eigenschaft und "display_name", um den anzuzeigenden Namen der Eigenschaft zu definieren.

#### Anpassung der SimpleORMap-Klasse

In der statischen configure-Methode werden zum assoziativen Array $config die folgenden Einträge hinzugefügt:

```php
$config['i18n_fields']['display_name'] = true;
$config['i18n_fields']['description'] = true;
```

Damit wurde die SimpleORMap-Klasse bereits auf internationalisierte Datenfelder angepasst.

#### Benutzung im View

Internationale Datenfelder werden angezeigt, indem statische Methoden der I18N-Klasse aufgerufen werden, welche die passenden HTML-Eingabefelder erzeugen.
Für das Feld "description" ist eine Textarea notwendig, für "display_name" hingegen ein einfaches Eingabefeld. 
Im View wird zur Anzeige der Felder folgender Code eingefügt:

```php
<label>
<?= _('Beschreibung')?>
<?= I18N::textarea('description', $property->description) ?>
</label>
<label>
<?= _('Angezeigter Name') ?>
<?= I18N::input('display_name', $property->display_name) ?>
</label>
```

Damit werden die internationalisierten Datenfelder in den passenden Eingabefeldern angezeigt. 
Die Methoden `I18N::textarea()` und `I18N::input()` nehmen als ersten Parameter den Namen des Feldes im HTML-Formular, der als name-Attribut zu den Eingabefeldern hinzugefügt wird und als zweiten Parameter den Wert des Feldes.

#### Verarbeiten von Eingaben

Wurde ein HTML-Formular abgesendet, in dem internationalisierte Datenfelder vorhanden sind, so werden die Inhalte der Datenfelder 
mit der Methode `Request::i18n()` statt `Request::get()` ausgelesen. Die Inhalte können dann direkt den Datenbankfeldern der SimpleORMap-Klasse zugewiesen werden. 
Im Falle der als Beispiel genutzten ResourceProperty-Klasse sieht dies so aus:

```php
//Einlesen der Datenfelder aus dem Request:
$description = Request::i18n('description');
$display_name = Request::i18n('display_name');

//Zuweisen zum ResourceProperty-Objekt:
$property->description = $description;
$property->display_name = $display_name;

//Speichern:
if ($property->isDirty()) {
$property->store();
}
```


