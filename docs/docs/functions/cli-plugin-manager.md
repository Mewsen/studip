---
id: cli-plugin-manager
title: Pluginmanager auf der Kommandozeile
sidebar_label: Pluginmanager CLI
---

# Pluginmanager auf der Kommandozeile

| 📌 **Achtung** |
|--|
| Das Kommandozeilen-Tool `cli/plugin_manager` findet bis **Stud.IP v5.0** Verwendung und wird dann durch das allgemeinere Tool [`cli/studip`](CLI) abgelöst. |

Wenn man mal nicht die Möglichkeit die Plugins über die Oberfläche zu verwalten oder einfach mal ein bereits an der passenden Stelle abgelegtes Plugin Stud.IP unterzuschieben, dann kommt einem dieses Tool gerade recht. Es liegt im Ordner `cli` und kann von jeder Stelle aufgerufen unter dem Namen `plugin_manager` aufgerufen werden.

Es bietet folgende Befehle:

## Installieren einer Plugin-Datei

```shell
./cli/plugin_manager install pfad/zur/plugin.zip
```

Installiert das Plugin in der angegebenen ZIP-Datei. Diese Aktion ist analog zu dem installieren eines Plugins über die Oberfläche.

## Registrieren eines Plugins

```shell
./cli/plugin_manager register public/plugins_packages/origin/plugin
```

Wenn das Plugin bereits an der korrekten Stellen im Verzeichnisbaum von Stud.IP liegt, so kann man es hiermit im System registrieren. Es werden dabei etwaige Installations-SQL Dateien ausgeführt und fehlende Migrationen nachgezogen

## Entfernen der Registrierung eines Plugins

```shell
./cli/plugin_manager unregister PluginName
```

Entfernt das Plugin mit dem angegeben Namen aus dem System und führt alle "down"-Migrationen des Plugins aus.

## Pluginmigrationen ausführen

```shell
./cli/plugin_manager migrate PluginName  [-l] [-v] [-t *zahl]

-l Listet nur auf was getan werden soll, migriert aber nicht.
-v Schaltet den "verbose"-Modus ein. Es werden zusätzliche Informationen über die durchgeführten Migrationen angezeigt.
-t zahl - Erlaubt einem die Zielmigration. Eine 0 führt zum zurücknehmen aller Migrationen, ansonsten wird zu der entsprechenden Migration (hoch oder runter) hinmigriert. Gibt man diesen Parameter nicht an, so wird automatisch bis zur aktuellsten Migration hochmigriert.
```

Erlaubt es, Plugin-Migrationen auszuführen. Verhält sich dabei analog zum [Stud.IP-CLI-Migrator](Migrations#toc7).

## Plugin einschalten

```shell
./cli/plugin_manager activate PluginName
```

Schaltet das angegebene Plugin ein

## Plugin ausschalten

```shell
./cli/plugin_manager deactivate PluginName
```

Schaltet das angegebene Plugin aus

## Plugininfo anzeigen

```shell
./cli/plugin_manager info PluginName
```

Zeigt Informationen zu einem einzelnen oder allen installierten Plugins an. Die Informationen werden aus der Datenbank gelesen, wenn das Plugin im Dateisystem nicht gefunden wurde, ist \[class_exists\] => 0 gesetzt.

```shell
[id] => 142
[name] => Forum
[class] => CoreForum
[path] => core/Forum
[type] => ForumModule,StandardPlugin,StudipModule
[enabled] => 1
[position] => 50
[depends] => 0
[core] => 1
[class_exists] => 1
```

## Alle verfügbaren Plugins auflisten

```shell
./cli/plugin_manager scan
```

Listet alle Plugins auf, die im Dateisystem vorhanden sind, aber nicht in der Datenbank registriert sind.

```shell
[pluginclassname] => Achievements
[pluginname] => Achievements
[origin] => tgloeggl
[version] => 0.1.2
[path] => /path/to/trunk/public/plugins_packages/tgloeggl/Achievements
```
