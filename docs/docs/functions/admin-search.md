---
id: admin-search
title: Wie erweitere ich die Admin-Suche?
sidebar_label: Admin-Suche
---

Ab der 3.1 gibt es einen neuen Admin-Bereich, der auch gerne Admin-MeineVeranstaltungen genannt wird. Ab der 3.2 und 3.3 gibt es darin neue Möglichkeiten, die Admin-Suche mit Plugins zu erweitern. Das soll den Administratoren für alle Speziallplugins Möglichkeiten bieten, ohne dass sie dabei ihren gewohnten Arbeitsplatz (den Admin-Bereich in Stud.IP) verlassen müssen.



## Neue Filter in der Sidebar

Dies ist der einfache Teil. Man muss einen Filter in die Sidebar bekommen und die Sidebar kennen wir ja gut. Nur den Zeitpunkt sollte man beachten. Der Konstruktor des Plugins ist der falsche Zeitpunkt. Stattdessen muss sich das Plugin registrieren für eine Notification (des NotificationCenters). Aber immerhin das Registrieren kann es im Konstruktor machen. Und zwar in etwa so:

```php
if ((stripos($_SERVER['REQUEST_URI'], 'dispatch.php/admin/courses') !== false)) {
    NotificationCenter::addObserver(
        $this, 
        'addLectureshipFilterToSidebar', 
        'SidebarWillRender'
    );
}
```


Und dazu gehört noch eine Methode des Plugins, die "addLectureshipFilterToSidebar" heißt. Sie kann so aussehen:

```php
public function addLectureshipFilterToSidebar() 
{
    $widget = new OptionsWidget();
    $widget->setTitle(_('Lehrauftragsfilter'));
    $widget->addCheckbox(
        _('Nur mit Lehrauftrag'),
        $GLOBALS['user']->cfg->getValue('LECTURESHIP_FILTER'),
        PluginEngine::getURL($this, [], 'toggle_lectureship_filter')
    );
    Sidebar::Get()->insertWidget($widget, 'editmode', 'filter_lectureships');
}
```

Letztlich kann man die Sidebar zu dem Zeitpunkt beliebig manipulieren. Für die Filter der Sidebar ist es üblich, je ein eigenes Formular zu definieren, das auf eine besondere Action verweist, in der dann ein globaler Parameter (hier `$GLOBALS['user']->cfg->getValue("LECTURESHIP_FILTER")` ) der UserConfig verändert wird.

Mein Beispiel-Plugin hat also folgerichtig noch eine Action:

```php
public function toggle_lectureship_filter_action()
    {
        $oldvalue = (bool) $GLOBALS['user']->cfg->getValue("LECTURESHIP_FILTER");
        $GLOBALS['user']->cfg->store("LECTURESHIP_FILTER", $oldvalue ? 0 : 1);
        header("Location: ".URLHelper::getURL("dispatch.php/admin/courses"));
    }
```

Diese Action ändert nur den UserConfig-Eintrag und schickt den Nutzer gleich zurück zur Admin-Seite.

Dies ist jetzt ein Beispiel mit einer Checkbox, die nur zwei Zustände hat. Aber auf dieselbe Weise könnte man Freitextfelder oder Select-Boxen oder jedes andere komplexe Formular unter bringen. Wichtig ist der Ablauf: Sidebar-Formular einbauen, Nutzer klickt drin rum, Seite lädt sich neu und in einer speziellen Action wird der globale Parameter verändert.

## Die AdminCourseFilter-Klasse

Jetzt muss dieser Filter noch angewendet werden. Klicken kann man ihn schon, aber er muss noch tatsächlich die Ergebnisse verändern können. Dazu wird die Klasse AdminCourseFilter wichtig. Sie regelt den gesamten Query, mit dem die Veranstaltungen zusammen gesucht werden. Kurz bevor der Query ausgeführt wird, wird eine Notification des NotificationCenters angeschmissen, die "AdminCourseFilterWillQuery" heißt. Ein Plugin, das einen Filter anwenden möchte, sollte sich im Konstruktor (Achtung, es sollte ein SystemPlugin sein) entsprechend registrieren. Das geht so:

```php
NotificationCenter::addObserver($this, "addMyFilter", "AdminCourseFilterWillQuery");
```

Zudem sollte das Plugin eine Methode besitzen, die "addMyFilter" heißt. Das ist natürlich nur ein Beispielname und sollte abgeändert werden. So könnte diese Methode aussehen:

```php
public function addLectureshipFilter($event, $filter)
{
    if ($GLOBALS['user']->cfg->getValue("LECTURESHIP_FILTER")) {
        $filter->settings['query']['joins']['lehrauftrag'] = [
            'join' => "INNER JOIN",
            'table' => "lectureship",
            'on' => "seminare.Seminar_id = lehrauftrag.seminar_id"
        ]
    }
}
```

Auf die Details gehen wir gleich ein. Wichtig ist erst einmal, die Methode bekommt als zweiten Parameter ein $filter Objekt vom Typ "AdminCourseFilter" und kann dieses Objekt beliebig modifizieren. Die hier angegebene Methode modifiziert nur, wenn ein bestimmter UserConfig-Parameter gesetzt ist. Das ist der Parameter, der in der Sidebar gesetzt wird.

### Modifizieren des AdminCourseFilter Objektes

