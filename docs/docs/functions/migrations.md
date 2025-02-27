---
id: migrations
title: Migrationen
sidebar_label: Migrationen
---

## Grundlegendes zu Migrationen

Im Zusammenhang mit einem Update notwendige Änderungen (vor allem) am Datenbankschema oder Inhalten in der Datenbank werden in *Migrationen* verpackt - das sind kleine, isolierte Transformationen, die beim Update in einer definierten Reihenfolge ausgeführt werden können, um von einem Versionstand zu einem anderen zu gelangen. Falls die Migrationen das vorsehen, ist auf dem gleichen Weg ein Rollback bzw. Downgrade des Datenbankschemas möglich.

Für die Verwendung in Plugins (oder für standortbezogene Schemaerweiterungen) gibt es noch Migrationsdomänen: Jede Domäne enthält eine komplett eigenständige Sammlung von Migrationen, die unabhängig von Migrationen aus anderen Domänen ausgeführt werden können. Die aktuelle Schemaversion wird für jede Domäne separat gespeichert, damit besitzt insbesondere jedes Plugin einen eigenen Raum von Versionsnummern für seine Migrationen.

### Struktur und Numerierung

Im einfachsten Fall bilden die Migrationen eine lineare Abfolge, dies war bis zur Version 4.3 auch die einzig mögliche Anordnung:

```mermaid
graph LR
  subgraph default
  0((0)):::dashed-->1-->2-->3-->4-->5-->6:::current
  end
  classDef current stroke:#000080
  classDef dashed stroke-dasharray:2
```

Genau eine Position ist als "aktuelle Schemaversion" (hier im blau) markiert. Die Stelle "0" darf dabei nicht als Nummer einer echten Migration vergeben werden: Sie steht nur für die Position "vor allen weiteren Migrationen" (z.B. wenn noch gar keine ausgeführt wurde).

Ab Stud.IP 5.1 kann es allerdings auch Abzeige geben - das ist beispielsweise sinnvoll, wenn man nachträglich zwischen Stand 2 und 3 noch weitere Migrationen unterbringen möchte:

```mermaid
graph LR
  subgraph default
  0((0)):::dashed-->1-->2-->3-->4-->5-->6:::current
  2-->2.0
  subgraph two [2]
  2.0((2.0)):::dashed-->2.1-->2.2:::current-->2.3
  end
  end
  classDef current stroke:#000080
  classDef dashed stroke-dasharray:2
```

Ein Abzweig ("Branch") kann an jeder beliebigen Stelle aufgehängt werden, eine Migration an dem entsprechenden Knoten muß dazu auch nicht existieren. Ein Abzweig trägt immer den Namen des Knotens, bei dem er abzweigt (im obigen Beispiel "2"), den Versionen auf dem Abzweig wird dieser Name (gefolgt von einem ".") vorangestellt, also "2.1" usw. Auch hier ist die Stelle "2.0" reserviert und kann nicht als echte Migration verwendet werden.

Jeder Abzweig hat eine eigene Positionsmarkierung für die auf diesem Branch bereits ausgeführten Migrationen. In einer (gedanklichen) linearisierten Reihenfolge aller Migrationen sind alle Migrationen auf einem Branch zwischen dem Abzweigpunkt und seinem Folgeknoten auf dem übergeordneten Branch angeordnet. In diesem Fall wäre das also: 1, 2, 2.1, 2.2, 2.3, 3, 4, 5, 6.

Auch Abzeige können ihrerseits natürlich wieder Abzweige bekommen (so tief man möchte):

```mermaid
graph LR
  subgraph default
  0((0)):::dashed-->1-->2-->3-->4-->5-->6:::current
  2-->2.0
  4-->4.0
  subgraph two [2]
  2.0((2.0)):::dashed-->2.1-->2.2:::current-->2.3
  2.1-->2.1.0
  subgraph two-one [2.1]
  2.1.0((2.1.0)):::current-dashed-->2.1.1
  end
  end
  subgraph four [4]
  4.0((4.0)):::dashed-->4.1:::current
  end
  end
  classDef current stroke:#000080
  classDef current-dashed stroke:#000080,stroke-dasharray:2
  classDef dashed stroke-dasharray:2
```

