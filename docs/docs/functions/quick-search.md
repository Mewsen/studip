---
id: quick-search
title: QuickSearch-Klasse
sidebar_label: QuickSearch-Klasse
---


In `lib/classes/QuickSearch.class.php` wird eine GUI-Klasse bereit gestellt, mit der man ein einzeiliges Suchfeld inklusive AJAX-Dropdown Menü schnell und einfach an jede Stelle einbauen kann. Vorteile:

* Wenig und übersichtlicher Quellcode nötig.
* AJAX-Suche und Javascript-Dropdown Menü gibt es gratis.
* Das Suchfeld ist auch ohne Javascript benutzbar und genügt damit den Bestimmungen zur Barrierefreiheit von Stud.IP.
* Es kann nach praktisch allem gesucht werden - nicht nur in der Datenbank, sondern ganz beliebig.
* Das Suchfeld ist konfigurierbar durch weitere Funktionen.
* Bei zukünftigen Änderungen an diese Suchfelder durch die GUI-Kommission ist der Programmierer mit dieser Klasse auf der sicheren Seite, weil nur die Klasse verändert werden muss.

Im HTML gibt es dann das Input-Feld, das man erwartet und ein weiteres unsichtbares Input-Feld für die ID. Meistens sucht man nach etwas wie Personen, die einen klar lesbaren Namen haben. Aber der Programmierer will an der Stelle eigentlich nicht den Namen, sondern lieber eine user_id haben. Die user_id wird dann versteckt im Hintergrund gespeichert. Der Programmierer kann QuickSearch einbinden, wie ein Input-Feld, in das der Nutzer wie magisch nur die user_id eingegeben hätte - der Nutzer gibt aber den Klarnamen ein. QuickSearch bietet diese Architektur ganz automatisch.

## Einbau eines QuickSearch Feldes

Üblicherweise besteht die Suche aus zwei Elementen, die zusammen arbeiten: erstens, das Suchelement, das quasi ein Model ist, das konkret nach etwas sucht, und zweitens die QuickSearch-Klasse, die diese Suche ausführt und sich um die Ausgabe kümmert.

```php
$suche = new SQLSearch("SELECT username, Nachname " .
    "FROM auth_user_md5 " .
    "WHERE Nachname LIKE :input " .
    "LIMIT 5", _("Nachname"), "username");
print QuickSearch::get("username", $suche)
    ->setInputStyle("width: 240px")
    ->render();
```

Die Variable `$suche` ist also das Objekt, das die Suche durchführt. Dieses Objekt ist nicht notwendigerweise ein Objekt der Klasse SQLSearch, sondern ein Objekt der Klasse SearchType (wovon SQLSearch eine Unterklasse ist). SQLSearch ist nun eine spezielle Klasse, die beliebige SQL-Queries auf die Datenbank anwenden kann. In diesem Query bezeichnet `:input` stets den Suchstring, den ein Nutzer später eingibt.

Die Klasse QuickSearch kümmert sich danach dann um die Ausgabe. Der erste Wert des Konstruktors ist immer der Name des Suchfeldes im HTML, also zum Beispiel `<input name="username">`. Der zweite Parameter ist dann das Suchobjekt, das wir vorher definiert haben. Danach folgen einige Methoden, um die Ausgabe weiter zu konfigurieren wie `setInputStyle("width: 240px")` und die Methode `render()` veranlasst dann die Ausgabe des Ganzen.

Und so wird es dann aussehen:

Attach:QuickSearch1.png      Attach:QuickSearch2.png


## Shortcut

Für ganz einfache Suchen wie der Suche nach einem username oder einer user_id kann man als Suchobjektes auch einfach die Klasse "StandardSearch mit Parameter "username", "user_id", "Seminar_id", "Institut_id" oder "Arbeitsgruppe_id" schreiben. Also:

```php
print QuickSearch::get("seminar", new StandardSearch("Seminar_id"))
    ->setInputStyle("width: 240px")
    ->render();
```

## Weitere Methoden der QuickSearch-Klasse

