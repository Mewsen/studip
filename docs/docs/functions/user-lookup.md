---
id: user-lookup
title: Benutzung der Klasse UserLookup
sidebar_label: Benutzung der Klasse UserLookup
---

## 

Die neue Klasse (ab Version 2.1) dient zur Filterung von Nutzern nach speziellen Kriterien.


### Allgemeines
Entwickelt wurde diese Klasse, um eine einheitliche, wiederverwendbare Lösung zum Filtern bzw. Suchen von Benutzern anhand bestimmter Kriterien zu schaffen.

Es können verschiedene Kriterien (auch gleichen Typs mit unterschiedlichen Filterwerten) kombiniert werden. Intern wird schlussendlich die Schnittmenge aller einzelnen angewandten Filterkriterien gebildet.

Nach folgende Kritieren kann standardmässig gefiltert werden:

* Studienfach (*fach*)
* Studienabschluss (*abschluss*)
* Studienfachsemester (*fachsemester*)
* Einrichtungszugehörigkeit (*institut*)
* Nutzerstatus (*status*)

### Methoden der Klasse `UserLookup`
An dieser Stelle sind die öffentlichen Operation der Klasse `UserLookup` dokumentiert. 

#### Klassenmethoden
* **UserLookup::getValuesForType($type)**
Liefert alle möglichen Werte für einen gegebenen Typ zurück. Diese Werte werden mit den Standardeinstellungen für eine Stunde im Cache gehalten, um die Datenbank zu entlasten.

* **UserLookup::addType($name, $values_callback, $filter_callback)**
Fügt ein neues Filterkriterium *type* hinzu. Sollte ein Kriterium mit dem gleichen Namen schon existieren, so wird es überschrieben.

*$values_callback* gibt an, welche Methode genutzt wird, um alle möglichen Werte für dieses Kriterium zurückzuliefern. Diese Methode **sollte** nach Möglichkeit ein eindimensionales, assoziatives Array (Schlüssel = Id des Wertes) zurückgeben.

*$filter_callback* gibt an, welche Methode genutzt wird, um das Filterkriterium anzuwenden. Diese Methode **muss zwingend** eine Liste von Benutzer-Ids zurückgeben, auf die das Filterkriterium zutrifft.

Die beiden Callback-Parameter müssen valide [PHP-Callbacks](http://php.net/manual/language.pseudo-types.php#language.types.callback) sein.

#### Instanzmethoden
* **setFilter($type, $value)**
Fügt der momentanen Auswahl an Filtern einen neuen Filter des Kriteriums *$type* hinzu. *$value* kann hierbei entweder ein atomarer Wert oder zur Vereinfachung auch ein Array von Werten sein.

* **clearFilter**
Löscht alle gesetzten Filterkriterien.

* **execute($flags)**
Wendet die aktuelle Auswahl an Filtern an. Zurückgegeben wird standardmässig ein Array, das alle passenden Benutzer-Ids enthält.
Die Rückgabe kann über optionale Flags noch weiter gesteuert werden:

| Flag | Beschreibung |
| ---- | ---- |
|FLAG_SORT_NAME|Die zurückgegebenen Ids werden nach Namen der damit verbundenen Benutzern sortiert (aufsteigend nach Nachname und Vorname). |
|FLAG_RETURN_FULL_INFO|Anstatt eines Array mit den reinen Benutzer-Ids wird ein assoziatives Array zurückgegeben, das die Benutzer-Id als Schlüssel enthält und als Wert ein Array mit den folgenden Angaben des jeweiligen Benutzers: *username*, *Vorname*, *Nachname*, *Email* und *perms* |


### Beispiele

Hier sollten einige kleine Bespiele für die Verwendung des `UserLookup` aus dem praktischen Einsatz in Stud.IP gesammelt werden. 

```php
# Create a new UserLookup object
$user_lookup = new UserLookup;

# Filter all users in their first to sixth fachsemester
$user_lookup->setFilter('fachsemester', range(1, 6));

# Filter all users with the fach 'fach123' or 'fach456'
$user_lookup->setFilter('fach', array('fach123', 'fach456'));
/* Equivalent: 
$user_lookup->setFilter('fach', 'fach123');
$user_lookup->setFilter('fach', 'fach456');
*/

# Filter all users that have an 'autor' or 'tutor' permission
$user_lookup->setFilter('status', array('autor', 'tutor'));
   
# Get a list of all matching user ids (sorted by the user's names)
$user_ids = $user_lookup->execute(UserLookup::FLAG_SORT_NAME);

# Get another list of all matching user ids but this time we want
# the complete unordered dataset
$user_ids = $user_lookup->execute(UserLookup::FLAG_RETURN_FULL_INFO);
```
