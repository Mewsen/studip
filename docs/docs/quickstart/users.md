---
title: Users
---


User sind über zwei systemweit eindeutige Kennzeichen zu identifizieren: Die `user_id` und der `username`. Der Username, eine vom Nutzer selbstgewählte oder aus einem zur Authentifizierung genutzten System übernommene Zeichenkette kann sich ändern, die user_id, ein von Stud.IP generierter MD5-Hash nicht.

Informationen über den Nutzer, für den das Script gerade ausgeführt wird, findet sich im global bekannten Objekt `$user`, das von `page_open()` initialisiert wird.

Darüber lassen sich verschiedene Informationen gewinnen:

| Wert | Beschreibung
| ---- | ---- |
| `$GLOBALS['user']->id` | `user_id` des aktuellen Nutzers |
| `$GLOBALS['user']->username` | `username` des aktuellen Nutzers |

:::warning Anmerkung

In Stud.IP-Installationen älter als v2.2 wurde inzwischen veraltetet so auf diese Daten zugegriffen: `$auth->auth["uid"]` bzw. `$auth->auth["uname"]`

:::

## Berechtigungen

Jeder Nutzer hat verschiedene Rechte im System, die über das global bekannte Objekt `$perm` zugänglich sind,
das ebenfalls von [`page_open()`](quickstart/Allgemeine-Struktur#page_open) initialisiert wird.

### Globale Rechte

Die globale Rechtestufe wird über die Methode `$perm->get_perm()` erfragt, bzw. über `$perm->have_perm(*<rechtestufe>*)` geprüft. 
Mögliche Werte für Rechtestufen sind:
* `nobody`
* `user`
* `author`
* `tutor`
* `dozent`
* `admin`
* `root`. 

Die Methode`$perm->check(*<rechtestufe>*)` wird wie have_perm verwendet, liefert jedoch im Misserfolgsfall nicht `FALSE` zurück, sondern erzeugt eine Stud.IP-Seite mit einer Zugriffsfehlermeldung und beendet die Ausführung des aktuellen Scripts.

`$perm->have_perm(*<rechtestufe>*)` und `$perm->check(*<rechtestufe>*)` prüfen nicht exakt, sondern betrachten die übergebene Stufe als Mindeststufe. 

`$perm->have_perm('dozent')` liefert also `true`, wenn der aktuelle Nutzer die globale Rechtestufe 'dozent', 'admin' 
oder 'root' hat.


:::tip ist der eingeloggte Nutzer Admin?

```php
if ($perm->have_perm('admin')) {
    ...
}
```
:::

:::tip welche Rechtestufe hat der Nutzer global?

```php
$p = $perm->get_perm();
```
:::

:::note Datenbankinfo

Die globale Rechtestufe ist in `auth_user_md5.perm` gespeichert.

:::

### Sonstige Rechte

In jeder Veranstaltung und jeder Einrichtung kann ein Nutzer über Rechte verfügen, die von seiner globalen Rechtestufe abweichen können. 
Der Zugriff auf veranstaltungs- und einrichtungsbezogene Rechtestufen geschieht über die Methoden 

`$perm->get_studip_perm($range_id)` und `$perm->have_studip_perm(*<rechtestufe>*,$range_id)`. 

Die `range_id` ist die `seminar_id` oder `institut_id` der zu überprüfenden Veranstaltung oder Einrichtung (jeweils MD5-Hashes).

`$perm->have_studip_perm(*<rechtestufe>*,$range_id)` prüft, ob der Nutzer *mindestens* über die angefragte Rechtestufe in der Veranstaltung 
bzw. der Einrichtung verfügt (s.o.).


:::tip ist der eingeloggte Nutzer mindestens Tutor in Veranstaltung `$course_id`?

```php
if ($perm->have_studip_perm('tutor', $course_id)) {
    ...
}
```

:::

:::tip welche Rechtestufe hat der Nutzer in der Veranstaltung `$course_id`?

```php
$p = $perm->get_studip_perm($sem_id);
```

:::

:::note Datenbankinfo

Veranstaltungsbezogene Rechtestufen sind in `seminar_user.perms` abgelegt, die einrichtungsbezogenen Rechtestufen in `institute_user.perms`.

:::


:::warning

An vielen alten Stellen des Stud.IP-Codes wird auf die globale Variable `$rechte` zugegriffen, in der als boolscher Wert abgelegt ist, ob der aktuelle Nutzer in der gewählten Einrichtung oder Veranstaltung mindestens über Tutorenrechte verfügt. Diese Variable sollte in neuem Code nicht mehr verwendet werden. Stattdessen ist der Ausdruck `$perm->have_studip_perm('tutor', $SessSemName[1])` zu verwenden.

:::


### UserManagement

Die Klasse [`UserManagement`](https://gitlab.studip.de/studip/studip/-/blob/main/lib/classes/UserManagement.class.php) kapselt viele Aktionen, die mit Nutzerdaten regelmäßig durchgeführt werden. Sie ist vor allem entstanden, um die vielfältigen Aktionen rund um Neuanlegen und Löschen eines Nutzers an einer Stelle zusammenzufassen und z.B. Authentifizierungsplugins zugänglich zu machen.

Eine Detaildokumentation der verfügbaren Methoden sollte direkt dem Quellcode entnommen werden: https://gitlab.studip.de/studip/studip/-/blob/main/lib/classes/UserManagement.class.php

Die von der Klasse `UserManagement` berührten Daten liegen vor allem in den Tabellen `auth_user_md5` und `user_info`.

:::warning

Jeglicher Code, der INSERT oder DELETE Statements gegen die Tabellen `auth_user_md5` und `user_info` absetzt, ist als veraltet zu betrachten. 

Für alle Aktionen zum Erzeugen und Löschen von Nutzern sollte die Klasse `UserManagement` verwendet werden.

:::

### Voreinstellungen

Die Nutzer können sich "ihr" Stud.IP mit einer Reihe von persönlichen Konfigurationsoptionen verändern. Sei es die 
nach dem Login angezeigte Startseite, die Sprache, die bevorzugte Sortierung aus der Seite "Meine Seminare".

Der für alle Neuentwicklungen vorgesehene Weg führt über die Klasse [`UserConfig`](https://gitlab.studip.de/studip/studip/-/blob/main/lib/classes/UserConfig.class.php). Darüber lassen sich nutzerspezifische Einstellungen abrufen und speichern.



:::tip Methoden und typische Verwendungsweise der Klasse sind:

```php
// construct without implicit user/key
$uc=UserConfig()
// construct and set implicit user/key
$uc=UserConfig($someuser_id, 'somekey');

// get value for "someuser" and "somekey"

$v=$uc->getValue();    

// get value for "otheruser" and "somekey"

$v=$uc->getValue($otheruser_id, $somekey);

// get value for "someuser" and "otherkey"
$v=$uc->getValue(null, 'otherkey'); 

// get value for "otheruser" and "otherkey"
$v=$uc->getValue($otheruser_id, 'otherkey'); 

// set value to "somevalue" for "someuser"/"somekey"
//... combinations of explicit user_id and key same as getValue(...) ...
$uc->setValue('somevalue'); 

// unset (delete from db) value for "someuser"/"somekey"
//... combinations of explicit user_id and key same as getValue(...) ...
$uc->unsetValue();

// delete all entries for a user (user_id explicit or implicit)
$uc->unsetAll($user_id); 

// get all entries for a user (user_id explicit or implicit)
$uc->getAll($user_id); 

// switch to "otheruser" for implict get/set
$uc->setUserId($otheruser_id);

// switch to "otherkey" for implict get/set 
$uc->setKey('otherkey');

```
:::

:::warning 

In früheren Codeteilen wurden Nutzereinstellung zumeist in dauerhaften Session-Variablen gespeichert.

:::
