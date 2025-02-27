---
id: log
title: Log
sidebar_label: Log
---

Häufig muss man während der Programmierung, u.U. aber auch nachträglich auf dem Produktiv/Staging System debugging durchführen. Dazu werden dann meist Ausgaben erzeugt. Das ist unübersichtlich und auf dem Produktivsystem auch für alle sichtbar, wenn man nicht aufpasst. Es gibt auch Bereiche, wo man nur sehr schwierig mit debugging Ausgaben arbeiten kann, z.B. Loginvorgang, SSO Varianten, Webservices etc. Für solche Fälle steht eine Log-Klasse zur Verfügung stehen, mit der man flexibel eines oder mehrere Logs erzeugen kann, und die Ausgabe des Logs nach belieben steuern kann.

Die Klasse [Log](https://gitlab.studip.de/studip/studip/-/blob/main/lib/classes/Log.php) steht ab Version 2.4 zur Verfügung. Es wird automatisch ein standard log initialisiert, welches in die Datei studip.log im temporären Stud.IP Verzeichnis geschrieben wird. Im Modus `development` wird der Log Level auf DEBUG gesetzt, ansonsten auf ERROR.

# Allgemeines

Über die Klasse `Log` können verschiedene benannte Logger gesteuert werden. Der standard Logger hat keinen speziellen Namen. Die logger können im einfachsten Fall einfach der Name einer Datei sein, in die die Meldungen geschrieben werden. Alternativ kann ein callback übergeben werden, der die Meldungen entgegen nimmt. Die Klasse definiert folgende Logging Level:

* `FATAL`
* `ALERT`
* `CRITICAL`
* `ERROR`
* `WARNING`
* `NOTICE`
* `INFO`
* `DEBUG`

Es stehen gleichnamige Methode zur Verfügung, um eine Meldung auf dem entsprechenden Level abzusetzen.

Statischer Aufruf:

```php
Log::warning('Dies könnte problematisch sein');
```

Aufruf über Instanz Methode:

```php
Log::get()->warning('Dies könnte problematisch sein');
```

Groß- und Kleinschreibung spielen keine Rolle, es reicht auch eine eindeutige Abkürzung, z.B. warn() statt warning().

## Andere Logger erzeugen

### Dateibasiert

```php
//neuen benannten logger erzeugen
Log::set('meinlogger', '/tmp/meinlog.log');

//Aufruf
Log::info_meinlogger('Dies nur zu meiner Information');
oder
Log::get('meinlogger')->info('Dies nur zu meiner Information');
```

### Mit callback

```php
//logging per mail 
$logtomail = function($l)
{
    return mail('noack@data-quest.de', 'Log ' . ' ['.$l['level_name'].'] ', $l['formatted']);
};
//benannten logger ändern
Log::get('meinlogger')->setHandler($logtomail);
```

### kombinierte Logger mit verschiedenen Stufen

```php
$defaultlog = Log::get();
$defaultlog->setLogLevel(Log::DEBUG);
$maillog = Log::get('meinlogger');
$maillog->setLogLevel(Log::ALERT);
$combi_handler = function($m) use ($defaultlog, $maillog)
{
    $maillog->log($m['message'], $m['level']);
    $defaultlog->log($m['message'], $m['level']);
};
//als neuen standard logger setzen
Log::set(*, $combi_handler );

//das geht nur in die Datei
Log::debug('eigentlich egal was hier passiert');
//in Datei und per mail
Log::fatal('jetzt krachts aber richtig');
```
