---
title: HTML-Ausgaben
---

## Flexi-Templates
Stud.IP verwendet zur Ausgabe von HTML Templates, genauer gesagt eine Eigenentwicklung namens [Flexi-Templates](./flexi-templates)

Der einzige Unterschied ist, dass es in Stud.IP schon eine TemplateFactory instanziiert ist, die man einfach verwenden kann.

```php
$template = $GLOBALS['template_factory']->open('shared/searchbox.php');
echo $template->render();
```

## Templates und Klassen für Alle(s)

#### Meldungen

Um in Stud.IP Meldungen anzuzeigen, verwendet man die Klasse [MessageBox](./message-box).

Hier ein einfaches Beispiel:
```php
// Beispiel für eine einfache Info-Nachricht
echo MessageBox::info('Nachricht');
```

Möchte man die Meldung nicht sofort, sondern erst zusammen mit dem Seiten-Layout ausgeben, sollte man die Ausgabe an die [PageLayout](./page-layout)-Klasse delegieren:
```php
// Beispiel für eine einfache Info-Nachricht
$info = MessageBox::info('Nachricht');
PageLayout::postMessage($info);
```

Alle Details und weitere Typen von MessageBoxen findet man in der [Dokumentation](./message-box).

#### Suchbox

Das Template `searchbox` bietet eine einheitliche Suchmaske für alle Seiten, auf denen gesucht werden soll. Das Template ist recht minimalistisch in kann in eine HTML-Form gebettet werden.

Verwendung im Template
```php
<form action="<?=URLHelper::getLink()?>" method=post>
    <?= $this->render_partial('shared/searchbox'); ?>
</form>
<?
$searchterm = Request::get('searchterm');
```

#### Paginierung

Das Template `pagechooser` ist für Seiten, die eine Paginierung haben sollen. Man gibt dem Template die Anzahl an Elementen, Elemente pro Seite, die aktuelle ausgewählte und einen Link mit Formatauszeichnung wo die Seitenzahl sein soll mit, dann erhält man oben gezeigten Seitenwähler.

Seit Stud.IP 2.1 befindet sich ein globaler Wert in der Datenbank. Dieser ist mit get_config('ENTRIES_PER_PAGE') nutzbar. Der Standard-Wert ist 20.

```php
<?= $this->render_partial("shared/pagechooser",
  array(
    "perPage" => get_config('ENTRIES_PER_PAGE'),
    "num_postings" => $numberOfPersons,
    "page"=>$page,
    "pagelink" => "score.php?page=%s"));
?>
```

#### Modaler Dialog

Manchmal ist es notwendig bei sehr wichtigen Rückfragen einen [modalen Dialog](./modaler-dialog) statt einer normalen MessageBox zu verwenden.

Beispiel:
```php
$question = sprintf(_('Möchten Sie wirklich den User **%s** löschen ?'), $username);
echo createQuestion( $question,
  array(
    "studipticket" => get_ticket(),
    'u_kill_id' => $u_id
  ),
  array(
    'details' => $username
  )
);
```

Ab Version 4.2 kann stattdessen PageLayout::postQuestion verwendet werden.

```php
PageLayout::postQuestion($question, $accept_url = *, $decline_url = *)
```
