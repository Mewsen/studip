---
id: skiplinks
title: Skiplinks
sidebar_label: Skiplinks
---

Skiplinks ermöglichen für Tastaturbenutzer das schnelle Navigieren innerhalb einer Seite. In Stud.IP sind die Skiplinks zunächst verborgen. Erst durch das Drücken der Tab-Taste werden die Skiplinks eingeblendet. Dabei bekommt der erste Link der Liste den Focus, so dass durch weiteres Drücken der Tab-Taste durch die Liste navigiert werden kann. Durch Drücken der Eingabetaste wird das Ziel des Links angesprungen.

Diese Klasse ermöglicht das Platzieren von Skiplinks an einer zentralen Stelle im Seitenlayout in Stud.IP. Diese Stelle besteht aus einem DIV-Container, der ziemlich direkt nach dem schließenden header-Tag ausgegeben wird. Grundsätzlich gibt jede Seite in Stud.IP, die ein Layout besitzt oder die `lib/include/html_head.inc.php` einbindet, diesen Container aus.

Dieser Container enthält die eigentliche Liste mit Skiplinks als Aufzählungsliste. Jedes Listenelement besteht aus einem Skiplink.

Die Klasse SkipLinks ermöglicht ein hinzufügen von Skiplinks zu dieser Liste zu jedem Zeitpunkt des Renderprozesses. Sie stellt auch Methoden zur Ausgabe der Liste bereit, die aber normalerweise nicht explizit aufgerufen werden müssen. Die Ausgabe erfolgt, wie bereits gesagt, an zentraler Stelle.

## Methoden der Klasse

Die Klasse SkipLinks kann nicht instanziiert werden, sie bietet ausschließlich Klassenmethoden, d.h. der Aufruf einer Methode erfolgt über `SkipLinks::`*Name*.

* **addIndex($name, $id, $position = NULL, $overwritable = false)**

  Registriert einen Link zu einem Ziel auf der ausgegbenen Seite (Anker).
  
  `$name` ist der Name des Links, der in der Liste ausgegeben werden soll.
  
  `$id` ist die id des HTML-Elements, das durch den Skiplink angesprungen werden soll. Dieses Element muss ein Attribut *id* mit dem hier übergebenen Wert besitzen. Die IDs müssen auf einer Seite natürlich eindeutig sein.
  
  `$position` ist die Position des Skiplinks in der Liste. Dieser Parameter ermöglicht also ein Einsortieren der Links an eine bestimmte Position. Wird keine Position angegeben, wird der Link nach der ersten freien Position größer 100 angelegt.
  
  Mit `$overwritable` kann festgelegt werden, ob ein Link an einer bestimmten Position von einem später im Renderprozess der Seite hinzugefügten Skiplink überschrieben werden kann.

* **addLink($name, $url, $position = NULL, $overwritable = false)**

  Registriert einen Link auf eine andere (auch externe) Seite.
  
  Statt einer id für einen Anker wird durch den Parameter `$url` eine beliebige URL and die Methode übergeben, auf die der Link verweisen soll. Die anderen Parameter haben die gleiche Funktion wie bei addIndex().

* **insertContainer()**

  Fügt dem Pagelayout einen Container hinzu, der die Liste mit den Skiplinks aufnimmt.
  
  Diese Funktion wird normalerweise beim Rendern einer Stud.IP-Seite automatisch an der richtigen Stelle Aufgerufen, so dass ein manuelles Aufrufen nicht erforderlich ist.

* **getHTML()**

  Gibt die registrierten Skiplinks formatiert anhand des Templates `templates/skiplinks` zurück. Das Template rendert die Skiplinks als HTML-Liste.
  
  Auch diese Funktion wird beim Rendern einer Stud.IP-Seite automatisch aufgerufen, ein manuelles Aufrufen ist also auch hier nicht erforderlich.

## Vorbelegte Positionen

Eine Seite in Stud.IP verfügt in der Regel immer über einen Satz gleicher Inhaltsbereiche. Für diese werden automatisch Skiplinks mit einer festen Position registriert. Im einzelnen sind das:

* Hauptnavigation im Header (Position 1, nicht überschreibbar)
* Erste Reiternavigation (Position 10, nicht überschreibbar)
* Zweite Reiternavigation (Position 20, nicht überschreibbar)
* Hauptinhalt (Position 100, überschreibbar)
* Infokasten (Position 10000, nicht überschreibbar)


Durch diese vorbelegten Positionen ist es möglich, auch zwischen z.B. zweiter Reiternavigation und Hauptinhalt einen Skiplink zu setzen. Es sollte allerdings darauf geachtet werden, dass die Skiplinks zu den Haupnavigationsbereichen immer auf allen Seiten einheitlich am Anfang der Liste stehen, um den Benutzer nicht zu verwirren.

## Beispiele

Einfügen des Skiplinks für die Haupnavigation:

```php
[...]

<div id="barTopFont">
<?= htmlentities($GLOBALS['UNI_NAME_CLEAN']) ?>
</div>
<? SkipLinks::addIndex(_("Hauptnavigation"), 'barTopMenu', 1); ?>
<ul id="barTopMenu" role="navigation">
<? $accesskey = 0 ?>
<? foreach (Navigation::getItem('/') as $nav) : ?>

[...]
```

Verwendung von `addLink()` auf der `index_nobody.php`. Die Links der Startseite werden in die Skiplink-Liste übertragen, so dass der Benutzer z.B. direkt die Anmeldeseite aufrufen kann:

```php
[...]

<? foreach (Navigation::getItem('/login') as $key => $nav) : ?>
    <? if ($nav->isVisible()) : ?>        <? list($name, $title) = explode(' - ', $nav->getTitle()) ?>
        <div style="margin-left:70px; margin-top:10px; padding: 2px;">
            <? if (is_internal_url($url = $nav->getURL())) : ?>
                <a class="index" href="<?= URLHelper::getLink($url) ?>">
            <? else : ?>
                <a class="index" href="<?= htmlspecialchars($url) ?>" target="_blank">
            <? endif ?>
            <? SkipLinks::addLink($name, $url) ?>
            <font size="4"><b><?= htmlReady($name) ?></b></font>
            <font color="#555555" size="1"><br><?= htmlReady($title ? $title : $nav->getDescription()) ?></font>
            </a>
        </div>
    <? endif ?>
<? endforeach ?>

[...]
```

## Ein wenig Magie

Die Liste mit Skiplinks wird zunächst an das Ende der Seite angefügt. Per Javascript wird die Liste in den Container am Anfang der Seite verschoben. Um auch Benutzern älterer Screenreader die Navigation und das Erkennen von Inhaltsbereichen zu erleichtern wird per Javascript jedes Ziel eines Skiplinks mit dem Linktext als *h2*-Überschrift versehen. Das verlinkte HTML-Element wird mit dem Attribut `aria-labelledby` versehen, dass auf die Überschrift verweist, so dass der Inhaltsbereich entsprechend benannt ist.
