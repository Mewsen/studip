---
id: cheat-sheet
title: Cheat-Sheet
sidebar_label: Cheat-Sheet
---

## User

### Aktuellen Benutzer finden
```php
User::findCurrent();
```

### Wert aus der Benutzer-Konfiguration auslesen

```php
$wert = UserConfig::get(User::findCurrent()->id)->getValue('WERT_NAME');
```

### Wert in der Benutzer-Konfiguration speichern

```php
UserConfig::get(User::findCurrent()->id)->store('WERT_NAME', $wert);
```

WICHTIG: `$wert` wird standardmäßig als String gespeichert, sofern nichts anderes in der Config-Tabelle angegeben ist! 
Arrays sollten vorher mit `json_encode($array);` kodiert werden!

### Prüfen, ob ein Benutzer Administrator ist

```php
$GLOBALS['perm']->have_perm('admin')
```

Liefert true zurück, wenn der Benutzer entweder 'root'- oder 'admin'-Berechtigungen hat, ansonsten false.


## SimpleORMap

### Veranstaltungen

Courses (/lib/models/Course.class.php)

### Archivierte Veranstaltungen

ArchivedCourses (/lib/models/ArchivedCourse.class.php)

### Dozenten

User-Klasse, alle Objekte, bei welchen das Attribut "perms" auf "dozent" gesetzt ist.

### Benutzer

User (/lib/models/User.class.php), Erweiterung von AuthUserMd5, welche die Grunddaten (z.B. Vorname, Nachname, Benutzername) eines Benutzers enthält.

## Controller-relevante Klassen

### URLs

#### URL erzeugen

```php 
URLHelper::getLink('dispatch.php/CONTROLLER);
```
wobei CONTROLLER der aufzurufende Controller ist. 

**WICHTIG**: `getLink` ändert URLS so ab, sodass diese in HTML-Code eingebettet werden können. 
Beispielsweise wird aus & ein &amp;. Will man diese Umwandlung nicht, sollte `URLHelper::getURL();` verwendet werden.

#### URL-Erzeugung im Plugin

```php 
PluginEngine::getLink(PLUGIN, PARAMETER, PFAD);
```

`PLUGIN = $this->plugin` (im Controller) oder `$plugin` (im Template), PARAMETER = assoziatives Array, PFAD = Pfad zum Controller)

**WICHTIG**: getLink ändert URLS so ab, sodass diese in HTML-Code eingebettet werden können. Beispielsweise wird aus & ein &amp;. 
Will man diese Umwandlung nicht, sollte `PluginEngine::getURL();` verwendet werden.

Sobald man sich in einer Trails-App befindet, sollte allerdings die Controller-Methode `url_for()` genutzt werden, welche den obigen Aufruf kapselt und vereinfacht.

#### Icon-Erzeugung

```php
Icon::create(SYMBOL, KATEGORIE)->asImg(GRÖßE);
```
* SYMBOL = das anzuzeigende Symbol
* KATEGORIE = farbliche Einordnung des Symbols (z.B. `Icon::ROLE_CLICKABLE`),
* GRÖßE = Angabe in Pixeln (z.B. "12px")

#### URL-Parameter "dauerhaft" machen

Um einen Parameter beim Aufruf der nächsten Seite mitgeben zu können, verwendet man die Methode addLinkParam von URLHelper:

`URLHelper::addLinkParam('name', WERT);`

An die URL wird nun der Parameter name mit dem Wert WERT angehängt (z.B.: `http://example.org?name=WERT`).

## Darstellung

#### Navigationselemente

##### Navigationselement auf der Startseite hinzufügen

```php
<?php
$navigation = new Navigation('LINKBESCHREIBUNG', 'EINE_URL'); //Erzeugen des Navigationselementes mit einem passenden Text und der gewünschten URL
Navigation::addItem('/start/ID', $navigation); //ID = eindeutige Bezeichnung des Navigationselementes. /start/ muss auf jeden Fall dorthin, damit das Element auf der Startseite angezeigt wird
```

##### Reiternavigation (Tabs) erzeugen

```php
<?php
$navigation = new Navigation('LINKBESCHREIBUNG', 'EINE_URL');
Navigation::addItem('/ID', $navigation); //ID = eindeutiger Pfad. Dieser liegt in der "Wurzel", also nicht unterhalb anderer Pfade wie z.B. /start/
```

In dem Controller, welcher über obiges Navigationselement erreichbar ist, muss das Navigationselement aktiviert werden:
```php
<?php
Navigation::activateItem('/ID');
```

**Hinweis:** Natürlich sind auch hier Unterpfade, z.B. /ID/NOCHEINEID möglich.

##### Icon in Reiternavigation einfügen

Beim Hinzufügen von Icons in die Reiternavigation muss beachtet werden, dass das Icon eines aktiven Reiters eine andere Farbe hat als das Icon eines inaktiven Reiters. Deshalb muss der Wechsel des Icons bei der Aktivierung eines Reiters durchgeführt werden.

Definition des Reiters in der Navigationsstruktur:
```php
<?php
$navigation = new Navigation(
    'Text',
    PluginEngine::getUrl('ein/link')
    );
$navigation->setImage(Icon::create('edit', Icon::ROLE_NAVIGATION));
Navigation::addItem('/navigations/pfad', $navigation);
```

#### Hinweistexte

Möglichkeiten für Hinweistexte:

| Typ | Beschreibung |
| ---- | ---- |
| `MessageBox::error` | Fehlermeldungen |
| `MessageBox::info` | Informationen |
| `MessageBox::warning` | Warnmeldungen (aber keine Fehler) |
| `MessageBox::success` | Erfolgsbestätigungen (Aktionen, die erfolgreich abgeschlossen wurden) |


