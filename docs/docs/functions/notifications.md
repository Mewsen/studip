---
id: notifications
title: Notifications
sidebar_label: Notifications
---

## Notifications - ein Eventsystem für Stud.IP

Plugins können in Stud.IP schon eine Menge. Sie können für jede Veranstaltung als Reiter eingefügt werden, sie können sich in die Homepage eines Nutzers mogeln und dort eine eigene Rubrik darstellen, sie können die Navigation umstellen und auf diese Weise komplette Fuktionen von Stud.IP wie das Forum ersetzen. Aber manchmal braucht man auch kleine Plugins, die nicht ganze Seiten verändern, sondern nur etwas tun, wenn etwas ganz bestimmtes passiert.

Zum Beispiel: Wenn ein Nutzer eine Veranstaltung abonniert und mindestens auf der Warteliste steht, soll eine Person in der Buchhaltung, die Stud.IP fremd ist, eine automatische E-Mail bekommen. So eine Funktionalität ist in Stud.IP nicht integriert bisher, und der Entwickler, der sich damit befasst, will möglichst wenig im Quellcode von Stud.IP verändern. Er muss nun nur die betreffende Codezeile finden und setzt dort ein Event mit einem beliebigen Namen. Das geschieht so:

`NotificationCenter::postNotification("user_accepted_to_seminar", $username);`

Das Event heißt nun "user_accepted_to_seminar" und wird an dieser Stelle immer aufgerufen. $username ist einfach eine Variable, die in dem Kontext verfügbar ist. In diesem Fall ist das natürlich der Nutzername und der ist einfach wichtig, damit in der später verschickten Email auch drin steht, welcher Nutzer sich angemeldet hat. Aber theoretisch könnte man diese Variable auch weg lassen.

Jetzt muss noch das Plugin geschrieben werden. Das Plugin muss vorher auf der Seite initialisiert sein, damit es eine Funktion für dieses Event registrieren kann. Ich schlage da natürlich ein SystemPlugin vor, das im Konstruktor sich selbst registriert:

```php
class MyPlugin extends StudIPPlugin implements SystemPlugin {
    public function __construct() {
        NotificationCenter::addObserver($this, "send_mail_to_accepted_user", "user_accepted_to_seminar");
    }

    public function send_mail_to_accepted_user($username) {
        /* hier das, was getan werden soll, wenn der Nutzer registriert ist */
    }
}
```

An dieser Stelle wird klar, dass die übergebene Funktion nicht einfach eine Funktion sein sollte, sondern eine Methode eines Objektes. Zuerst wird also das spezifische Objekt übergeben und als zweiter Parameter der Name der Methode. Erst der dritte Parameter ist der Name des Events. In diesem Fall ist das Plugin faul und registriert sich selbst für das Event durch $this. Aber es kann auch ein anderes Objekt registriert werden als ein Plugin.

Ab Stud.IP 4.2 ist es auch möglich, [Closures](php.net/manual/class.closure.php) als Event-Handler über die Funktion `on()` zu registrieren. Die Syntax folgt dabei der jQuery-Notation:

```php
NoticationCenter::on('UserDidDelete', function ($event, $user) {
    // ...
});
```

In Stud.IP 4.2 und 4.3 war hier nur die Übergabe von Closures möglich. Ab Stud.IP 4.4 können nahezu alle Callable-Typen übergeben werden. Lediglich Strings können nicht vernünftig abgebildet werden und sind deshalb zu vermeiden.

### Rückgabewerte von Notifications

Leider sind Notofications nicht dazu gedacht, etwas zurückzugeben. Aber wenn man Plugins baut, die etwas tun, möchte man vielleicht eine visuelle Rückmeldung geben wie eine Fehler- oder Erfolgsmeldung.

Man kann von einem Observer keine Arrays oder andere Datenobjekte zurück bekommen. Aber es steht dem Observer natürlich frei, für sich die print oder echo-Anweisung zu verwenden, um Text an den Nutzer zu schreiben. Dies ist die einzige derzeit mögliche Art der Rückmeldung vom Observer; der eventuelle Rückgabewert der übergebenen Methode wird zurzeit komplett ignoriert.

Jetzt kann also der Observer seiner Erfolgsmeldung schreiben a'la

`echo '<div class="messagebox messagebox_success">Hurra! Daten erfolgreich übernommen.</div>';`

Beachten muss man allerdings, dass dieses Echo vielleicht zur falschen Zeit kommt, wenn das Skript, das die Notification postet, mit Templates arbeitet oder gar ein Trails-Skript ist. Das ist natürlich nicht schlecht, führt nur dazu, dass die Fehlermeldung unter Umständen noch vor dem einleitenden `<html>`, also ganz ganz oben auf der Seite steht. Um das zu umgehen, kann das ausführende Skript schreiben:

```php
ob_start();
NotificationCenter::postNotification("user_accepted_to_seminar", $username);
$message = ob_get_contents();
ob_end_clean();
```

