---
id: troubleshooting
title: Troubleshooting
sidebar_label: Troubleshooting
---

### PHP generell

#### Falsche Zeitdifferenzen? Bei Zeitdifferenzen gmdate() statt date() verwenden!

Zeitangaben in Stud.IP werden häufig als Sekunden in der Unix-Zeit (Sekunden seit 1.1.1970 0:00:00 Uhr UTC) angegeben. Da es sich um Zeitstempel in normalen positiven Ganzzahlen handelt, lässt sich problemlos damit rechnen und durch eine einfache Subtraktion die Zeitdifferenz zwischen zwei Zeitangaben bilden.

Da die Differenz zweier Zeitangaben in der Unix-Zeit, also in UTC, vorliegt, muss zur Ausgabe statt date() die Funktion gmdate() verwendet werden, da date() die Zeitzone des übergebenen Zeitstempels mitbetrachtet. Am 1.1.1970 galt die mitteleuropäische Zeitzone (UTC + 1 Stunde), wodurch date() eine Stunde auf die Differenz aufaddiert. gmdate() hingegen ignoriert die Zeitzone und liefert die korrekte Zeitdifferenz.

### Stud.IP Klassen und Methoden

#### in URLs mit Parametern wird `&` durch `&amp;` ersetzt

Warscheinlich wurde die Methode getLink() der URLHelper-Klasse verwendet. Der Unterschied zwischen getLink() und getURL() besteht darin, dass getLink() alle Zeichen, welche in HTML-Code problematisch sein könnten, durch ihre HTML-Entitäten ersetzt. Die HTML-Entität von `&` ist `&amp;` (für ampersand).

Zur Lösung des Problems wird getLink() einfach durch getURL() ersetzt.


### SimpleORMap
#### Fehlermeldung "Invalid argument supplied for foreach()" in SimpleORMap

Es tauchen die folgenden Fehlermeldungen auf:

```shell
Warning: Invalid argument supplied for foreach() in /var/www/studips/studip-trunk/lib/models/SimpleORMap.class.php on line 1619

Warning: in_array() expects parameter 2 to be array, null given in /var/www/studips/studip-trunk/lib/models/SimpleORMap.class.php on line 1311
```

Das Problem tritt auf, wenn auf eine Datenbanktabelle zugegriffen wird, die keinen Primärschlüssel (welcher meistens "id" heißt) besitzt. Um einen Primärschlüssel nachzurüsten, führt man in der MySQL-Konsole folgenden Befehl auf die betroffene Tabelle aus:
`ALTER TABLE tabellenname ADD PRIMARY KEY(id);`

Anschließend muss der Stud.IP-Cache gelöscht werden. Liegt dieser unter /tmp/studip-cache/ so werchselt man in dieses Verzeichnis und führt `rm -rf ./*` aus, um den gesamten Inhalt von /tmp/studip-cache/ zu löschen.

#### Fehlermeldung "Passed variable is not an array or object, using empty array instead" bei Benutzung einer SimpleORMap-Relation

Dieses Problem kann auftauchen, wenn man versucht, im Plugin auf eine SimpleORMap-Relation zuzugreifen, welche mit "has_many" definiert wurde. Der Fremdschlüssel wurde über den Parameter "foreign_key" korrekt gesetzt, der assoziierte Fremdschlüssel (Parameter "assoc_foreign_key") wurde ebenfalls richtig gesetzt, wird aber nicht beachtet.

Das Problem besteht darin, dass im Controller, in welchem auf die Relation zugegriffen wird, die Zielklasse der Relation nicht eingebunden wurde. Bindet man die Zielklasse der "has_many" Relation ein, so verschwindet das Problem. Andere Arten von SimpleORMap-Relationen können ebenfalls von dem Problem betroffen sein.


#### RuntimeException mit der Nachricht: "assoc_func: ModellKlasse::findBySomeAttribute is not callable"

Bei der Benutzung einer "has_many"-Relation kann diese Fehlermeldung beim Zugriff auf die Relation auftauchen. Das Problem besteht darin, dass das Modell, welches das Ziel der Relation darstellt, nirgendwo eingebunden ist und somit die Modellklasse nicht bekannt ist.

Zur Lösung wird einfach die Modellklasse mittels require_once eingebunden. Am besten wird dies in der Datei getan, in welcher auf die Relation zugegriffen wird.


#### Meine Dateitypen oder ähnliche vom Plugin bereitgestellte Strukturen sind in der API nicht verfügbar

Die APIs (REST und JSON) laden nicht alle Systemplugins. Wenn über ein Systemplugin neue Datenstrukturen bereitgestellt werden, muss das Plugin auch das Interface `RESTAPIPlugin` bzw. `JsonApi\Contracts\JsonApiPlugin` implementieren, damit diese Strukturen auch innerhalb von API-Aufrufen verfügbar sind.
