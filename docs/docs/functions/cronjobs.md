---
id: cronjobs
title: Cronjobs
sidebar_label: Cronjobs
---

Um Cronjobs nutzen zu können, müssen sie entsprechend installiert werden. Die Installationsanleitung gibt dazu [ausführlich Auskunft](http://docs.studip.de/admin/Admins/Installationsanleitung#toc23).

Zu beachten sind bei der Cronjob-Erstellung mindestens zwei Probleme:

* Das Cronjob-Skript auf CLI-Ebene wird ggf. unter einer anderen Nutzerkennung ausgeführt als die Skripte über die Weboberfläche. Dies kann zu Seiteneffekten bei bestimmten Operationen wie zum Beispiel bei Dateien führen.
* Nicht jedes Cache-Modul ist auch auf CLI-Ebene verfügbar. Dies führt dazu, dass Klassen sich unterschiedlich verhalten können, wenn sie über die Weboberfläche oder über einen Cronjob ausgeführt werden. Sollten sich Inhalte scheinbar nicht ändern, prüfen Sie den Zustand in der Datenbank und falls dort Unterschiede zur Weboberfläche festzustellen sind, ist bestimmt ein Cache im Spiel. Prüfen Sie in diesem Zusammenhang auch die Einstellung `CACHING_ENABLE` in der Datei `cli/studip_cli_env.inc.php`. Eventuell kann der Cache auf CLI-Ebene aktiviert werden, aber das hängt von dem verwendeten Tool ab.

Seit Stud.IP Version 3.3 hat sich das im letzten Punkt angesprochene Verhalten verbessert, da das Caching über einen speziellen Cache-Proxy konsistenter im CLI-Modus und im Webmodus arbeitet. Es können aus dem CLI-Modus Werte gelöscht werden. Das Auslesen aus dem Cache ist allerdings immer noch nicht unbedingt gewährleistet.

### Anatomie

#### Vollständiges Beispiel

```php
<?php
class ExampleCronjob extends CronJob
{
    public static function getName()
    {
        return _('Beispiel');
    }

    public static function getDescription()
    {
        return _('Beispiel Cronjob, der nichts macht');
    }

    public static function getParameters()
    {
        return [
            'verbose' => [
                'type'        => 'boolean',
                'default'     => false,
                'status'      => 'optional',
                'description' => _('Sollen Ausgaben erzeugt werden'),
            ],
        ];
    }

    public function setUp()
    {
    }

    public function execute($last_result, $parameters = [])
    {
        do_something();
    }

    public function tearDown()
    {
    }
}
```


#### Grundlagen

#### `setup() / teardown()`


#### `execute()`


#### Parameter

Gültige Parameter-Typen sind die folgenden:

| Parameter | Beschreibung |
| ---- | ---- |
| `boolean` | |
| `string` | (1-zeiliger Text) |
| `text` | (mehrzeiliger Text) |
| `integer` | `select` (die gültigen Werte werden in dem separaten Feld `values` definiert|


Über das Feld `default` kann ein Standardwert angegeben werden, während über das Feld `status` angegeben wird, ob der Parameter zwingend erforderlich (`mandatory`) oder optional (`optional`) ist. In dem Feld `description` kann eine Beschreibung für den Parameter mitgegeben werden, der bei der Konfiguration des Cronjobs angezeigt wird.

### Registrieren/Installation eines Cronjobs

```php
<?php
require_once '../classes/example_cronjob.php'; // Contains ExampleCronjob

class CronjobMigration extends Migration
{
    public function up()
    {
        ExampleCronjob::register()->schedulePeriodic(59, 23)->activate();
    }

    public function down()
    {
        ExampleCronjob::unregister();
    }
}
```
