---
title: Aktionsmenü
sidebar_label: Aktionsmenüs
---
# ActionMenu

Am `ActionMenu` können Aktionsicons in ein ausklappbares Menü zusammengefasst werden.

## PHP

Die Klasse kann direkt innerhalb einer View verwendet werden, weil sie den nötigen HTML-Code generiert und ausgibt.

Über `ActionMenu::get()` erhält man eine Instanz der Klasse, die mit Inhalten befüllt werden kann.

Über die Methode `addLink` werden neue Aktionslinks hinzugefügt, die wie üblich mit den Parametern URL, Label, Icon und weiteren Optionen spezifiziert werden.

Weiter gibt es die Methode `addMultiPersonSearch`, die als Parameter ein `MultiPersonSearch-Objekt` erhält und diese Suche als Aktion hinzufügt.

Ein minimaler Aufruf mit einer einzigen Aktion könnte also so aussehen:

```php
<?php
$menu = ActionMenu::get();
$menu->addLink(
    $controller->url_for('controller/action'),
    _('Hinzufügen'),
    Icon::create('add'),
    ['data-dialog' => 'size=auto']
);
$menu->addLink(
    $controller->url_for('controller/second_action'),
    _('Bearbeiten'),
    Icon::create('edit'),
    ['data-dialog' => 'size=auto']
);
$menu->addButton(
    'delete',
    ('Löschen'),
    Icon::create('trash'),
    [
        'data-confirm' => _('Wollen Sie wirklich löschen?'),
        'formaction'   => $controller->url_for('controller/delete')
    ]
);
$menu->addMultiPersonSearch(
    MultiPersonSearch::get('add_users')
        ->setTitle(_('Personen hinzufügen'))
        ->setLinkText(_('Personen hinzufügen'))
        ->setSearchObject($array)
        ->setDefaultSelectedUser($array_selected_user)
        ->setDataDialogStatus(Request::isXhr())
        ->setJSFunctionOnSubmit(Request::isXhr() ? 'STUDIP.Dialog.close();' : false)
        ->setExecuteURL($controller->url_for('controller/add_member/'))
        ->addQuickfilter(_('Titel'), $array)
);
echo $menu->render();
?>
```

## Vue

Die Vue-Komponente wird über das Tag `StudipActionMenu` eingebunden und hat die folgenden Properties:

```json
{
  "collapseAt": "Schwellwert, ab dem das Menü als tatsächliches Menü angezeigt wird [Number] (optional)",
  "context": "Optional Kontext, der über den Einträgen angezeigt wird [String]",
  "items": [],
  "title": "Titel des Aktionsmenüs [String] (optional, Default: 'Aktionsmenü')"
}
```

Der Wert für `collapseAt` ist optional und wenn er nicht angegeben wird, wird auf den Stud.IP-Default zurückgegriffen.

Das `items`-Array besteht dabei aus Einträgen des folgendes Formats:

```json
{
  "label": "Text des Eintrags [String]",
  "url": "URL, die aufgerufen werden soll, wenn der Eintrag ausgewählt wird [String] (optional, default: '#')",
  "emit": "Event, der gefeuert werden soll, wenn der Eintrag ausgewählt wird [String] (optional)",
  "emitArgument": "Argumente, die dem Event mitgegeben werden sollen, wenn dieser gefeuert wird [Array] (optional)",
  "icon": "Icon, das für den Eintrag angezeigt werden soll [Objekt: {shape: String}] oder false, wenn kein Icon angegeben werden soll (optional, default: false)",
  "type": "Möglicher Typ des Eintrags: 'link', 'button', 'separator' [String] (optional, default: 'link')", 
  "name": "Name des Buttons; Eintrag wird hierdurch automatisch zu einem Button, wenn keine 'url' gesetzt ist [String] (optional)",
  "classes": "CSS-Klassen, die bei dem Eintrag gesetzt sein sollen [String] (optional)",
  "attributes": "Weitere HTML-Attribute, die bei dem Eintrag gesetzt sein sollen [Objekt] (optional)",
  "disabled": "Gibt an, ob der Eintrag deaktiviert sein soll [Boolean] (optional)" 
}
```
