---
id: multi-person-search
title: MultiPersonSearch-Klasse
sidebar_label: MultiPersonSearch-Klasse
---

`lib/classes/MultiPersonSearch.class.php` stellt eine Klasse bereit, mit der ein Dialog zum Hinzufügen von mehreren Personen erstellt werden kann. Ist JavaScript aktiviert, wird dazu ein [Modaler Dialog](ModalerDialog#toc5) geöffnet, anderenfalls wird ein Fallback angezeigt.

Attach:MultiPersonSearch.png

## Einbau im View

Mit dem folgenden Quelltext wird ein MultiPersonSearch Objekt erzeugt und als Link ausgegeben. 

```php
$mp = MultiPersonSearch::get('eindeutige_id')
    ->setLinkText(_('Beispiellink'))
    ->setTitle(_('Titel des Dialogs'))
    ->setDefaultSelectedUser($defaultSelectedUser)
    ->setExecuteURL($this->url_for('controller'))
    ->setSearchObject($searchObj)
    ->addQuickfilter(_('Name des Quickfilters'), $userArray)
    ->render();

print $mp;
```


### Übersicht wichtiger Methoden

* *setLinkText($text)* setzt den Name des Links, der den Dialog öffnet.
* *setTitle($title)* setzt den Namen des Dialogtitels.
* *setDescription($desc)* setzt die Beschreibung des Dialogs, die unter dem Titel angezeigt wird.
* *setDefaultSelectedUser($userArray)* setzt alle User, die bereits hinzugefügt sind (z. B. alle TeilnehmerInnen, die bereits in einer Veranstaltung eingetragen sind). *$userArray* ist ein Array bestehend aus User-Ids.
* *setDefaultSelectableUser($userArray)* setzt ein Menge von Personen, die standardmäßig auf der linken Seite des Dialogs angezeigt werden. *$userArray* ist ein Array bestehend aus User-Ids.
* *setExecuteURL($action)* setzt den Link des Controllers, der die Auswahl verarbeitet.
* *setSearchObject($searchType)* setzt ein *SearchType* Objekt (z. B: SQLSearch), dass zur Suche von Personen verwendet wird.
* *addQuickfilter($title, $userArray)* fügt einen Quickfilter, bestehend aus einem Titel und einem Array von User-Ids, hinzu.
* *setJSFunctionOnSubmit($function_name)* fügt eine JavaScript Funktion hinzu, die ausgeführt wird, sobald auf den Button zum Speichern geklickt wird.
* *setLinkIconPath($path)* setz ein Link-Icon (Standard-Wert: icons/16/blue/add/community.png).

## Verarbeitung
Um die über den Dialog ausgewählten Personen zu speichern, muss mittels `setExecuteURL($action)` eine entsprechende URL (z. B. zu einem Controller) bereitgestellt werden.

Im Controller kann nun mittels `load($name)` das `MultiPersonSearch` Objekt geladen werden. Die Funktion `getAddedUsers()` liefert ein Array mit allen neu ausgewählten User-Ids zurück.
```php
$mp = MultiPersonSearch::load('eindeutige_id');

foreach ($mp->getAddedUsers() as $userId) {
    do_something($userId);
}
```
