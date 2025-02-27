---
id: assets
title: Assets
sidebar_label: Assets
---

### Einleitung

Alle Bilder, JavaScript- und Stylesheet-Dateien von Stud.IP liegen in einem gemeinsamen Verzeichnis `public/assets` (siehe zum Beispiel: https://gitlab.studip.de/studip/studip/-/blob/main/public/assets)

Ausserdem exisitiert die Konfigurationsvariable `$GLOBALS['ASSETS_URL']`, die eine URI zu diesem Verzeichnis enthält. Standardmässig verweist diese auf das relativ zur jeweiligen Stud.IP-URI gelegene Verzeichnis `assets`.

Beispiel: Ein Stud.IP liegt unter `https://www.meinstudip.de`, dann liegt das Assets-Verzeichnis standardmässig unter `https://www.meinstudip.de/assets`.

Zweck dieser Übung ist, statische Inhalte von anderen Servern ausliefern zu können, um so die eigentlichen Webserver zu entlasten. Ein speziell eingerichteter Assets-Webserver ist viel effizienter in der Auslieferung der statischen Inhalte, als das der normale Webserver könnte.

### Einrichten eines speziellen Assets-Webservers

Vorraussetzung ist ein bereits vorhandener Webserver, der als Assets-Webserver dienen kann. In dieser Hinsicht ist [lighttpd](http://lighttpd.net) sehr zu empfehlen. Kopieren Sie nun einfach das komplette `assets`-Verzeichnis in Ihren Webbereich und notieren Sie sich die URI für dieses Verzeichnis. In Ihrer Stud.IP-Installation öffnen Sie die Konfigurationsdatei `config/config_local.inc.php` und suchen Sie dort nach dem Text `$ASSETS_URL = $ABSOLUTE_URI_STUDIP . 'assets/';`, den Sie dann in die oben notierte URI ändern müssen. **Achten Sie darauf, dass die `$ASSETS_URL` mit einemn Slash enden muss.

### Verwendung der Klasse Assets

Um Bilder, JavaScripts usw., die sich im Assets-Verzeichnis befinden, im HTML-Markup ansprechen zu können, bestünde selbstverständlich die Möglichkeit, direkt die globale Variable `$ASSETS_URL` zu verwenden. Einfacher geht es aber mit der Klasse `Assets`, zudem bietet die Klasse einige Vorteile bei der dynamischen Auslieferung von Grafik-Assets zB. für Retina Displays. 

**Für die Einbindung von Icons wird seit Stud.IP v3.4 nicht mehr die Klasse `Assets`, sondern die gesonderte Icon-API verwendet.**

Die Verwendung der Klasse soll hier kurz dargestellt werden. 

`echo Assets::img('blank.gif');` gibt einen kompletten Image-Tag aus:

```php
<img alt="Blank" src="assets/images/blank.gif" />
```

Will man das `alt`-Attribut ändern oder weitere Attribute hinzufügen, kann man einfach als zweiten Parameter ein Array von Attribut => Attributwerten hinzufügen:

```php
echo Assets::img('blank.gif', array('alt' => 'nothing here', 'class' => 'some_class'));
<img alt="nothing here" class="some_class" src="assets/images/blank.gif" />
```

## Retina-Auflösungen

Die Retina-Klasse kümmert sich automatisch  um das Einsetzen von Grafiken in Retina-Auflösung, wenn der Nutzer bei der Anmeldung an einem System mit einer entsprechende Auflösung arbeitet (dieses Verhalten ist ab der Version 2.5 vollständig implementiert). Bei allen Grafiken muss per Parameter darauf hingewiesen werden, dass die entsprechende Grafik auch in einer Retina-Version vorliegt.

Will man nun Grafiken auch in Retina-Auflösung bereitstellen und die Assets-Klasse automatisch die korrekte Größe bzw. Grafikdatei auswählen lassen, so sind folgende Vorraussetzungen zu erfüllen:

- Zunächst muss eine Grafik in doppelter Auflösung (oder anders ausgedrückt in doppelter Größe in X und Y-Dimension) erstellt werden.
- Die Grafik muss im gleichen Verzeichnis wie die Originaldatei mit dem Zusatz "@2x" (vor der Dateiendung und dem Punkt) abgelegt werden. Zu `header_logo.png` gesellt sich somit zB. `header_logo@2x.png`.
- Beim Aufruf muss der Parameter "@2x" gesetzt sein. Erst dann sucht die Assets-Klasse nach einer entsprechend größeren Grafikdatei (es gibt keine automatische Suche nach einer Retina-Datei).

*Ein bisschen Hintergrund*: Retina-Grafiken werden auf vielen Smartphones oder Tablets mit hochauflösenden Displays verwendet. Auch die ersten Notebooks mit doppelter Auflösung sind erhältlich. Seit der Version 2.4 prüft Stud.IP beim Login die Pixel-Ratio des Ausgabegeräts und legt diese in der Session ab (der Wert heisst "'devicePixelRatio"). Wenn dieser Wert 2 ist, nimmt Stud.IP für die Dauer der Session eine Retina-Auflösung an. Die Grafiken werden - sofern "@2x" gesetzt ist, in der doppelten Auflösung geladen, aber weiterhin mit der Pixel-Ratio 1 angezeigt. Eine Grafik mit 44*44 wird also aus einer 88*88 Datei geladen, ihr aber die Größe 44*44 mitgegeben. Die aktuellen Browser entscheiden dann, wie sie mit der größeren Pixelanzahl umgehen. Damit aber diese Grafiken nicht auch auf nicht-Retina-Displays geladen und skaliert werden müssen, müssen oben genannte Bedingungen erfüllt werden. Auf Bildschirmen mit geringer Auflösung wird die Grafik dann trotzdem herunterskaliert, falls irrtümlich Retina angenommen wurde.
