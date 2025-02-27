---
title: SimpleORMap
---

Die Klasse SimpleORMap (häufig mit SORM abgekürzt) bietet seit Stud.IP Version 1.4 einfaches objekt-relationales Mapping nach dem Active Record Muster. Mit ihrer Hilfe kann die Verwendung von SQL-Code stark reduziert werden, sodass es für Controller in den meisten Fällen nicht mehr relevant ist, wie der Name der Datenbanktabelle ist, in der die Daten liegen.


### Allgemeines

Jede von SimpleORMap abgeleitete Klasse gehört zu einer Datenbanktabelle. Eine Instanz der Klasse entspricht dann einem Datensatz der entsprechenden Tabelle, und ermöglicht damit normale Lese- und Schreiboperationen (auch "CRUD Operationen" genannt) auf der Datenbank. Alle Spalten der Tabelle werden zu virtuellen Attributen der Klasse. Die Klasse erfüllt das ArrayAccess Interface, womit der Zugriff auf die Attribute wie auf ein Array geschehen kann. Groß/Kleinschreibung der Attribute muss nicht berücksichtigt werden.

#### Die SimpleORMap-Klasse

Die SimpleORMap-Klasse ist die Basisklasse, welche einige Funktionen zum einfachen Lesen von Objekten aus Datenbanktabellen mitliefert.

#### Die SimpleORMapCollection-Klasse

Die SimpleORMapCollection-Klasse verwaltet eine Sammlung von SimpleORMap-Objekten. Sie wird beim Holen mehrerer Objekten aus der Datenbank eingesetzt und kann wie ein normales Array behandelt werden, da sie (über ein paar andere Ableitungen) die Klasse ArrayAccess implementiert.

Zusätzlich hat sie ein paar Methoden, die ein Array nicht hat und die Verarbeitung von SimpleORMap-Objekten und deren Attributen erleichert. Die wichtigsten davon werden im Folgenden vorgetellt.

##### pluck() - Wert eines Attributes aller Objekte mit der Methode finden

Will man beispielsweise nur die Benutzer-ID aller Teilnehmer einer Veranstaltung finden, so kann man diese mittels der pluck()-Methode der SimpleORMapCollection-Klasse finden:

```php
$memberIds = Course::find($id)->members->pluck('user_id');
```

`$memberIds` enthält ein Array mit allen Benutzer-IDs.

##### filter() - Objekte filtern

Die filter()-Methode erlaubt es, Objekte in einer SimpleORMapCollection nach selbst gewählten Kriterien zu filtern, wobei sie wiederrum ein SimpleORMapcollection-Objekt zurückliefert. Dazu benöigt sie eine Callback-Funktion, welche bei jedem Objekt entscheidet, ob das Filterkriterium erfüllt ist, oder nicht. Liefert diese Funktion false zurück, ist das Kriterium nicht erfüllt. Ist es erfüllt, wird hingegen true zurückgeliefert.

Beispiel: Finde alle Benutzer-IDs aller Dozenten einer Veranstaltung:

```php
$dozenten_ids = Course::find($seminar_id)->members->filter(function ($m) {
    return $m['status'] === 'dozent';
})->pluck('user_id');
```

`$m` ist ein Objekt von CourseMember, wodurch mit `$m['status']` abgefragt werden kann, ob das Mitglied Dozent in der Veranstaltung ist oder nicht.

##### weitere Methoden

Es gibt noch einige weitere Methoden von SimpleORMapCollection, die hier nur kurz beschrieben werden:

* **map**: Verändert alle Elemente des SimpleORMapCollection-Objektes anhand einer Funktion, die ein Element annimmt und etwas beliebiges anderes zurück gibt. Das Ergebnis von `map` ist ein Array und kein SimpleORMapCollection-Objekt, weil in einem SimpleORMapCollection-Objekt nur Elemente von SimpleORMap auftauchen dürfen.
* **toGroupedArray**: liefert ein Array der Elemente zurück, wobei die Schlüssel des Arrays gleich der IDs der Elemente ist. Das ist praktisch, um schnell innerhalb der Menge das Element mit der einer bestimmten ID zu bekommen.
* **first**: Gibt nur das erste Element des SimpleORMapCollection-Objektes zurück.
* **last**: Gibt nur das letzte Element des SimpleORMapCollection-Objektes zurück.
* **val**: Gibt vom ersten Element den Wert eines bestimmten Attributes wieder. Beispielsweise gibt `Course::find($id)->members->val('status');` den Status des ersten Teilnehmers in der SimpleORMapCollection zurück.