Wie modifiziert man dieses AdminCourseFilter Objekt jetzt eigentlich? Am Ende kommt eine SQL-Query heraus. Dass man das Objekt modifizieren möchte, bedeutet im Grunde, dass man alle Teile des Queries modifizieren will, jeden JOIN und jedes SELECT und natürlich auch das WHERE. Dazu hat das $filter Objekt ein öffentliches Attribut $filter->settings, in dem der ganze Query in einem Array gespeichert wird. Alle Einträge des Arrays sind assoziative Einträge von wiederum assoziativen Arrays. Auf diese Weise kann man neue Einträge hinzufügen, etwa um eine WHERE-Klausel einzubauen, aber man kann auch bestehende Einträge löschen oder ändern.

```php
$filter->settings = [
    'query' => [],
    'parameter' => []
];
```

In dem Query-teil steht alles drin, um ein Prepared-SQL-Statement zu erzeugen. Im Parameter-Teil stehen dann die notwendigen Parameter drin, die per `execute` eingesetzt werden.

```php
$filter->settings['query'] = array();
```

## Weitere Administrationsmodi

In dem Admin-Bereich gibt es in der Sidebar eine Auswahlbox "Aktionsbereich-Auswahl", mit der man einstellen kann, was genau man mit den gefilterten Veranstaltungen machen möchte. Man kann sagen, man will die Grunddaten bearbeiten oder Veranstaltungen archivieren. Aber Plugins können sich hier auch einklinken und einen eigenen Aktionsbereich definieren.

Dazu muss ein Plugin das Interface AdminCourseAction implementieren ( `class LehrauftragPlugin extends StudIPPlugin implements SystemPlugin, **AdminCourseAction**` ).

Dieses Interface besteht aus drei Methoden, die implementiert werden müssen. Dazu muss man verstehen, was der Adminbereich im Aktionsbereich macht. Der Adminbereich ist ja erst einmal eine große Liste von Veranstaltungen mit einem Aktionsbereich rechts in der Zeile der Veranstaltung. Da kann ein Button drin stehen oder eine Checkbox.

Im Falle der Checkboxen braucht es allerdings noch einen Button über und unter den Veranstaltungen, mit dem der ganze Bereich als ein Formular abgeschickt wird. Da stellt sich noch die Frage, wohin das Formular abgeschickt wird?

Also die erste Methode des Interface "AdminCourseAction" ist vermutlich "`public function useMultimode()`", die nur zurückgibt, ob man die Buttons oben und unten braucht. Falls sie nicht gebraucht werden, gibt die Methode `false` zurück, ansonsten `true`. Alternativ kann auch ein String übergeben werden, der gewissermaßen der Text des Buttons ist. Sowas wie "Veranstaltungen archivieren" oder so.

Die zweite Methode ist "`public function getAdminActionURL()`", mit der man definiert, an welche URL das Formular verschickt werden soll.

Die dritte ist die interessanteste Methode, weil sie den Aktionsbereich am Ende tatsächlich definiert. Sie lautet "`public function getAdminCourseActionTemplate($course_id, $values = null)`". Die $course_id sollte klar sein. $values als Parameter gibt an, was die Klasse AdminCourseFilter für die Veranstaltung gefunden hat. Dieser Parameter ist also ein assoziatives Array mit verschiedenen Daten der Veranstaltung. Der Name sollte darin auftauchen, vielleicht auch die Lehrenden. Theoretisch ist dieses Array auch erweiterbar, wie oben zu sehen ist. Damit könnte man sich Einzelabfragen sparen.

Die Methode `getAdminCourseActionTemplate` nimmt nun die Parameter und gibt ein Objekt vom Typ Flexi_Template (oder null) zurück. Dieses Template ist der Aktionsbereich, der als HTML-Schnipsel innerhalb einer Tabellenzelle dargestellt wird. Er kann einen Button ebenso beinhalten wie eine Checkbox oder sogar beides oder ganz andere komplexe Formularfelder.

Den Rest übernimmt die Schnittstelle. Sie speichert zum Beispiel selbst, welcher Aktionsbereich gewählt wurde, sodass ein Admin im richtigen Bereich bleibt.

## Hinzufügen von Spalten zu der Tabelle (ab 4.1)

Plugins haben auch die Möglichkeit, die angezeigte Tabelle um weitere Spalten zu erweitern. Das kann nützlich sein, wenn ein Evaluationsbeauftragter zum Beispiel in einer Spalte sehen möchte, wer die Evaluationsdaten das letzte Mal bearbeitet hat. Dazu kann ein Plugin ein weiteres Plugin-Interface implementieren, das `AdminCourseContents` lautet. Dieses Interface erwartet vom Plugin zwei Methoden.

* `adminAvailableContents()`: Das Plugin liefert hier zurück, welche weiteren Spalten überhaupt möglich sind, in der Tabelle anzuzeigen. Rückgabewert ist ein assoziatives Array, wobei der Index ein interner Name ist und der Wert der sichtbare Name mit allen möglichen Umlauten.
* `adminAreaGetCourseContent($course, $index)`: Dabei ist der `$index` exakt der Index, den `adminAvailableContents` zurückgeliefert hat. `$course` ist dabei ein Objekt der Klasse `Course` mit der betreffenden Veranstaltung. Rückgabe der Funktion ist entweder ein String oder ein Objekt vom Typ `Flexi_Template`. So ist man flexibel für alles. Wenn man möchte, dass die Spalte auch in der CSV-Datei beim Export sinnvoll auftaucht (was möglich ist), sollte man auf Schnickschnack wie klickbare Buttons im Template verzichtet.
