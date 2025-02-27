---
id: studip-pdo
title: StudipPDO
sidebar_label: StudipPDO
---


Stud.IP benutzt für die Datenbankzugriffe eigene, von `PDO` und `PDOStatement` abgeleitete Klassen. `DBManager::get()` liefert immer automatisch eine Instanz von `StudipPDO` zurück. Man kann dieses Objekt genauso benutzen wie ein Standard-PDO Objekt, darüberhinaus enthält es aber noch ein paar Erweiterungen, die den Umgang mit der Datenbank erleichtern.

[StudipPDO](https://gitlab.studip.de/studip/studip/-/blob/main/lib/classes/StudipPDO.class.php) 

## Automatische Parametererkennung, Zusätzliche Parameter Typen

Die an eine Statement übergebenen Parameter werden anhand des PHP Typs auf einen passenden PDO Parametertypen gemappt. D.h. z.B., wenn ein übergebener Parameter `NULL` in PHP ist, wird er auch in der Abfrage als ein SQL `NULL` ersetzt. Es stehen noch neue Parameter Typen zur Verfügung:

### `StudipPDO::PARAM_ARRAY`
Der Parametertyp erlaubt die Übergabe eines Arrays, das dann zu einer Wertliste expandiert wird. Zu benutzen mit dem `IN ()` Konstrukt in SQL.

Beispiel:
```php
$st = DBManager:get()->prepare("SELECT * FROM seminare WHERE status IN(?)");
$st->execute([[1,2,3,4]]);
```

wird ausgeführt als 
```sql
SELECT * FROM seminare WHERE status IN (1, 2, 3, 4)
```

### `StudipPDO::PARAM_COLUMN`
Dieser Parametertyp erlaubt die Übergabe eines Strings, der ohne weitere Behandlung in die SQL Abfrage eingesetzt wird. Damit kann z.B. ein Parameter im `ORDER BY` Teil benutzt werden. Weil das ein Sicherheitsproblem sein kann, wird in jedem Fall jedes nicht-Wort Zeichen aus dem Parameter herausgefiltert.

Beispiel:
```php
$st = DBManager:get()->prepare("SELECT * FROM auth_user_md5 WHERE perms IN (:perms) ORDER BY :sorter");
$st->bindValue(':status', ['tutor','dozent']);
$st->bindValue(':sorter', 'Nachname', StudipPDO::PARAM_COLUMN);
```

wird ausgeführt als
```sql
SELECT * FROM auth_user_md5 WHERE perms IN ('tutor','dozent') ORDER BY Nachname
```

## Prepared Statements to go

Da Prepared Statements in der Anwendung etwas umständlich sind, wurde ein Abkürzung für häufig benutzte Varianten eingebaut. Das erspart auch die Eingabe der PDO Konstanten für den fetch-mode. Dazu stehen diese fetch Methoden außerdem direkt im StudipPDO Objekt zur Verfügung stellen, mit der Möglichkeit eine Query und die Parameter direkt mit anzugeben.

Beispiele:
```php
<?php
$db = DBManager::get();

//für DELETE UPDATE etc
$db->execute("DELETE FROM xxx WHERE id=?", [$id]);

//nur das erste Ergebnis der Abfrage als assoc array holen
$db->fetchOne("SELECT * FROM xxx WHERE id=?", [$id]);

//nur den Wert der ersten Spalte des ersten Ergebnisses der Abfrage holen
$db->fetchColumn("SELECT id FROM xxx WHERE id=?", [$id]);

//alles als array holen, erste Spalte als Schlüssel, zweite als Wert
$db->fetchPairs("SELECT id,value FROM xxx WHERE id IN (?)", [$ids]);

//alles als array holen, nur die Werte der ersten Spalte
$db->fetchFirst("SELECT value FROM xxx WHERE id IN (?)", [$ids]);

//alles als assoc array holen,
$db->fetchAll("SELECT * FROM xxx WHERE id IN (?)", [$ids]);

//alles als assoc array holen, die erste Spalte wird zum Schlüssel, 
//der Rest wird gruppiert, wenn zu einem Schlüssel mehrere Zeilen vorhanden sind,
// wird nur die erste zurückgegeben
$db->fetchGrouped("SELECT id, xxx.* FROM xxx WHERE id IN (?)", [$ids]);

//alles als assoc array holen, die erste Spalte wird zum Schlüssel, die zweite wird 
//gruppiert und als array zurückgegeben
//(Anm. Arne:) ersetzt: fetchAll(PDO::FETCH_COLUMN|PDO::FETCH_GROUP)
$db->fetchGroupedPairs("SELECT id, value FROM xxx WHERE id IN (?)", [$ids]);

//dritter Parameter kann bei allen Methoden die Arrays liefern ein callable sein
//z.B. um bei den Gruppierungen eine Aggregatfunktion zu realisieren
$count = function ($a) {
    return count($a);
};
$db->fetchGroupedPairs("SELECT id, value FROM xxx WHERE id IN (?)", [$ids], $count);

//das Ergebnis wäre das gleiche wie hier
$db->fetchPairs("SELECT id, COUNT(*) FROM xxx WHERE id IN (?) GROUP BY id", [$ids]);
```