### Erstellen einer SimpleORMap-Klasse für eine Datenbanktabelle

Eine Klasse für eine Datenbanktabelle erweitert die Klasse SimpleORMap. Es ist wichtig, den Namen der Datenbanktabelle zu setzen. Dies geschieht in der statischen Methode configure(), welcher man ein Array mit Konfigurationsparametern übergeben kann, sofern diese benötigt werden.
```php
class HalloWelt extends SimpleORMap
{
    protected static function configure ($config = [])
    {
        $config['db_table'] = 'hallo_welt';
        parent::configure($config);
    }
}
```

Die Tabellenspalten werden automatisch aus der Datenbank ausgelesen, genau so wie der Primärschlüssel. Die Metadaten werden im Stud.IP Cache zwischengespeichert, daher muss dieser nach Tabellenänderungen auch geleert werden. Man kann diese Daten über die Methode SimpleORMap::getTableMetadata() bekommen. Der Primärschlüssel eines Datensatzes lässt sich immer über die Methode getId() auslesen, außerdem wird er auf eine virtuelle Eigenschaft id abgebildet. D.h. wenn eine Tabelle eine Spalte id besitzt, sollte sie auch der Primärschlüssel sein. 

#### Dokumentation im Quellcode

Es ist bei SimpleORMap-Klassen üblich, im Quellcode eine Dokumentation anzulegen, die die verwendbaren Attribute beschreibt. Dies erleichtert es anderen Entwicklern, die Klasse zu verwenden, da kein Blick in das Datenbankschema geworfen werden muss, um rauszufinden, welche Attribute verfügbar sind.

Zur Dokumentation (als Beispiel mit der obigen HalloWelt-Klasse) wird oberhalb der Klassendefinition ein Block mit folgendem Schema eingefügt:
```php
/**
 * @property int id database column
 * @property string user_id database column
 * @property string greeting database column
**/
```

### Objekte aus der Datenbank lesen

#### anhand des Primärschlüssels

##### einzelnes Objekt holen

Zum Finden eines vorhandenen Datensatzes anhand des Primärschlüssels benutzt man die find() Methode. Übergibt man dem Konstruktor einen Primärschlüssel, so wird ein vorhandener Datensatz mit diesem Primärschlüssel geladen. Wurde der Datensatz nicht gefunden, wird null zurückgegeben.

```php
$id = 1;

$course = Course::find($id);
if ($course) {
    echo $course->name;
}
```

##### viele Objekte holen

Benötigt man eine Menge von Objekten, die man anhand einer Liste von Primärschlüsseln ermittelt hat, kann man die Methode findMany() benutzen. Diese nimmt ein Array mit Schlüsseln entgegen und als zweiten Parameter optional einen ORDER BY Teil.

```php
$courses = Course::findMany($course_ids, "ORDER BY name");
```

##### viele Objekte holen und direkt weiterverarbeiten

Möchte man dagegen eine Menge von Objekten nicht erzeugen, sondern prozessieren, gibt es die genannten Methoden noch in einer findEach... und findAndMap... Ausprägung. Diese Methoden fordern als ersten Parameter ein "callable", und sie iterieren durch die gefundenen Datensätze und rufen jeweils das callable mit einem Objekt auf. FindEach... gibt die Anzahl der iterierten Objekte zurück, findAndMap.. dagegen ein Array mit den Rückgabewerten des callable.

```php
//erzeugt ein Array mit Veranstaltungstiteln
$courses = Course::findAndMapMany(function ($course) {
    return $course->getFullname('number-name-semester');
}, $course_ids, "ORDER BY name");
```