### Struktur und Numerierung im Stud.IP-Kern

Die Struktur der Migrationen im Kern sieht derzeit (mit einem kleinen Vorgriff auf 5.2) so aus:

```mermaid
graph LR
  subgraph default
  0((0)):::dashed-->1-->5
  1-->1.0
  5-->5.0
  subgraph one [1]
  1.0((1.0)):::dashed-->1.1-->1.2-->1.3-->1.4-.->1.327
  end
  subgraph five [5]
  5.0((5.0)):::dashed-->5.1-->5.2
  5.1-->5.1.0
  5.2-->5.2.0
  subgraph five-one [5.1]
  5.1.0((5.1.0)):::dashed-->5.1.1-->5.1.2-.->5.1.25
  end
  subgraph five-two [5.2]
  5.2.0((5.2.0)):::dashed-->5.2.1-->5.2.2
  end
  end
  end
  classDef dashed stroke-dasharray:2
```

Alle "alten" Migration bis einschließlich zur 5.0 liegen auf einem Branch "1", und für aktuelle Releases ≥ 5.1 gibt es jeweils einen eigenen Branch mit dem Names des Releases und den dazugehörigen Migrationen. Bei Service-Releases können diese Branches unabhängig voneinander mit zusätzlichen Migrationen bestückt werden.

### Aufbau von Migrations-Dateien (d.h. Klassen):

Grundsätzlich müssen alle Migrationsklassen die Klasse `Migration` erweitern.

Sie bestehen in der Grundlage aus mindestens zwei Funktionen `up()` und `down()`. In `up()` werden die Änderungen für diesem Schritt durchgeführt und entsprechend in `down()` die Änderungen der `up()` Methode wieder rückgängig gemacht, soweit das sinnvoll ist. (*->siehe Irreversible Migrationen*)

Die optionale Funktion `description()` liefert eine kurze Beschreibung der durchzuführenden Migration. 

In Kernmigrationen sollten keine APIs aus dem Kern verwendet werden (beispielsweise `Config`), da nicht gewährleistet ist, dass diese sich nicht ändern werden. Es sollten also z.B. immer die entsprechenden Datenbankeinträge manuell vorgenommen werden. Für Plugins gilt dies nicht: Pluginmigrationen sollten immer die entsprechende API verwenden. Falls Pluginmigrationen APIs des Plugins verwenden, kann es allerdings analoge Probleme zu Kern-APIs in Kernmigrationen geben - also hier auch vorsichtig sein.

### Namensgebung für die Dateien

Die Migrationsdateien sollen fortlaufend nummeriert werden, beginnend bei 1 und haben immer ganzzahlige Versionsnummern (1, 2, 3, usw.). Es sollten keine (unnötigen) Löcher in der Nummerierung vorhanden sein, führende Nullen dürfen verwendet werden (z.B. "001" statt "1") - diese haben keine Auswirkung auf die Einsortierung. Zusätzlich zur Version kann es einen Branch geben, der der Versionsnummer vorangestellt wird, z.B. "5.1" - die komplette Bezeichnung ist dann *Branch*`.`*Version*`_klassenname.php`. Beispiele dafür wären 3.1 oder 289.5.2. Der Branch ist optional und kann verwendet werden, um nachträglich Migrationen "zwischen" vorherige Migrationen schieben zu können.

Als Analogie kann man sich die Kombination aus Branch und Versionsnummer wie eine Software-Versionsbezeichnung vorstellen. Daraus ergibt sich auch implizit eine Reihenfolge aller Migrationen.

Für das Stud.IP-Release wird ab der Version 5.1 folgendes Numerierungsschema verwendet (siehe auch das Diagramm oben):

* Alte Migrationen (vor 5.1) haben Nummern auf dem 1.x Branch, also "1.1" usw. Neue Migrationen auf diesem Branch kann es nicht mehr geben.
* Migrationen ab 5.1 bekommen Nummern gemäß der Version, ab der sie hinzugefügt wurden - z.B. "5.1.1", "5.2.3" usw.
* Migrationen mit Fehlerkorrekturen bekommen Nummern gemäß der ältesten Version, in der sie landen sollen - allerdings keinesfalls vor 5.1 (denn vorher gab es das neue Schema ja nicht).