Mehr Informationen: [Messagebox](MessageBox)

##### Hinweistexte vom Controller heraus ausgeben

```php
<?php
PageLayout::postError(_('Fehler!'));
```

Mehr zu PageLayout: [PageLayout](PageLayout)

#### Sidebar

##### Navigations-Bereich hinzufügen

```php
<?php
$navigation = new NavigationWidget();
$navigation->setTitle('Titel des Bereiches');

//hier wird ein Link hinzugefügt:
$navigation->addLink(
    'Ein Linktitel',
    PluginEngine::getURL($this->plugin, [], 'show')
);

Sidebar::Get()->addWidget($navigation); //Navigations-Bereich in die Sidebar einhängen
```


##### Link auf Dialog im ActionsWidget hinzufügen

```php
<?php
$actions = new ActionsWidget();
$actions->addLink(
    'Beschreibung',
    URLHelper::getURL('dispatch.php/CONTROLLER'),
    Icon::create(SYMBOL, KATEGORIE)
)->asDialog();
```

CONTROLLER ist der aufzurufende Controller, SYMBOL das ausgewählte Symbol, dessen Farbe durch die Kategorie KATEGORIE gesetzt wird. Mit der Methode asDialog() (Klasse LinkElement in /lib/classes/sidebar) wird das HTML-Attribut "data-dialog" beim Erstellen des HTML-Codes des Links gesetzt.

##### Schnellsuche (Suche mit Drop-Down-Menü) zu einem Suchfeld der Sidebar hinzufügen

```php
<?php
$searchWidget = new SearchWidget(PluginEngine::getLink($this->plugin, [], 'search'));
$searchWidget->setTitle(_('Suche'));
$searchWidget->setMethod('post');
        
$sqlSearch = new SQLSearch("SELECT auth_user_md5.user_id as userId FROM auth_user_md5 " .
    "WHERE ((vorname like CONCAT('%', :input, '%') " .
    "OR (nachname like CONCAT('%', :input, '%')) ",
    _('Benutzername')
    );

//QuickSearch zum SearchWidget hinzufügen:
$searchWidget->addNeedle(
    _('Benutzername'),
    'userId',
    _('Benutzername'),
    $sqlSearch
    );
```

Mehr zu QuickSearch: [QuickSearch](QuickSearch)


#### Templates

##### Button erzeugen

```php
<?= \Studip\Button::create(_('Speichern')); ?>
```

##### Button für Dialog erzeugen

```php
<div data-dialog-button>
    <?= \Studip\Button::create(_('Speichern')); ?>
</div>
```


### Plugins

#### Herausfinden, ob ein anderes Plugin aktiviert ist

Dies ist z.B. sinnvoll, wenn ein Plugin von einem anderen Plugin oder dessen Klassen abhängig ist. Folgender Code prüft, ob ein anderes Plugin aktiviert ist:
```php
$pluginManager = PluginManager::getInstance();
$pluginManager->getPluginInfo('AnderesPlugin');
//$pluginManager enthält nun Daten über das gesuchte andere Plugin.
if ($pluginManager['enabled']) {
    //das andere Plugin ist angeschaltet: Nun können z.B. Klassen dieses Plugins eingebunden werden oder andere Dinge gemacht werden, die dieses Plugin voraussetzen
}
```

#### Alle Plugins für eine Seite deaktivieren

Um festzustellen, ob Probleme auf einer Seite von einem Plugin ausgelöst werden, können auf dieser Seite alle Plugins deaktiviert werden, indem der URL-Parameter `disable_plugins=1` angehängt wird.


### JavaScript

#### foreach in JavaScript

```javascript
for (var element of someArray) {
    doSomething(element);
}
```

Siehe: [https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Statements/for...of](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Statements/for...of)

#### JavaScript-Datei eines Plugins einbinden

In der Plugin-Klasse wird im Konstruktor folgender Code eingefügt, falls das JavaScript auf allen Seiten verfügbar sein soll. Ansonsten muss der folgende Code in der `perform()`-Methode des Plugins eingefügt werden, damit das JavaScript nur auf den Pluginseiten zur Verfügung steht:
```php
<?php

PageLayout::addScript($this->getPluginURL() . '/assets/javascript/JavaScriptDatei.js');
```

Mehr zu PageLayout: [PageLayout](PageLayout)

#### URL in JavaScript erzeugen

In JavaScript ist ebenfalls ein URLHelper implementiert, welcher sich ähnlich aufrufen lässt, wie der URLHelper in PHP:

`STUDIP.URLHelper.getURL(URL, {"parameter" : WERT});`

Das Objekt, welches in JSON-Notation hinter der URL angegeben wird, beinhaltet Parameter, welche an die URL angehängt werden.

### Composer

#### Wie installiere ich die durch Composer definierten Abhängigkeiten?

`composer install` bzw. `make composer`

#### Wie installiere ich eine neue Abhängigkeit mittels Composer?

`composer require <lib>`

#### Wie aktualisiere ich eine durch Composer definierte Abhängigkeit?

`composer update <lib>`

Es dürfen immer nur einzelne Abhängigkeiten im Rahmen eines TICs (oder Bugfixes, wenn nötig) aktualisiert werden, da durch das Update durch API-Änderungen oder andere kritische Änderungen Probleme entstehen können. Es sollte niemals grundlos `composer update` ohne Angabe einer Lib aufgerufen werden.

#### Wo finde ich weitere Informationen, wie man Composer verwendet?

[https://getcomposer.org/doc/01-basic-usage.md](https://getcomposer.org/doc/01-basic-usage.md)