#### anhand von SQL-Anweisungen

Jedes SimpleORMap Objekt verfügt aufgrund der Vererbung von SimpleORMap über eine ganze Reihe von findBy-Methoden. Die wichtigste davon ist findBySQL(), da man mit dieser Methode den Teil einer SQL-Abfrage übergeben kann, welche rechts neben dem WHERE-Teil der SQL-Abfrage stehen soll. Der zweite Parameter der findBySQL()-Methode besteht aus einem assoziativen Array, welches Parameter enthält, die in die Abfrage eingebaut werden sollen. Die Rückgabe ist immer ein Array (genauer gesagt eine SimpleORMapCollection) von SimpleORMap Objekten der entsprechenden Klasse. 

```php
$courses = Course::findBySQL("name LIKE ? ORDER BY name", [$search]);
```

##### Finden einzelner Objekte anhand von SQL-Anweisungen

Wenn man nur ein Objekt durch die Abfrage holen möchte, kann man stattdessen findOneBySQL() verwenden. Hier wird grundsätzlich nur das erste Element der Abfrage als Objekt erzeugt und zurückgegeben.
```php
$newest_course = Course::findOneBySQL("1 ORDER BY mkdate");
```

##### Finden von Objekten anhand dessen Attributen

Ein SimpleORMap-Objekt besitzt automatisch auch findBy-Methoden für Abfragen nach allen Attributen (Datenbankspalten), die definiert wurden, sodass Abfragen der folgenden Art möglich sind:

```php
$courses = Course::findManyByStatus([1,4,5,7], "ORDER BY status,name");

$courses = Course::findByStatus(1, "ORDER BY status,name");

$course = Course::findOneByStatus(1, "ORDER BY mkdate");
```

Analog können ab Stud.IP 4.2 Einträge anhand eines Attributwerts gezählt bzw gelöscht werden:

```php
// Zähle alle versteckten Veranstaltungen im System
$hidden_courses = Course::countByVisible(0);

// Lösche alle Veranstaltungen mit der Veranstaltungsnummer "TODO"
Course::deleteByVeranstaltungsnummer('TODO');
```

### Bearbeitung eines Objektes

Nach dem Laden eines Objektes aus der Datenbank kann dieses anhand der Attribute geändert werden. Dazu setzt man einfach die Attribute auf andere Werte:
```php
$course = Course::find($id); //laden
$course->name = 'Neue Veranstaltung'; //ändern
$course->store(); //speichern
```

#### Speichern

Um die geänderten Werte eines Objektes in der Datenbank zu speichern ruft man dessen store()-Methode auf. Es findet keine automatische Speicherung statt, sodass Änderungen, welche nicht mittels store() in die Datenbank überführt wurden, verloren gehen.

store() liefert eine Zahl zurück, die die Anzahl der geänderten Datensätze anzeigt (da u.U. Relationen gespeichert werden, kann das auch > 1 sein). Es kann auch false zurück geliefert werden, das bedeutet dann, das die Speicherung unterbrochen wurde, z.B. wegen eines Fehlers oder eines callbacks (`before_store`, `before_update`), der die Speicherung verhindert hat.


#### Änderungen vor dem Speichern erkennen

Möchte man überprüfen, ob sich das Objekt seit dem letzten Lesen Änderungen enthält, kann man die Methode isDirty() aufrufen. Analog dazu kann man ein einzelnes Attribut auf Änderung mit isFieldDirty($field) überprüfen.


#### Zurücknehmen von Änderungen

Eine Änderung kann mit revertValue() zurückgenommen werden. Den ursprünglichen Wert kann man, sofern vorhanden mit getPristineValue() herausbekommen.


#### Beispiel zum Speichern:

Im Folgenden wird ein Course-Objekt (welches eine Veranstaltung wiederspiegelt) geladen, geändert, auf Änderungen geprüft, die Änderungen zurückgenommen und gespeichert.

