---
title: Anlegeassistent Veranstaltungen
---

### Architektur

Der Anlegeassistent besteht aus zwei Teilen.
* Der Trails-Controller (`app/controllers/course/wizard.php`) übernimmt die Ablaufsteuerung des Assistenten: welche Schritte im Assistenten gibt es, in welcher Reihenfolge werden sie durchlaufen, und wie werden die Daten weitergegeben.
* Die einzelnen Schritte sind jeweils in Klassen gekapselt, die das Interface `CourseWizardStep` implementieren.

### Der Controller

Im Trails-Controller wird die Abfolge der einzelnen Schritte und das Zwischenspeichern der eingegebenen Daten übernommen. Wichtige Actions des Controllers sind:

**`index_action()`**

DIese Action dient nur als Einstiegspunkt für neue Veranstaltungen.

**`step_action($number=0, $temp_id=*)`**

Hier findet die Anzeige eines einzelnen Schrittes statt. Parameter `$number` gibt hierbei an, welche Nummer der Schritt in der Abfolge hat. Der Parameter `$temp_id` dient dazu, die eingebenen Daten der einzelnen Schritte auch über die Schritte hinweg verfügbar zu halten. Ist die `$temp_id` nicht angegeben, wird eine neue generiert. Alle eingebenen Daten werden in der Session gespeichert, in der Form:
```php
$_SESSION[$temp_id] = [
    '<StepClassname1>' = [
        'name1' => 'value1',
        ...
    ],
    '<StepClassname2>' = [
        'name2' => 'value2',
        'name3' => ['value3', 'value4'],
        ...
    ],
    ...
];
```
Durch die bei jedem Schritt mitgeschleifte `$temp_id` ist sichergestellt, dass auch in mehreren Tabs parallel neue Veranstaltungen angelegt werden können. Die hier vergebene `$temp_id` ist nicht die spätere `seminar_id` der angelegten Veranstaltung.

**`process_action($step_number, $temp_id)`**

Diese Action wird nach dem Abschicken des Formulars aufgerufen, alle eingebenen Daten werden in die Session geschrieben.

Wurde im Assistenten auf "Zurück" geklickt, so wird einfach der vorhergehende Schritt aufgerufen.

Wurde "Weiter" geklickt, so werden alle eingegebenen Daten an die Schrittklasse übergeben. Schlägt diese fehl, wird der aktuelle Schritt nochmals angezeigt, durch die Validierung generierte Fehlermeldungen werden ausgegeben. Die Generierung der Fehlermeldungen (`PageLayout::postMessage` u.ä.) wird in der Schrittklasse erledigt.

Nicht alle registrierten Schritte sind immer notwendig, z.B. müssen nur dann Studienbereiche angegeben werden, wenn ein entsprechender Veranstaltungstyp gewählt wurde. Bei erfolgreicher Datenvalidierung ermittelt der Controller, welcher der eingetragenen Schritte der nächste erforderliche ist, und leitet zu dessen Anzeige weiter.

Sind bereits alle notwendigen Schritte durchlaufen, wird eine kurze Meldung angezeigt, dass alle nötigen Daten eingegeben wurden und die Veranstaltung nun angelegt werden kann. Wird dies bestätigt, legt der Controller eine neue, völlig leere, unsichtbare Veranstaltung an, damit eine ID generiert wird. Diese Veranstaltung wird dann sequenziell an jede einzelne der aufgerufenen Schrittklassen übergeben, damit die Daten der Veranstaltung mit den gespeicherten Werten befüllt werden können. Wie die genau zu geschehen hat, weiß jede Schrittklasse selbst. Das modifizierte `Course`-Objekt wird von der Klasse zurückgegeben und dann an den nächsten Schritt weitergereicht.

Nach Abschluss erfolgt eine Weiterleitung auf den Verwaltungsbereich der angelegten Veranstaltung oder auf "Meine Veranstaltungen", falls der Anlegeassistent durch Admins oder Roots von dort im Dialog aufgerufen wurde.

**`ajax_action()`**

Manche Schritte möchten vielleicht Daten per AJAX nachladen. Die Klasse selbst hat jedoch keine URL, die als Endpunkt für solche AJAX-Calls dienen kann. Daher gibt es diese Action, die per Request folgende Parameter übergeben bekommt:
* `step`: Nummer des aktuellen Schritts; dient dazu, die zugehörige Klasse zu ermitteln
* `method`: in der Zielklasse aufzurufende Methode, die die gewünschten Daten liefert
* `parameter`: dieses Array enthält alle für den Aufruf der Methode nötigen Parameter in der korrekten Reihenfolge