* *withButton()* : Das Suchfeld bekommt auch gleich eine Lupe dazu; diese Lupe ist ein einfacher Submit-Button. Vorteil ist einfach, dass man zum Design keine drei verschachtelten DIVs selber schreiben muss. Aber bitte benutzt diese Methode nicht in Kombination mit anderen Methoden unten. Dies ist quasi nur für ein ganz einfaches Suchfeld ohne spezielles Styling. Insbesondere setInputStyle zerschießt anschließend das Styling eher, als dass es es besser macht. Man kann nur die Länge der Box ändern durch withButton(array('width' => "50")) mit Pixeln als Längenangabe.
* *defaultValue($valueID, $valueName)* : falls schon etwas eingetragen sein soll in dem Suchfeld, kann man hier den Namen und die dazu gehörige ID angeben.
* *setInputClass($class)* : Name einer CSS-Klasse, die dem Feld mitgegeben wird.
* *setInputStyle($style)* : besondere Angaben für style="" das dem Input-Feld mitgegeben wird.
* *setDescriptionColor($color)* : Farbe der Beschreibung des Textfeldes. Die Beschreibung des Textfeldes taucht nur auf, solange der Nutzer noch nichts geschrieben hat und kann abweichen von der normalen Schreibfarbe des Textfeldes.
* *noSelectbox()* : erzwingt, dass auf keinen Fall eine Select-Box für die Suchergebnisse angezeigt wird. Sinnvoll füre Suchfelder, die auf jeder Seite auftauchen. Aber Achtung! Hiermit fällt die nicht-JS Funktionalität des Suchfeldes ebenfalls flach.
* *fireJSFunctionOnSelect($function_name)* : Der Programmierer kann eine Javascript Funktion angeben, die das ausgewählte Objekt weiter verarbeiten kann. Nur den Namen angeben. Die Javascript-Funktion sollte als Parameter (item_id, item_name) erwarten. Diese Funktion sollte true zurückliefern, damit das Ergebnis nach dem Abfeuern der JS-Funktion noch im Input bestehen bleibt. Ansonsten wird es automatisch wieder gelöscht.
* *setAttributes($attr_array)* : Weitere Attribute für das Textfeld wie zum Beispiel ein title-Attribut. Zum Setzen solch eines titles würde `$attr_array = array('title' => 'nur ein Suchfeld')` übergeben werden. Natürlich funktioniert das aber auch mit "style" oder "class" als Attribut.
* *disableAutocomplete($disable = true)* : Hiermit kann man das AJAX-Autocomplete für dieses Suchfeld deaktivieren. Meistens will man das zur Verbesserung der Performance tun. Man erhält dann kein Auswahlfeld mehr, sondern muss regulär auf Enter drücken und bekommt dann eine stinknormale Select-Box. Diese Eigenschaft deaktiviert nebenbei natürlich auch alle Angaben aus fireJSFunctionOnSelect und lässt sich nur bedingt mit noSelectbox kombinieren. Falls man alle Autocompleter für QuickSearches im System deaktivieren will, um die Performance zu verbessern, eignet sich die Config-Einstellung global -> AJAX_AUTOCOMPLETE_DISABLED besser, die bewirkt dasselbe. Nur eben global für das ganze System und ohne den Quellcode anfassen zu müssen.

## Weitere Suchobjekte

Man ist nicht auf SQLSearch beschränkt. Jeder Programmierer kann eigene Suchobjekte definieren und so zum Beispiel auch eine Lucene-Index-Suche implementieren, wenn ihm gerade danach ist. Die Suchklassen müssen alle von der Klasse SearchType abgeleitet werden und mindestens die Methoden `includePath()` und (sinnvollerweise) `getResults(...)` überschreiben. Falls die Suchklasse im Kern von Stud.IP benutzt wird, sollte sie auch im Verzeichnis `lib/classes/seachtypes/` hinterlegt werden. Aber Pluginbauer können ihre Suchklassen natürlich auch im Plugin hinterlegen.

Eine kleine Beispielsuchklasse könnte zum Beispiel so aussehen:

```php
class SeminarTypSuchen extends SearchType {

    public function getTitle() {
        return _("Seminartyp suchen");
    }
    
    public function getResults($input, $contextual_data = array()) {
        $typen = $GLOBALS['SEM_TYPE'];
        foreach($typen as $key => $typ) {
            if (strpos($typ['name'], $input) === false) {
                unset($typen[$key]);
            } else {
                $typen[$key] = array($key, $typ['name']);
            }
        }
        return $typen;
    }

    public function includePath() {
        return __file__;
    }
}
```

Diese Klasse ist für den Fall gedacht, dass es in Stud.IP unübersichtlich viele Semeinartypen gibt. Diese Typen sind in der config.inc.php definiert und finden sich also nicht in der Datenbank. Für diesen Zweck ist also die Klasse SQLSearch unpraktikabel. 

Die Methode `getTitle` gibt nur den Schriftzug, der später im leeren Formularfeld stehen soll, wider. 
Die Methode `getResults` erledigt gewissermaßen die ganze Arbeit, durchsucht das Array aller Seminartypen nach dem eingegeben String und gibt ein Ergebnisarray der Form `array(array(ID_des_Seminar_Typs, Name_des_Typs), ...)` zurück. 
Die Methode `includePath` ist notwendig, damit diese Klasse gefunden wird (es gibt einen internen kleinen Autoloader), hat aber stets den gleichen Inhalt, kann also für alle Erweiterungsklassen von SearchType so übernommen werden.

Und das sollte es auch gewesen sein. Man kann noch einen Avatar für seine Suchergebnisse angeben, was sich aber für Seminartypen nicht gerade anbietet. Dennoch würde man da tun, indem man die Methoden `getAvatar` und `getAvatarImageTag` überschreibt. Siehe dazu Dokumentation im Quellcode.
