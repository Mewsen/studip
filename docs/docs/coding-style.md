---
title: Coding Style
---

### Geltungsbereich

Dieses Dokument bietet Richtlinien für die Formatierung von Code und Dokumentation für Entwickler, die an Stud.IP mitarbeiten. Die folgenden Bereiche werden vom Stud.IP Coding Standard abgedeckt: 


## PHP Dateiformatierung

Für Dateien, die nur PHP Code beinhalten ist der schliessende Tag ("?>") nicht zugelassen. Er wird von PHP nicht benötigt, und das weglassen verhindert, dass versehentlich Leerzeilen in die Antwort eingefügt werden.

**WICHTIG:** Einbeziehen von beliebigen binären Daten durch __HALT_COMPILER() ist in den PHP Dateien verboten. Das Benutzen ist nur für einige Installationsskripte erlaubt.

## Einrücken

Ein Einzug sollte aus 4 Leerzeichen bestehen. Tabulatoren sind nicht erlaubt.

## Maximale Zeilenlänge

Die Zielzeilenlänge ist 80 Zeichen. Entwickler sollten jede Zeile Ihres Codes unter 80 Zeichen halten, soweit dies möglich und praktikabel ist. Trotzdem sind längere Zeilen in einigen Fällen erlaubt. Die maximale Länge einer Zeile beträgt 120 Zeichen.

## Zeilenbegrenzung

Die Zeilenbegrenzung folgt der Unix-Textdateikonvention. Zeilen müssen mit einem einzelnen Zeilenvorschubzeichen (LF) enden. Zeilenvorschubzeichen werden duch eine 10 (dezimal) bzw. durch 0x0A (hexadezimal) dargestellt.