Um z.B. die Methode `getFoo($param1, $param2)` in der Klasse des Schritts 3 aufrufen zu können, lautet die korrekte (GET-)URL zum Controller:

`<studip>/dispatch.php/course/wizard/ajax?step=3&method=getFoo&parameter[]=<param1>&parameter[]=<param2>`

**`forward_action($step_number, $temp_id)`**

Diese Action dient einfach nur dazu, direkt per Request Daten an die Schrittklasse durchreichen zu können. Das ist vor allem dann wichtig, wenn kein JavaScript zur Verfügung steht. So wird z.B. das Öffnen eines Studienbereichsknotens realisiert, indem einfach der Parameter `open_node=<id`> an die forward_action gehängt wird.

**`copy_action($id)`**

Wird zum Kopieren der Veranstaltung mit der ID `$id` aufgerufen. Dort wird die zu kopierende Veranstaltung sequenziell an alle registrierten Schritte übergeben, deren `copy`-Methode die nötigen Daten für jeden Schritt extrahiert und so das in der Session gespeicherte Datenarray für den Assistenten befüllt.

### Das Interface `CourseWizardStep`

Jede Klasse, die in den Assistenten eingebunden werden soll, muss das Interface `CourseWizardStep` implementieren. Dazu gehören folgende Methoden:

**`getStepTemplate($values, $stepnumber, $temp_id)`**

Hier lädt der Schritt das Flexi-Template zur Anzeige und befüllt dessen Daten mit den Werten aus dem `$values`-Array. Wie weiter oben beschrieben, enthält dieses Array alle eingegebenen Werte aller Schrittklassen, will ein Schritt also nur die Werte haben, die von der eigenen Klasse kommen, so stehen diese in `$values[__CLASS__]`.

Die im Kern enthaltenen Templates liegen standardmäßig unter `app/views/course/wizard/steps`, das kann aber jeder Schritt für sich selbst durch die Instanziierung einer entsprechenden Flexi_TemplateFactory selbst regeln.

**`isRequired($values)`**

Anhand der übergebenen, bereits eingegeben Werte bestimmt der Schritt, ob er für den Anlegeprozess erforderlich ist. Bekanntestes Beispiel dafür ist der `StudyAreasWizardStep` (Zuordnung von Studienbereichen), der nur angezeigt werden muss, wenn in einem vorhergenden Schritt ein entsprechender Veranstaltungstyp gewählt wurde.

**`alterValues($values)`**
Neben den Standardbuttons für "Weiter" und "Zurück", die durch den Assistenten navigieren, kann es im Eingabeformular eines Schritts auch weitere Buttons geben, die das Formular absenden. Hierdurch ausgelöste Aktionen werden vom Controller direkt an diese Methode der Schrittklasse weitergereicht, wo sie verarbeitet werden können. So kann man z.B. für den Fall ohne Javascript Buttons einbauen, die bestimmte Werte übernehmen. Auch direkte Aufrufe der `forward`-Methode im Controller, die ein Request generieren, ohne das Formular abschicken, werden hierhin übergeben.

**`validate($values)`**

Beim Fortfahren zum nächsten Schritt im Assistenten werden die bisher eingegebenen Daten an diese Klassenmethode übergeben, um auf Vollständigkeit und Plausibilität überprüft zu werden. Im Fehlerfall müssen hier auch entsprechende Meldungen erzeugt werden, z.B. über `PageLayout::postMessage`. Wie bei `getStepTemplate` werden auch hier alle Werte aller Schrittklassen übergeben, die eigenen Werte sind also über `$values[__CLASS__]` verfügbar.

**`storeValues($course, $values)`**

Diese Methode wird direkt vor dem Anlegen der Veranstaltung aufgerufen. Das übergebene Course-Objekt repräsentiert die anzulegende Veranstaltung, die mit den übergebenen Werten weiter befüllt werden kann. Auch hier gilt: $values enthält alle Werte aller Schrittklassen.

**`copy($course, $values)`**

Mit dieser Methode kann das $values-Array mit Werten befüllt werden, die z.B. aus dem übergebenen Course-Objekt (= die zu kopierende Veranstaltung) stammen können.
