---
id: icon
title: Icon
sidebar_label: Icon
---

## Benutzung der Klasse Icon

Stud.IP stellt eine reichhaltige Auswahl an Icons zur Verfügung. Ab Stud.IP v3.4 werden diese Icons mit einer PHP-API angesprochen. In den früheren Versionen musste man dazu eine Pfad im Dateisystem kennen. Jetzt reicht es, die Form des Icons zu kennen, um dieses einzubinden.

Zu diesem Zweck wurde die Klasse `Icon` geschaffen. Instanzen dieser Klasse haben 3 Eigenschaften:

* die Form des Icons
* die Rolle (und damit implizit die Farbe) des Icons
* semantische Attribute wie `title`

### Quick Start

Wir wollen das "Startseite"-Icon (das man links oben auf jeder Stud.IP-Seite findet) einbinden. Dazu schreibt man einfach:

```php
<?= Icon::create('home', Icon::ROLE_NAVIGATION, ['title' => _("Zur Startseite")]) ?>`
```

Und damit erhält man ein `img`-Element wie dieses:

https://develop.studip.de/studip/assets/images/icons/lightblue/home.svg

Die Form des Icons ist "home". Die Rolle/Funktion des Icons ist "navigation". Das Icon soll ein semantisches Attribut, einen Titel, bekommen: _("Zur Startseite").

Die Signatur von `Icon::create` sieht daher so aus:

```php
public static function create($shape, $role = Icon::DEFAULT_ROLE, $attributes = array())
```

Wenn man früher Icons verwenden wollte (`Assets::img("icons/16/lightblue/home.png")`), war dort die Farbe hartkodiert. 
Wollte man in seiner Installation ein Redesign vornehmen, ist das mehr als unschön. 
Aus diesem Grund wurden die Farben aus dem PHP-Code verbannt und auf Rollen umgestellt.

Derzeit befindet sich die Abbildung der Rollen auf Farben in der Variablen `Icon::$roles_to_colors`.

Will man eigene Icons verwenden, schreibt man einfach:

```php
Icon::create($eigeneURL)
```

### Icon-API im Detail

Die Klasse `Icon` bietet nur wenige Methoden an.

#### Factory-Methoden

```php
Icon::create($shape, $role = Icon::DEFAULT_ROLE, $attributes = [])
```

Das ist *die* Methode, um ein Icon zu instanziieren. Die `$shape` gibt die Form des Icons an, ohne weiter auf eine Färbung etc. einzugehen.

Die Rolle `$role` definiert den Kontext, in dem das Icon verwendet werden soll. Der Stud.IP-Style-Guide gibt zB vor, dass alle Icons in Links einheitlich gefärbt sein sollen (in der Regel blau). Die Rolle hilft dabei, Farbwerte nicht hart zu kodieren und trotzdem im ganzen System einheitlich zu differenzieren.

Die Eigenschaften `$attributes` enthalten **nur** semantische Attribute wie `title`. Nicht-semantische Werte wie CSS-Klassen, Größen, oder `data-`Attribute dürfen hier nicht eingetragen werden.

#### Ausgabe-Methoden

```php
$icon->asImg($size = null, $view_attributes = [])
```

Diese Methode gibt das Icon als `img`-Element aus:

```php
Icon::create('vote', Icon::ROLE_CLICKABLE)->asImg(16)
```

erzeugt:

```html
<img width="16" height="16" src="images/icons/blue/vote.svg" alt="vote" class="icon-role-clickable icon-shape-vote">
```

Der erste Parameter `$size` legt die `width/height` des `img`-Elements fest. Die `$view_attributes` können mit beliebigen Attributen wie `class` usw. befüllt werden.

```php
$icon->asInput($size = null, $view_attributes = [])
```

Eine Variation von `Icon::asImg`, die das Icon als `input`-Element ausgibt:

```php
Icon::create('upload', Icon::ROLE_CLICKABLE)->asInput(20, ['class' => 'text-bottom'])
```

ergibt:

```html
<input type="image" class="text-bottom icon-role-clickable icon-shape-upload" width="20" height="20" src="images/icons/blue/upload.svg" alt="upload">
```

Die Parameter funktionieren wie bei `Icon::asImg`.
            
```php
$icon->asCSS($size = null)
```

Diese (selten verwendete) Methode gibt das Icon als CSS-Style-Angabe via @background-image@@ aus:

```php
Icon::create('vote+add')->asCSS(17)
```

generiert:

```css
background-image:url(images/icons/17/blue/add/vote.png);background-image:none,url(images/icons/blue/add/vote.svg);background-size:17px 17px;
```

Der Parameter `$size` legt die `background-size` fest.

```php
$icon->asImagePath()
```

Mit dieser Methode erhält man einfach den Pfad zum SVG, dass für das gewünschte Icon steht:

```php
Icon::create('vote+add')->asImagePath() === 'images/icons/blue/add/vote.svg'
```

```php
$icon->__toString()
```

Die magische `__toString`-Methode ist nur ein Alias mit Default-Werten für `Icon::asImg`, sodass folgendes:

```php
echo Icon::create('vote+add')
```

einfach nur:

```html
<img width="16" height="16" src="images/icons/blue/vote.svg" alt="vote" class="icon-role-clickable icon-shape-vote">
```

ausgibt.

#### Getter

* `$icon->getShape()`
* `$icon->getRole()`
* `$icon->getAttributes()`

Diese Methoden geben einfach die entsprechenden Werte zurück.

#### "Setter"

* `$anotherIcon = $icon->copyWithShape($shape)`
* `$anotherIcon = $icon->copyWithRole($role)`
* `$anotherIcon = $icon->copyWithAttributes(array $attributes)`

Diese Methoden ändern nicht das `$icon`, sondern geben ein neues Icon mit verändertem `Shape/Role/Attributes` zurück. Instanzen von `Icon` sind `immutable`, so dass unerwünschte Seiteneffekt nicht auftauchen können.

## Was fehlt noch?

* `$size === false`
* Additions
* CSS-Mixins
* Rollen-zu-Farben-Abbildung