Beachte: Benutzen Sie nicht den Wagenrücklauf (CR &#8594; 0x0D) oder die Kombination aus Wagenrücklauf und Zeilenvorschub (CRLF &#8594; 0x0D 0x0A).


## Namenskonventionen

### Klassen

Klassennamen dürfen nur alphanumerische Zeichen enthalten. Nummern sind in Klassennamen gestattet, es wird aber in den meisten Fällen davon abgeraten.

Wenn ein Klassenname aus mehr als einem Wort besteht, muß der erste Buchstabe von jedem neuen Wort großgeschrieben werden.

Um Pseudo-Namensräume zu definieren, dürfen in Klassennamen einzelne Unterstriche verwendet werden.

Beispiel: `class Trails_Controller`

Sobald echte Namensräume verfügbar sind, müssen diese Pseudo-Namensräume entsprechend ersetzt werden.

### Dateinamen

In Dateinamen sind nur alphanumerische Zeichen ("a-zA-Z0-9"), Unterstriche ("_"), Bindestriche ("-") und Punkte (".") gestattet. Leerzeichen sind völlig verboten.

Jede Datei die PHP-Code enthält, sollte mit der Endung ".php" enden.

Dateinamen müssen den Klassennamen wie oben beschrieben entsprechen.

### Funktionen und Methoden

Methodennamen dürfen nur alphanumerische Zeichen enthalten. Unterstriche sind nicht gestattet. Ziffern sind in Funktionsnamen gestattet, aber in den meisten Fällen nicht empfohlen.

Funktions- und Methodennamen müssen immer mit einem Kleinbuchstaben anfangen. Wenn ein Methodenname aus mehr als einem Wort bestehen, muß der erste Buchstabe eines jeden Wortes großgeschrieben werden. Das wird üblicherweise "camelCase"-Formatierung genannt.

Wortreichtum wird generell befürwortet. Funktionsnamen sollten so wortreich wie möglich sein, um deren Zweck und Verhalten zu erklären.

Beispiele für Methodenamen:

```php
filterInput()

getElementById()

widgetFactory()
```

Für objekt-orientiertes Programmieren sollten Zugriffsmethoden für Instanz- oder Klassenvariablen immer mit `get` oder `set` beginnen. Wenn Design-Pattern implementiert werden sollte, sollte der Name der Methode den Konventionen des Patterns entsprechen, um das Verhalten besser zu beschreiben.

Globale Funktionen sind gestattet, aber es wird von ihnen in den meisten Fällen abgeraten. Diese Funktionen sollten in einer statischen Klasse gekapselt werden.

### Variablen

Variablennamen dürfen nur alphanumerische Zeichen und den Unterstrich enthalten. Ziffern sind in Variablen gestattet, in den meisten Fällen aber nicht empfohlen.

Wie bei Funktionsnamen (siehe oben) müssen Variablennamen immer mit einem Kleinbuchstaben anfangen.

Sprechende Bezeichner werden generell befürwortet. Variablen sollen immer so wortreich wie möglich sein, um die Daten zu beschreiben, die der Entwickler in ihnen zu speichern gedenkt. Von sehr kurzen Variablennamen wie `$i` und `$n` wird abgesehen von der Verwendung in kleinen Schleifen abgeraten. Wenn eine Schleife mehr als 20 Codezeilen enthält, sollten die Index-Variablen einen ausführlicheren Namen haben.

### Konstanten

Konstantenbezeichner können alphanumerische Zeichen und Unterstriche enthalten.

Alle Buchstaben, die in Konstantenname verwendet werden, müssen großgeschrieben werden. Wörter in einem Konstantennamen müssen durch Unterstriche getrennt werden.

Beispiel: `EMBED_SUPPRESS_EMBED_EXCEPTION` ist gestattet,`EMBED_SUPPRESSEMBEDEXCEPTION` jedoch nicht.

Konstanten müssen als Klassenkonstanten (Schlüsselwort "const") definiert werden. Die Definition von Konstanten mit der `define` Funktion im globalen Bereich ist gestattet, jedoch wird davon stark abgeraten.


## PHP Code-Abgrenzung

PHP Code muß immer mit der kompletten Form des Standard-PHP Tags abgegrenzt sein:

```php
<?php

?>
```

Kurze Tags sind nur in Templates erlaubt. Für Dateien die nur PHP Code enthalten, darf das schließende Tag nie angegeben werden.

## Strings
### String-Literale

Bei String-Literalen, wenn ein String also keine Variablen enthält, sollte immer das Apostroph "'" (single quote) verwendet werden um den String abzugrenzen:

```php
$aString = 'Example String';
```


## String-Literale mit Apostrophen

Wenn ein String-Literal selbst Apostrophe enthält, ist es gestattet den String mit Anführungszeichen (double quotes)  abzugrenzen. Das ist speziell für SQL-Anweisungen nützlich:

```php
$sql = "SELECT `id`, `name` from `people` "
     . "WHERE `name`='Fred' OR `name`='Susan'";
```

Diese Syntax ist gegenüber dem Schützen des Apostrophs durch "\'" aus Gründen der besseren Lesbarkeit zu bevorzugen.

### Variable Substitution

Variable substitution is permitted using either of these forms:

```php
$greeting = "Hello $name, welcome back!";

$greeting = "Hello {$name}, welcome back!";
```



For consistency, this form is not permitted:

```php
$greeting = "Hello ${name}, welcome back!";
```


## String-Konkatenation

Strings müssen mit dem "."-Operator konkateniert werden. Ein Leerzeichen muß immer vor und nach dem "." Operator eingefügt werden, um die Lesbarkeit zu erhöhen:

```php
$company = 'Zend' . ' ' . 'Technologies';
```

Werden Strings mit dem "." Operator konkateniert, sollte die Anweisung in mehrere Zeilen umgebrochen werden, um die Lesbarkeit zu erhöhen. In diesen Fällen sollte jede folgende Zeile mit Leerraum aufgefüllt werden so das der "." Operator genau unterhalb des "=" Operators steht:

```php
$sql = "SELECT `id`, `name` FROM `people` "
     . "WHERE `name` = 'Susan' "
     . "ORDER BY `name` ASC ";
```


## Arrays
### Numerisch indizierte Arrays

Negative Indizes sind nicht gestattet. Ein solches Array darf mit einer nicht-negativen Zahl beginnen, es wird jedoch davon abgeraten.

Werden indizierte Arrays mehrzeilig mit Hilfe der "array"-Funktion definiert, muß ein Leerzeichen nach jeder Kommabegrenzung folgen, um die Lesbarkeit zu erhöhen:

```php
$sampleArray = array(1, 2, 3, 'Zend', 'Studio');
```

Es ist gestattet, mehrzeilige indizierte Arrays mit der "array"-Funktion zu definieren. In diesem Fall, muß jede folgende Zeile mit Leerzeichen aufgefüllt werden so das der Beginn jeder Zeile ausgerichtet ist:

```php
$sampleArray = array(1, 2, 3, 'Zend', 'Studio',
                     $a, $b, $c,
                     56.44, $d, 500);
```


### Assoziative Arrays

Wenn assoziative Arrays mit der "array"-Funktion deklariert werden, ist das Umbrechen der Anweisung in mehrere Zeilen gestattet. In diesem Fall muß jede folgende Linie mit Leerraum aufgefüllt werden so das beide, der Schlüssel und der Wert, untereinander stehen:

```php
$sampleArray = array(
    'firstKey'  => 'firstValue',
    'secondKey' => 'secondValue'
);
```


## Klassen
### Klassendeklaration

Klassen müssen den Namenskonventionen entsprechend benannt werden.

Die Klammer sollte immer in der Zeile unter dem Klassennamen geschrieben werden.

Jede Klasse muß einen Dokumentationsblock haben der dem PHPDocumentor Standard entspricht.

Jeder Code in der Klasse muß mit vier Leerzeichen eingerückt sein.

Nur eine Klasse ist in jeder PHP Datei gestattet.

Das Platzieren von zusätzlichem Code in Klassendateien ist gestattet, aber es wird davon abgeraten.

Das folgende ist ein Beispiel einer gültige Klassendeklaration:

```php
/**
 * Documentation Block Here
 */
class SampleClass
{
    // all contents of class
    // must be indented four spaces
}
```


### Klassenvariablen

Klassenvariablen müssen entsprechend den Variablennamenskonventionen benannt werden.

Jede Variable die in der Klasse deklariert wird muß am Beginn der Klasse aufgelistet werden, vor der Deklaration von allen Methoden.

Das "var"-Schlüsselwort ist nicht gestattet. Klassenvariablen definieren ihre Sichtbarkeit durch die Verwendung der private, protected, oder public Modifikatoren. Öffentliche Klassenvariable (Sichtbarkeit "public") sind erlaubt, es wird aber zu Gunsten von Zugriffsmethoden (getter/setter) davon abgeraten.

## Funktionen und Methoden
### Deklaration von Funktionen und Methoden

Funktionen müssen nach der Funktionsnamenskonvention benannt werden.

Methoden innerhalb von Klassen müssen immer ihre Sichtbarkeit durch Verwendung eines der private, protected, oder public Modifikatoren definieren.

Wie bei Klassen sollte die Klammer immer in der Zeile unterhalb des Funktionsnamens geschrieben werden. Leerzeichen zwischen dem Funktionsnamen und der öffnenden Klammer für die Argumente sind nicht erlaubt.

Von globalen Funktionen wird abgeraten.

Das folgende ist ein Beispiel einer gültigen Funktionsdeklaration in einer Klasse:

```php
/**
 * Documentation Block Here
 */
class Foo
{
    /**
     * Documentation Block Here
     */
    public function bar()
    {
        // all contents of function
        // must be indented four spaces
    }
}
```

NOTE: Pass-by-reference is the only {+explicit+} parameter passing mechanism permitted in a method declaration.

```php
/**
 * Documentation Block Here
 */
class Foo
{
    /**
     * Documentation Block Here
     */
    public function bar(&$baz)
    {}
}
```

Call-time pass-by-reference ist strikt verboten.

Der Rückgabewert darf nicht in Klammern stehen. Das kann die Lesbarkeit behindern und zusätzlich zu Fehlern führen, wenn eine Methode später auf Rückgabe durch Referenz geändert wird.

```php
/**
 * Documentation Block Here
 */
class Foo
{
    /**
     * WRONG
     */
    public function bar()
    {
        return($this->bar);
    }

    /**
     * RIGHT
     */
    public function bar()
    {
        return $this->bar;
    }
}
```


### Aufruf von Funktionen and Methoden

Wie bei Funktionsdeklaration darf zwischen Funktionsnamen und der öffnenden Klammer für die Argumente beim Funktionsaufruf kein Leerzeichen stehen.

Funktionsargumente sollten durch ein einzelnes trennendes Leerzeichen nach dem Komma getrennt werden. Das folgende ist ein Beispiel für einen gültigen Aufruf einer Funktion die drei Argumente benötigt:

```php
threeArguments(1, 2, 3);
```

Call-time pass-by-reference is strictly prohibited.

In passing arrays as arguments to a function, the function call may include the "array" hint and may be split into multiple lines to improve readability. In such cases, the normal guidelines for writing arrays still apply:

```php
threeArguments(array(1, 2, 3), 2, 3);

threeArguments(array(1, 2, 3, 'Zend', 'Studio',
                     $a, $b, $c,
                     56.44, $d, 500), 2, 3);
```


## Kontrollstrukturen
### if/else/elseif

Control statements based on the if and elseif constructs must have a single space before the opening parenthesis of the conditional and a single space after the closing parenthesis.

Within the conditional statements between the parentheses, operators must be separated by spaces for readability. Inner parentheses are encouraged to improve logical grouping for larger conditional expressions.

The opening brace is written on the same line as the conditional statement. The closing brace is always written on its own line. Any content within the braces must be indented using four spaces.

```php
if ($a != 2) {
    $a = 2;
}
```



For "if" statements that include "elseif" or "else", the formatting conventions are similar to the "if" construct. The following examples demonstrate proper formatting for "if" statements with "else" and/or "elseif" constructs:

```php
if ($a != 2) {
    $a = 2;
} else {
   $a = 7;
}

if ($a != 2) {
    $a = 2;
} elseif ($a == 3) {
   $a = 4;
} else {
   $a = 7;
}
```


PHP allows statements to be written without braces in some circumstances. This coding standard makes no differentiation - all "if", "elseif" or "else" statements must use braces.

Use of the "elseif" construct is permitted but strongly discouraged in favor of the "else if" combination.

### Switch

Control statements written with the "switch" statement must have a single space before the opening parenthesis of the conditional statement and after the closing parenthesis.

All content within the "switch" statement must be indented using four spaces. Content under each "case" statement must be indented using an additional four spaces.

```php
switch ($numPeople) {
    case 1:
        break;

    case 2:
        break;

    default:
        break;
}
```


The construct default should never be omitted from a switch statement.

NOTE: It is sometimes useful to write a case statement which falls through to the next case by not including a break or return within that case. To distinguish these cases from bugs, any case statement where break or return are omitted should contain a comment indicating that the break was intentionally omitted.

## Inline Documentation
### Documentation Format

All documentation blocks ("docblocks") must be compatible with the phpDocumentor format. Describing the phpDocumentor format is beyond the scope of this document. For more information, visit: http://phpdoc.org/

All class files must contain a "file-level" docblock at the top of each file and a "class-level" docblock immediately above each class. Examples of such docblocks can be found below.

### Files

Every file that contains PHP code must have a docblock at the top of the file that contains these phpDocumentor tags at a minimum:

```php
/**
 * Short description for file
 *
 * Long description for file (if any)...
 *
 * LICENSE: Some license information
 *
 * @author     Vorname Nachname <email>
 * @copyright  2008 Zend Technologies
 * @license    http://framework.zend.com/license   BSD License
 * @category   Stud.IP
*/
```

Optional tags:
```php
/**
 * @package    calendar 
 * @link       http://framework.zend.com/package/PackageName
 * @since      File available since Release 1.5.0
*/
```

### Classes

Every class must have a docblock that contains {-these phpDocumentor tags-} at a minimum:

```php
/**
 * Short description for class
 *
 * Long description for class (if any)...
 *
 */
```

Optional tags:
```php
/**
 * @link       http://framework.zend.com/package/PackageName
 * @since      Class available since Release 1.5.0
 * @deprecated Class deprecated in Release 2.0.0
 */
```

### Functions

Every function, including object methods, must have a docblock that contains at a minimum:

A description of the function

All of the arguments

All of the possible return values


It is not necessary to use the "@access" tag because the access level is already known from the "public", "private", or "protected" modifier used to declare the function.

If a function/method may throw an exception, use @throws for all known exception classes:

```php
@throws exceptionclass [description]
```


## Templates

Für Templates gelten alle obigen Aussagen. Zusätzlich gelten aber folgende Regeln:

Bei Short-Tag-Zuweisungen muss nach dem eröffnenden und vor dem schließenden Tag genau ein Leerzeichen eingefügt werden:

```php
<div class="<?= $css_class ?>"></div>
```

Semikola werden nicht verwendet.

Zur Steigerung der Lesbarkeit können die alternativen Kontrollstrukturen verwendet werden:

```php
<? if (true) : ?>
...
<? else : ?>
...
<? endif ?>

<? foreach ($array() as $key => $value) : ?>
...
<? endforeach ?>

usw.
```

Dabei ist zu beachten, dass die Doppelpunkte mit je einem Leerzeichen umschlossen werden. Die abschließenden `endif`, `endforeach` usw. dürfen (genau wie bei den sonst üblichen {}) nicht mit einem Semikolon beendet werden.



### Ziele

Coding Standards sind in jedem Softwareprojekt wichtig, insbesondere wenn viele Entwickler daran arbeiten. Coding Standards helfen sicherzustellen, dass der Code von hoher Qualität ist, weniger Fehler hat und einfach zu warten ist.


### Pagelevel-Doc-Block für copy&paste

Dieser Absatz ist nicht-normativ.

```php
/**
 * filename - Short description for file
 *
 * Long description for file (if any)...
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      name <email>
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 */
```