Dank der Gnädigkeit von PHP funktioniert das auch, wenn im Hintergrund noch ein anderer Outputbuffer (für das das "ob_" steht) aktiv ist. Auf diese Weise kann man also zumindest einen String von den Observern zum ausführenden Skript bringen.

**WARNUNG:** Jeder Gedanke, über diesen String serialisierte Arrays oder PHP-Objekte durchzuschleusen, ist naheliegend aber schmutzig, weil es ja immer sein kann, dass noch ein zweiter oder dritter Observer etwas sendet. Und zwei oder drei serialisierte Objekte in einem String lassen sich zumindest nicht so einfach mit einem `unserialize()` herausfischen. Also lasst sowas lieber gleich bleiben.

Tipp: Man kann über Plugins nicht nur Observer für Notifications definieren, sondern natürlich auch Notifications selbst posten. Damit kann man quasi Plugins für das eigene Plugin ermöglichen. Dadurch können ganz konkret zwei Plugins miteinander kommunizieren, wenn beide installiert sind. Und wenn nur eines installiert ist, passiert nichts. Das kann unter Umständen ziemlich nützlich sein.


### Notifications selber erzeugen

#### Wie benenne ich eine Notification?

Dazu gibt es bisher keine gültige Richtlinie. Offenbar scheint sich aber abzuzeichnen, dass der Name in CamelCase geschrieben und eine Handlung beschreibt. Da das NotificationCenter an die Originalimplementation aus NextStep angelehnt ist, macht es vermutlich Sinn, ähnliche Konventionen wie dort zu verwenden. So beschreibt der Artikel [NSNotfication Not Working, But Looks Right? :: Find That Bug!](http://www.goodbyehelicopter.com/?p=259) ein BestPractice, wonach die Notifications:

`JJColorChange`

und:

`JJColorChanged`

nur schwer im Code zu unterscheiden sind, da sie sich lediglich um einen Buchstaben unterscheiden. Die Empfehlung lautet statt:

* CamelCase
* CamelCased

lieber:

* CamelCase
* CamelDidCase
* CamelWillCase
* CamelByCase

zu verwenden. Aus diesen Gründen wurden bei den Notifications für Dateien, Wikiseiten und Forumsbeiträgen die Namen entsprechend gewählt.



### Liste der in Stud.IP eingebauten Notifications

Hier folgt nun eine Liste der verfügbaren Notifications, sortiert nach Bereichen. Bei der Parameterliste ist zu beachten, dass bei der durch die Notification aufgerufene Funktion oder Methode der erste Parameter den Namen der Notification beinhaltet.

#### Veranstaltungen

##### UserDidEnterCourse

**Zusätzliche Parameter für Observer-Methode:** 
* Veranstaltungs-ID
* Nutzer-ID

**Sendebedingung:** Ein Nutzer trägt sich in eine Veranstaltung ein.

##### UserDidLeaveCourse

**Zusätzliche Parameter für Observer-Methode:**
* ?

**Sendebedingung:** ?

##### CourseDidChangeSchedule

**Zusätzliche Parameter für Observer-Methode:**
* ?

**Sendebedingung:** ?

##### CourseDidGetMember

**Zusätzliche Parameter für Observer-Methode:**
* ?

**Sendebedingung:** ?




#### Dateien

##### DocumentWillCreate

**Zusätzliche Parameter für Observer-Methode:**
* StudipDocument-Instanz

**Sendebedingung**: Eine Datei wird hochgeladen und wurde noch nicht angelegt.

##### DocumentDidCreate

**Zusätzliche Parameter für Observer-Methode:**
* StudipDocument-Instanz

**Sendebedingung**: Eine Datei wurde hochgeladen und angelegt.

##### DocumentWillUpdate

**Zusätzliche Parameter für Observer-Methode:**
* StudipDocument-Instanz

**Sendebedingung**: Eine Datei wird aktualisiert.

##### DocumentDidUpdate

**Zusätzliche Parameter für Observer-Methode:**
* StudipDocument-Instanz

**Sendebedingung**: Eine Datei wurde aktualisiert.

##### DocumentWillDelete

**Zusätzliche Parameter für Observer-Methode:**
* StudipDocument-Instanz

**Sendebedingung**: Eine Datei wird gelöscht.

##### DocumentDidDelete

**Zusätzliche Parameter für Observer-Methode:**
* StudipDocument-Instanz

**Sendebedingung**: Eine Datei wurde gelöscht.


#### Forum

##### PostingWillCreate

**Zusätzliche Parameter für Observer-Methode:**
* ID des Forenbeitrags

**Sendebedingung**: Ein Forenbeitrag wurde verfasst, aber noch nicht gespeichert.

##### PostingDidCreate

**Zusätzliche Parameter für Observer-Methode:**
* ID des Forenbeitrags

**Sendebedingung**: Ein Forenbeitrag wurde verfasst und gespeichert.

##### PostingWillUpdate

**Zusätzliche Parameter für Observer-Methode:**
* ID des Forenbeitrags

**Sendebedingung**: Ein Forenbeitrag wurde geändert, die Änderung aber noch nicht gespeichert.

##### PostingDidUpdate

**Zusätzliche Parameter für Observer-Methode:**
* ID des Forenbeitrags

**Sendebedingung**: Ein Forenbeitrag wurde geändert und die Änderung gespeichert.

##### PostingWillDelete

**Zusätzliche Parameter für Observer-Methode:**
* ID des Forenbeitrags

**Sendebedingung**: Ein Forenbeitrag wird gelöscht, der Löschvorgang hat jedoch noch nicht begonnen.

##### PostingDidDelete

**Zusätzliche Parameter für Observer-Methode:**
* ID des Forenbeitrags

**Sendebedingung**: Ein Forenbeitrag wurde gelöscht.

#### Literaturverwaltung

##### LitListDidInsert

**Zusätzliche Parameter für Observer-Methode:**
* ?

**Sendebedingung:** ?

##### LitListDidUpdate

**Zusätzliche Parameter für Observer-Methode:**
* ?

**Sendebedingung:** ?

##### LitListDidDelete

**Zusätzliche Parameter für Observer-Methode:**
* ?

**Sendebedingung:** ?

##### LitListElementDidInsert

**Zusätzliche Parameter für Observer-Methode:**
* ?

**Sendebedingung:** ?

##### LitListElementDidUpdate

**Zusätzliche Parameter für Observer-Methode:**
* ?

**Sendebedingung:** ?

##### LitListElementDidDelete

**Zusätzliche Parameter für Observer-Methode:**
* ?

**Sendebedingung:** ?


#### Blubber

##### PostingHasSaved

**Zusätzliche Parameter für Observer-Methode:**
* ?

**Sendebedingung:** ?


#### Nachrichten

##### MessageDidSend

**Zusätzliche Parameter für Observer-Methode:**
* ?

**Sendebedingung:** ?


#### Nutzermigration

Beim Migrieren von einem Nutzeraccount in einen anderen wird vor der Aktion die Notification `UserWillMigrate` und nach der Aktion die Notification `UserDidMigrate` gesendet. Beide Notifications erhalten die Id des Accounts, {+aus+} dem migriert werden soll als `subject`, während die Id des Accounts, {+in+} den migriert werden soll, als `$userdata` übergeben wird.


#### Wiki

Wenn eine Wikiseite verfasst wurde, wird vor dem tatsächlichen Speichern die Notification `PostingWillCreate` und nach dem Speichern `PostingDidCreate` versendet. Das `subject` ist ein Array mit `range_id` und `keyword` der Wikiseite.

Wenn eine Wikiseite verändert wurde, wird vor dem tatsächlichen Speichern die Notification `PostingWillUpdate` und nach dem Speichern `PostingDidUpdate` versendet. Das `subject` ist ein Array mit `range_id` und `keyword` der Wikiseite.

Auch wenn eine Wikiseite gelöscht wird, gibt es Notifications: `PostingWillDelete` und `PostingDidDelete`. Auch dort wird der Vollständigkeit halber ein Array mit `range_id` und `keyword` der Wikiseite übergeben.


#### Veranstaltungsübersicht

Beim Klick auf den Link "Alles als gelesen markieren" auf der Veranstaltungsübersicht wird vor der Aktion die Notification `OverviewWillClear` und danach die Notification `OverviewDidClear` gesendet. Beide Notifications erhalten die Id des Nutzers als `subject`.


#### Modulverwaltung

**Notifications: `CourseRemovedFromModule` und `CourseAddedToModule`**
```php
NotificationCenter::postNotification(
    'CourseRemovedFromModule', 
    $studyarea, 
    ['module_id' => $sem_tree_id, 'course_id' => $seminar_id]
);

NotificationCenter::postNotification(
    'CourseAddedToModule', 
    $studyarea, 
    ['module_id' => $sem_tree_id, 'course_id' => $seminar_id]
);
```

<TODO: elmar oder anoack>


#### Sidebar

##### SidebarWillRender

**Zusätzliche Parameter für Observer-Methode:**
* ?

**Sendebedingung:** ?


#### Systemkonfiguration

**Notification: `ConfigValueChanged`**

Dies Notification wird versendet, wenn sich ein in der Klasse `Config` enthaltener Parameter geändert hat. Als `subject` wird die Config-Instanz mitgegeben und als `userdata` der neue und alte Wert.

<TODO: elmar oder anoack>
 
```php
NotificationCenter::postNotification('ConfigValueChanged', 
    $this, 
    [
        'field' => $field, 
        'old_value' => $old_value, 
        'new_value' => $value_entry->value
    ]
);
```
