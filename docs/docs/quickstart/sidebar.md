---
id: sidebar
title: Zugriff auf die Sidebar
sidebar_label: Sidebar
---

Eine Sidebar wird anhand von Widgets aufgebaut, welche kleine, voneinander unabhängige Bausteine der Seitenleiste darstellen. Eine Sidebar ist ein Singleton, sodass die einzige Instanz der Sidebar-Klasse nur über die statische Methode `Get()` erreicht werden kann:

```php
<?php
$sidebar = Sidebar::Get();
```


#### Setzen des Titels der Sidebar

Dazu wird die Methode `setTitle()` verwendet:
```php
<?php
Sidebar::Get()->setTitle('Hallo Sidebar!');
```

Der Titel kann mittels der Methode `removeTitle()` wieder entfernt werden.


#### Setzen eines Bildes für die Sidebar

Im oberen Bereich der Sidebar ist Platz für eine Grafik. Um festzulegen, welche Grafik erscheint, wird der Pfad zur Grafik im assets-Ordner der Methode `setImage` übergeben:
```php
<?php
Sidebar::Get()->setImage('some/image');
```

Die Grafik kann mittels der Methode `removeImage()` wieder entfernt werden.


#### Setzen eines Avatars für den Kontext

Im Kopf der Sidebar gibt es die Möglichkeit, zusätzlich einen Avatar anzuzeigen, der zu dem angezeigten Kontext gehört. Dies geschieht über die Methode `setContextAvatar()`, der ein Objekt vom Typ `Avatar` übergeben wird:
```php
<?php
Sidebar::Get()->setContextAvatar(Avatar::getAvatar(User::findCurrent()->id));
```

Dieser Avatar kann mittels der Methode `removeContextAvatar()` wieder entfernt werden.


#### Hinzufügen von Widgets

Die Methode `addWidget()` der Klasse `WidgetContainer`, von der die Klasse Sidebar abgeleitet ist, kümmert sich um das Hinzufügen von Widgets. Ihr erster Parameter ist ein Objekt der Widget-Klasse, der optionale zweite Parameter gibt dem Widget einen Namen. Ist dieser nicht gesetzt, so wird der Klassenname des Widgets ohne das Wort Widget als Name benutzt.

```php
<?php

$widget = new SearchWidget();
Sidebar::Get()->addWidget($widget, 'search1');
```


#### Hinzufügen eines Widgets an einer bestimmten Position

`insertWidget()` (ebenfalls aus der Klasse `WidgetContainer`) erlaubt es, ein Widget hinzuzufügen und dabei auch anhand des Namens eines anderen Widgets festzulegen, an welcher Position das Widget hinzugefügt werden soll. Der erste Parameter ist ein Objekt der Widget-Klasse, der zweite Parameter gibt an, vor welchem anderen Widget (identifiziert durch dessen Name) das neue Widget hinzugefügt werden soll. Der letzte Parameter ist wieder optional und legt den Namen des neuen Widgets fest.

```php
<?php

$widget1 = new SearchWidget();
$widget2 = new SearchWidget();
Sidebar::Get()->addWidget($widget1, 'search1');
Sidebar::Get()->insertWidget($widget2, 'search1', 'search2'); //widget2 (mit Namen search2) wird vor widget1 (mit Namen search1) platziert.
```



### JavaScript-Funktionen

Das Mitscrollen der Sidebar kann über JS gesteuert werden. Dazu gibt es folgende Funktion:

* `STUDIP.Sidebar.setSticky(bool is_sticky = true)`
