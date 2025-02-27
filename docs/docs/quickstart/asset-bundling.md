---
title: Asset Bundling
---

## Asset Bundling 
Asset Bundling ist das Zusammenstellen und  nötigenfalls Kompilieren von Modulen. Module können u.a. JavaScript, CSS, LESS-CSS enthalten. Module haben Abhängigkeiten unter einander. Asset Bundling löst diese Abhängigkeiten auf, kompiliert Module falls nötig und generiert zum Beispiel fertige JS- und CSS-Dateien.

Stud.IP verwendet in der Version 4.2 und aufwärts den Open-Source-Asset-Bundler "webpack".

Die Konfigurationsdateien liegen im Projektverzeichnis und lauten "webpack.dev.js", "webpack.dev-server.js" und "webpack.prod.js".

Damit stehen drei Modi zum Asset-Bundling zur Verfügung:

* `make webpack-dev`: Schnell. Kompiliert und bundled im Developer-Modus. Die Bundles sind nicht optimiert und leicht zu debuggen.
* `make webpack-prod`: Langsam. Kompiliert und bundled im Production-Modus. Die Bundles sind optimiert und mit Source Maps zu debuggen.
* `make wds`: Sehr schnell. Startet den webpack-dev-server. Die $ASSETS_URL muss geändert werden: `$ASSETS_URL = "http://localhost:8123/";` (ab Stud.IP 4.4 wird die Anpassung der `$ASSETS_URL` automatisch vorgenommen, wenn der webpack-dev-server läuft).

**Einmalig** vor dem ersten `make`-Aufruf müssen die npm-Pakete installiert werden:

`npm install`

Wenn man dann JavaScript- oder CSS-Dateien modifiziert hat, ruft man `make webpack-dev` oder `make webpack-prod` auf, damit die Änderungen in die Output-Dateien hineinkompiliert werden.

### make webpack-dev vs. make webpack-prod

Da `webpack-dev` einiges schneller ist (aber nicht optimiert), eignet sich `make webpack-dev` für die lokale Entwicklung. 

Sobald man Änderungen im SVN einchecken möchte, sollte man dringend `make webpack-prod` aufrufen. 
Da das Developer-Board direkt die Dateien aus dem `trunk` nimmt, sollten dort auch nur optimierte Dateien liegen, da sonst alle Developer-Board-Nutzer längere Ladezeiten haben. 

Bei `make webpack-prod` werden die Debug-Informationen in Sourcemap-Dateien hinterlegt, mit denen das Debugging ähnlich komfortabel sein sollte.

Auf einem Testsystem läuft `make webpack-dev` in ~6 Sekunden durch. Die `make webpack-prod`-Variante benötigt auf dem System ~17 Sekunden. 
Am schnellsten ist aber  `make wds`, das ab dem Zeitpunkt des **Abspeicherns** der Änderung weniger als 2 Sekunden benötigt


**Wann sollte man `make webpack-dev` aufrufen?** 

Immer wenn man gerade nur lokal entwickelt, ohne etwas einzuchecken.

**Wann sollte man `make webpack-prod` aufrufen?** 

Immer bevor man JS- oder CSS-Änderungen im SVN einchecken wird. Vorher sollte man aber auf jeden Fall `npm install` aufrufen, um sicher zu sein, dass man die aktuellsten Versionen der verwendeten Bibliotheken vorliegen hat.


### make wds

Mit `make webpack-prod` und `make webpack-dev` werden die Assets auf der Kommandozeile zusammengebaut. Der webpack-dev-server hingegen wacht über die Assets-Dateien; ändert sich eine davon, stößt er einen inkrementellen Build an und lädt im Browser die Seite neu.

Wenn man den webpack-dev-server verwenden möchte:

* ändert man zunächst in der Datei config_local.inc.php die ASSETS_URL auf:  `$ASSETS_URL = "http://localhost:8123/";`
* ruft dann auf der Kommandozeile `make wds` auf.

Die Assets werden dann inkrementell zusammengebaut und über `http://localhost:8123/` ausgeliefert. 

Über die magische URL: `http://localhost:8123/webpack-dev-server` sieht man, was der webpack-dev-server ausliefert


### Kryptische Namen von gebundelten JavaScript-Dateien

Damit nach einem Versionswechsel einer Stud.IP-Installation keine Caching-Probleme beim Nutzer entstehen, wurde vor einigen Jahren eingeführt, dass die JavaScript-Dateien via PHP mit einem speziellen versionsabhängigen URL-Parameter eingebunden werden, der damit das Caching-Problem löst.

Wenn nun JavaScript-Dateien dynamisch geladen werden (siehe [JavaScript in Stud.IP](HowToJavascript)), werden die nachzuladenden Dateien direkt über JS eingebunden, ohne den Umweg über PHP zu nehmen. Damit kann man also den vorhandenen PHP-Mechanismus nicht mehr verwenden. Das Problem wird in ticket:9114 berichtet. Die nachzuladenden JS-Dateien werden als "chunks" bezeichnet.

Glücklicherweise bietet webpack zu diesem Zweck  die Konfigurationsmöglichkeit, [den Dateinamen der nachzuladenden JS-Dateien ("chunks") anzupassen](https://webpack.js.org/configuration/output/#output-chunkfilename):

```shell
output: {
  chunkFilename: "javascripts/[name]-[chunkhash].chunk.js",
}
```

Beim Asset-Bundling bekommen damit die "chunks"  einen Namen, der einen Hash-Wert über den Inhalt des Chunks enthält. 
Ändert sich der Chunk inhaltlich bekommt er einen neuen Namen. An den Stellen im JS-Code, an denen auf diesen "chunk" verwiesen wird, setzt webpack automatisch diesen inhaltabhängigen Namen ein. Das ganze funktioniert natürlich automatisch, sodass man als Entwickler keine Mühe damit hat.

**ABER:** Ändert man den "chunk" oder updatet (automatisch) 3rd-party-Bibliotheken, die im "chunk" verwendet werden, ändert sich auch der Name der gebundelten "chunk"-Datei. Ruft man nach so einer Änderung "make" oder "webpack []" auf, entstehen neue "*.chunk.js" Dateien. Wenn man diese Dateien in eine Versionsverwaltung (svn oder git) einchecken möchte, muss man dann zuvor die alten "chunk"-Dateien abräumen und die neuen "chunk"-Dateien hinzufügen. **Dies ist aber lediglich dann erforderlich, wenn man an diesen Dateien Änderungen vornimmt.**

Damit kann es also passieren, dass Änderungen am Tablesorter durch Caching nicht unmittelbar beim Nutzer ankommen. Dieses Problem habe ich in ticket:9114 berichtet.

