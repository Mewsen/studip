---
id: buttons
title: Buttons
sidebar_label: Buttons
---

Buttons werden in Stud.IP über eine eigene Klasse erzeugt, die im [Studip-Namespace](Studip-Namespace) vorliegt. Abgeleitet sind sie von der Klasse Interactable, deren Beschreibung unter folgender URL verfügbar ist:
https://hilfe.studip.de/api/class_studip_1_1_interactable.html

Vor allem bei der Entwicklung von Views sind Buttons sehr nützlich.

### Arten von Buttons

#### Button

Dies bezeichnet einen einfachen Button, welcher als `<button>`-Element in HTML dargestellt wird. Im einfachsten Fall wird nur eine Beschriftung, ein Name und ein Array von Attributen benötigt, um einen Button zu erzeugen. Hier wird beispielsweise in einer Ansicht (View) ein einfacher Button erzeugt:

```php
<?= \Studip\Button::create('Klick mich!', 'klickMichButton', ['data-dialog-button' => '1', 'data-hallo' => 'welt']); ?>
```

#### LinkButton

Ein LinkButton wird im Gegensatz zum einfachen Button als `<a>`-Element (Verweis) in HTML dargestellt. Bei der Erzeugung wird aber die gleiche statische Methode verwendet. Dort, wo im Standard-Button der Name eingetragen wird, wird beim LinkButton die aufzurufende URL eingetragen.

```php
<?= \Studip\LinkButton::create('Klick mich!',  'http://example.org', ['data-dialog' => '1', 'data-hallo' => 'welt']); ?>
```

Der zweite Parameter gibt statt eines Namens eine URL an, die beim Klick besucht werden soll. Natürlich kann hier auch die URL für einen Stud.IP-Controller oder den Controller eines Plugins angegeben werden.

#### ResetButton

Ein ResetButton ist besonders in HTML-Formularen nützlich, da er in HTML als `<input>`-Element vom Typ "reset" gezeichnet wird, sodass er beim Klick ein Formular zurücksetzt.

```php
<?= \Studip\ResetButton::create('Klick mich!', 'klickMichButton', ['data-dialog-button' => '1', 'data-hallo' => 'welt']); ?>
```