```php
$course = Course::find($id);
$course->name; // "Alte Veranstaltung";
$course->name = 'Neue Veranstaltung';
$course->isDirty(); // ist true
$course->isFieldDirty('number'); // ist false
$course->getPristineValue('name'); // liefert "Alte Veranstaltung"
$course->revertValue('name'); //Zurücknehmen der Änderungen
$course->store(); //ergibt 0, da keine Veränderungen mehr vorliegen (Änderungen wurden ja zurückgenommen)
```


### Anlegen eines Objektes

Um einen neuen Datensatz zu erzeugen, erstellt man ein neues Objekt, weist zwingen benötigte Werte über dessen Attribute zu und ruft die store() Methode auf:

```php
$course = new Course();
$course->name = 'Neue Veranstaltung';
$course->store();
```

Da in diesem Beispiel kein Wert für den Primärschlüssel gesetzt wurde, wird vor dem store() implizit ein neuer Schlüssel erzeugt. Es wird bei einwertigem Schlüssel davon ausgegangen, dass ein für Stud.IP typischer 32 Zeichen langer Schlüssel benutzt wird. Wenn der Schlüssel in der Datenbank auf AUTO_INCREMENT gesetzt ist, wird stattdessen nach dem store() der automatisch von der Datenbank vergebene Schlüssel geladen. Man kann dieses Verhalten auch modifizieren (siehe #callbacks)

### Ein Objekt löschen

Um ein Objekt zu löschen ruft man die delete() Methode auf, nachdem man es aus der Datenbank geladen hat. delete() liefert die Anzahl der gelöschten Datensätze zurück. Hier kann es ebenfalls vorkommen, dass die Anzahl größer als 1 ist, wenn kaskadierend abhängige Objekte mit gelöscht wurden. Es kann auch false zurück geliefert werden, was bedeutet, dass das Löschen unterbrochen wurde, was aufgrund eines Fehlers oder eines callbacks (`before_delete`) passiert sein kann.

Das Objekt selbst wird nach dem Aufruf von delete() nicht automatisch gelöscht, aber geleert. Ob man ein gelöschtes Objekt vor sich hat, kann man mit dessen Methode isDeleted() nachprüfen.

```php
$course = Course::find($id); //Objekt laden
$course->delete(); //Objekt löschen
$course->getId(); // ergibt null
$course->isNew(); // ergibt false;
$course->isDeleted(); //ergibt true
```


### Relationen

Seit der Version 2.4 von Stud.IP kann SimpleORMap auch Relationen zwischen den Datenbanktabellen beziehungsweise deren beinhalteten Objekten abbilden. Das Grundprinzip ist, dass ein Objekt einer von SimpleORMap abgeleiteten Klasse ein weiteres Attribut hat, um auf eine andere Datenbanktabelle zuzugreifen. Beispielsweise sind einem Kursobjekt mehrere User-Objekte zugeordnet, die als Teilnehmer der Veranstaltung gelten. Zwischen den Tabellen `seminare` und `auth_user_md5/user_info` gibt es eine Relationstabelle `seminar_user`. Es gibt also eine n:m Verknüpfung, die in der Course-Klasse abgebildet wurde, um auf einfache Art und Weise über ein Attribut eines Course-Objektes die Teilnehmer zu bekommen:

```php
$course = new Course($seminar_id);
$courseMembers = $course->members;
```

**WICHTIG:** Dieses Beispiel zeigt einen Fallstrick bei der Benutzung von Relationen in SimpleORMap auf: Im Beispiel repräsentiert $courseMembers nicht die User, sondern Objekte der SimpleORMap-Klasse CourseMember. Damit ist die Variable `$coursemembers` zwar korrekt befüllt, weil man damit auch auf die Felder der Tabelle `seminar_user` leicht zugreifen kann, aber meistens interessiert man sich nicht für die Relationstabelle, sondern für die verknüpften Objekte wie hier die zugehörigen User-Objekte. Der naheliegende Weg, um an diese Objekte zu kommen, bestünde darin, alle Objekte von `$courseMembers` zu durchlaufen und sich dann die dazugehörigen User-Objekte zu holen. Dank SimpleORMapCollection gibt es einen Weg, bei dem weniger Code geschrieben werden muss.


Um zum Beispiel alle User-Objekte aller Dozenten einer Veranstaltung zu bekommen, werden aus allen Veranstaltungsteilnehmern diejenigen gefiltert, welche den Status Dozent haben und nur deren Benutzer-IDs gespeichert. Über die statische Methode findMany() der User-Klasse können dann alle User-Objekte der Dozenten der gewählten Veranstaltung ausgelesen werden.

```php
$dozenten_ids = Course::find($seminar_id)->members->filter(function ($m) {
     return $m['status'] === 'dozent';
})->pluck('user_id');
$dozenten = User::findMany($dozenten_ids, "ORDER BY Nachname, Vorname");
```


Der Vorteil bei diesem Vorgehen ist, dass nicht so viele Datenbankabfragen getätigt werden, als wenn alle Benutzer-Objekte einzeln geladen würden. Denn findMany() führt nur eine einzige SQL-Abfrage aus, deren Ergebnisset dann automatisch zu Objekten vom Typ User wird.

### Definition von Relationen


| Variable | Beschreibunt |
| ---- | ---- |
|assoc_foreign_key |Definiert die Spalte der zweiten Tabelle, mit der der Key (Default Primary Key) des Grundobjekts verglichen wird |

#### Beispiel zur Definition von Relationen

Mit der SimpleORMap ist es sehr einfach, eine Baumstruktur abzubilden, wie das folgende Beispiel zeigt. Im Beispiel wird vorausgesetzt, dass die verwendete Tabelle sample_table eine Spalte id besitzt, welche den Primärschlüssel darstellt. Ebenso wird vorausgesetzt, dass eine Spalte parent_id existiert, welche das jeweilige Elternelement eines Tabelleneintrags referenziert.

```php
class SampleSorm extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'sample_table';

        // Kindverknüpfung definieren
        $config['has_many']['children'] = [
            'class_name' => SampleSorm::class,
            'assoc_func' => 'findByParent_id'
        ];

        // Elternverknüpfung definieren
        $config['belongs_to']['parent'] = [
            'class_name' => SampleSorm::class,
            'foreign_key' => 'parent_id'
        ];
        parent::configure($config);
    }
}
```

Für jedes Objekt der Klasse SampleSorm können nun dessen Kindelemente und dessen Elternelement als SimpleORMap-Objekte direkt erreicht werden.

### Joins

Auch Joins sind mit SimpleORMap möglich. Diese können den Schreibaufwand stark verringern, wenn die Auswahlkriterien für Objekte anhand anderer Tabellen festgelegt werden sollen.

Zur Benutzung eines Joins wird einfach die statische Methode findBySql() derjenigen SimpleORMap-Klasse aufgerufen, welche den gewünschten Objekttyp abbildet. Das Ergebnis liegt dann im gewünschten Objekttyp vor.

Im Gegensatz zu den bisher vorgestellten Aufrufen von findBySql() muss bei der Verwendung von Joins der SQL-Code ab der JOIN-Anweisung angegeben werden. Dies bedeutet, dass nur der Teil "SELECT FROM tabellenname" der SQL-Anweisung von der SimpleORMap-Klasse generiert wird. Der Rest der Anweisung muss manuell geschrieben werden.

#### Beispiel
Im folgenden Beispiel werden alle Veranstaltungstermine eines Teilnehmers abgerufen:

```php
<?php
$courseDates = CourseDate::findBySQL(
      "LEFT JOIN seminar_user "
    . "ON (seminar_user.Seminar_id = termine.range_id ) "
    . "WHERE (seminar_user.user_id = :user_id )",
    ['user_id' => $user_id]
);
```


### Weitere Dokumente

* [Präsentationsfolien SORM 1](/pdf/entwicklerworkshop2013-activerecord.pdf)
* [Präsentationsfolien SORM 2](/pdf/entwicklerworkshop2014-attack_of_the_sorm.pdf)
* [Präsentationsfolien SORM 3](/pdf/entwicklerworkshop2015-sorm_sucks.pdf)