Jede Domäne (d.h. jedes Plugin) hat eine eigenständige Zählung der Migrationsschritte und Plugins müssen sich auch nicht an das o.g. Schema halten, d.h. sie können weiterhin einfach ihre Migrationen fortlauftend ab 1 numerieren. Plugin-Migrationen können aber natürlich auch in Branches aufgeteilt werden.

### Rückportierung von Migrationen in alte Versionen

Grundsätzlich ist die Idee, dass neue Migrationen, bei denen eine Rückportierung notwendig ist, direkt eine Versionsnummer auf dem frühesten Branch bekommen, in den sie portiert werden sollen (entspricht also der Versionsnummer am Ticket). D.h. die Migration hat auf allen Release-Branches die gleiche Bezeichnung.

**Achtung**: Das funktioniert natürlich nicht für Stud.IP Versionen vor 5.1 - denn dort existieren ja gar keine in Branches aufgeteilte Migrationen. D.h. bei einer Rückportierung in eine Version vor 5.1 bekommt die Migration zwei unterschiedliche Nummern:
- eine Nummer mit "5.1.x" für Stud.IP-Version 5.1 und größer
- eine Nummer basierend auf dem Datum (altes Namensschema) für Stud.IP-Version 5.0 und darunter

### Reversible und Irreversible Migrationen

Bei reversiblen Migrationen besteht die Möglichkeit über die `up()` und `down()` Methoden immer in eine andere Version zu springen. Bei irreversiblem Migrationen verändert die `up()` Funktion die vorhandenen Daten derart, dass ein Aufruf der `down()` Funktion diese nicht wieder herstellen kann. In solchen Fällen sollte eine Fehlerbehandlung in der `down()` Funktion des Migrationsschrittes erfolgen.

### Ausführung von Migrationen

Für die Ausführung von Migrationen gibt es zwei Möglichkeiten:

#### Ausführung über die Kommandozeile

In dem Ordner `cli` befindet sich ein Skript, das die Migrationen über die Kommandozeile ausführt. Hier ist auch das Anstoßen von Migrationen in Plugins möglich, die vom Webinterface nicht direkt unterstützt werden.  

##### Mögliche Parameter

| Parameter | Beschreibung |
| ------ | ------ |
| d | Domäne (default studip)  |
| m | Dateipfad zum Ordner mit den Migrationsdateien |
| l | Nur auflisten was getan werden soll nicht migrieren |
| t | Zielmigration (0 für komplettes Reset, andernfalls Zielversionsnummer) |
| b | Branch, auf dem die Migration passieren soll (optional) |
| v | verbose (empfohlen) |

Beispiel: Stud.IP Migrationen von 6 rückgängig machen auf 5:
`cli/migrate.php -d studip -t 5 -v`

Beispiel: Ausgabe mit l Parameter:
`cli/migrate.php -d studip -l -t 18`

``` 
  3 Step87ExternConfigurations Extends table extern_config and converts configurations for the external pages from 
    INI-style files to serialised arrays stored in the database.
  4 Step116ParticipantView creates table necessary for StEP116
  5 Step25RaumzeitMigrations modify db schema for StEP00025; see logfile in $TMP_PATH
  6 Step25RaumzeitDbConversion convert dates for StEP00025; see logfile in $TMP_PATH
  7 TableTokenClass      creates table for Token class
  8 Step117StudienModule modify db schema StEP00117 Studienmodulstrukturen; 
  9 StEP00111Admission   creates table admission groups
 10 ImageProxy           creates table image_proxy_cache and config entry EXTERNAL_IMAGE_EMBEDDING
 11 LockRules            creates table for lock rules
 12 Step120Userpic       modify existing user pictures according to Step00120
 13 RemoveFieldsFromExtermine removes expire|repeat|color|priority from table ex_termine
 14 StEP00123Admission2  modifies table seminare, adds field `admission_enable_quota`
 15 Step00129EmailRestriction Adds the new Value EMAIL_DOMAIN_RESTRICTION to table config.
 16 Step00126EmbeddingFlashMovies Adds the new values EXTERNAL_
    FLASH_MOVIE_EMBEDDING and DOCUMENTS_EMBEDD_FLASH_MOVIES to table config.
 17 DbOptimierungKontingentierung adds keys in admission_seminar_studiengang, admission_seminar_user and seminar_user
 18 Step00139UploadFileReorg reorganize uploaded files into sub-folders@@
````

#### Ausführung über die Web-Oberfläche

Das *web_migrate* Skript unter `/public/web_migrate.php` hat die gleichen Funktionen wie die oben beschriebene Kommandozeiolenversion, kann aber interaktiv verwendet werden (siehe Screenshot).

![Bildschirmfoto_2021-11-12_um_11.04.40](../assets/13c6d38bf74b403424e42c16f8308bfe/Bildschirmfoto_2021-11-12_um_11.04.40.png)

Wählt man links einen anderen Branch als "default" aus, werden nur Migrationen auf bzw. unterhalb des gewählten Branches zur Ausführung angeboten. Liegen Migrationen direkt auf dem gewählten Branch, können diese auch direkt ausgewählt werden (analog zur Option `-t` in der cli-Version) - die Verarbeitung endet dann, wenn die markierte Migration erreicht ist.

## Beispielplugin mit Migration

Elmar hat einfach mal ein kleines Beispiel fertiggemacht: das "DummyPlugin" (ein Plugin, da die Verwendung für die Plugin-Schnittstelle vorrangig von Interesse ist). Die Katalogstruktur des Plugins sieht folgendermaßen aus:
```
  DummyPlugin.class.php 
  plugin.manifest 
  sql/ 
    sql/dummy_install.sql 
    sql/dummy_uninstall.sql 
  migrations/ 
    migrations/1_test.php 
    migrations/2_foo.php 
```

Die SQL-Dateien unter sql definieren wie gehabt das "Grundlayout" für das Plugin und sind entsprechend als "dbscheme" (bzw. "uninstalldbscheme") im Manifest eingetragen, soweit also nichts neues. Neu hingekommen ist der Katalog migrations, der die einzelnen Deltas enthält, die von Plugin-Version zu Plugin-Version nachgezogen werden müssen (diese werden also nicht mehr in sql/dummy_install.sql abgebildet). Jeder Versionsschritt des Plugins kann beliebig viele solche Deltas ( = Migrations) besitzen. Alle Migrations sind aufsteigend numeriert, die Dateinamen folgen dabei der Konvention `nummer_klassenname.php`.

Jede Migration ist eine PHP-Klasse mit den Operationen up() und down(), die die jeweiligen Änderungen durchführen bzw. wieder zurücknehmen können. Als Beispiel hier der Inhalt von `migrations/1_test.php`:

```php
  <? 
  class Test extends Migration { 
      function up () {
          $db = DBManager::get();
          $db->exec("INSERT INTO dummy VALUES (42, 'axel')"); 
      } 

      function down () {
          $db = DBManager::get();
          $db->exec("DELETE FROM dummy WHERE id = 42"); 
      } 
  } 
  ?> 
```
Anstatt Werte in eine Tabelle einzutragen könnte man natürlich ebenfalls neue Tabellen anlegen, Felder hinzufügen oder entfernen, Daten umwandeln oder in eine andere Tabelle kopieren, Dateien oder Kataloge anlegen, Rechte setzen usw. (es muß nichts mit der DB zu tun haben). Der Zugriff auf die Datenbank erfolgt dabei wie üblich über die [DBManager-Klasse](Howto/Datenbankzugriffe). Alles weitere passiert (bei Plugins) automatisch, d.h. beim Update eines Plugins werden - ausgehend vom aktuellen Versionsstand - alle notwendigen Änderungen (d.h. Migrations) durchgeführt bzw. bei einem Downgrade eines Plugins entsprechend wieder zurückgenommen.

PS: Wenn man möchte, kann man natürlich auch "dbscheme" und "uninstalldbscheme" im Manifest weglassen und das Anlegen der kompletten DB-Struktur für das Plugin über Migrations erledigen. 

Für ein Beispiel für die Migration eines Plugins gibt es hier ein kleines Zip file:

[dummy_plugin-v0.3.zip](../assets/0ca19cdf7ae62c47c4a74b0110030059/dummy_plugin-v0.3.zip)
