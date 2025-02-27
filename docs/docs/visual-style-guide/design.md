---
title: Design
sidebar_label: Design
---

##  Farben und Farbraum
Farben sind mit das wichtigste Gestaltungsmittel. Richtig eingesetzt, können Farben Anwendern helfen Aufgaben leichter durchzuführen.

Die Standard-Farben des aktuellen Stud.IP Designs wurden von den Core-Group
GUI-Verantwortlichen in Zusammenarbeit mit einem Designer festgelegt.

Das Farbschema baut auf einigen Grundfarben und festen Kontrastabständen auf.

Prinzipiell kann jede Stud.IP Installation durch Anpassen der CSS-Dateien farblich verändert werden. Zu beachten ist, dass jedoch nur die base-color angepasst werden sollte. Dadurch verändern sich andere Farben entsprechend den vorgegeben Farbwerten.
(Dies setzt allerdings voraus, dass die Farben in den less-Dateien angepasst werden und mit dem less-Compiler kompiliert werden. Auch ist zu beachten, dass die vollständige Implementierung der Stud.IP-CSS-Dateien erst in Zukünftigen Versionen umgesetzt sein wird).
Von allen Basisfarben sind in dem Farbklima Abschwächungen in 20% Schritten vorgesehen, die automatisch über den less-Compiler erzeugt werden.

## Bedeutung und Auswahl der Farben in Stud.IP

Farben sind mit Bedacht zu wählen, da diese wie optische Methaphern wirken und Emotionen ansprechen. Daher ist es wichtig, Farben konsistent zu verwenden. Bislang werden Farben in Stud.IP wie folgt eingesetzt:

![image](../assets/476d123391bbc2e4a825a1ea146a8465/image.png)

PDF Download: [170804_Studip-Farbset.pdf](../assets/6d14189aa9093eb042bfa56eae8c7dc2/170804_Studip-Farbset.pdf)

### Blau (base-color und content-color)
Blau ist in verschiedenen Abstufungen die Standard-Hintergrundfarbe für das aktuelle Stud.IP Theme.
Die Basisfarbe ist #28497c. Zur Hinterlegung von Content (also Inhalte innerhalb des Blatt Designs) wird der Farbwert #899ab9 als Basis genommen.

Blau wird zusätzlich für klickbare Objekte verwendet, d. h. mit blau werden Text-Links und klickbare Icons gekennzeichnet.

Blau ist auch die Hintergrundfarbe für [Messageboxen](MessageBox) mit Informationsmeldungen.

Die base-color kann von Betreiber angepasst werden an eigene Farben, zB. um dem eigenen CD zu entsprechen. Die content-color sollte nicht angepasst werden.

### Grau (light-gray, dark-gray)

Zur Hinterlegung verschiedener Bereiche (zB. Infoboxen, Navigation) existieren unterschiedliche Grautöne, die frei verwendet werden dürfen. Die Basiswerte sind #69767f (light-gray) und #3c454e (dark-gray).
Inhalte (also Tabellen, Textbeiträge, Nachrichten, Formulare) dürfen nur mit der content-color (blau) hinterlegt werden. Andere Objekte können auch mit Grau hinterlegt werden.

## Markierungsfarben

Neben den Grundfarben werden Farben auch für unterschiedliche Markierungen/Kategorisierungen verwendet. Die dafür erlaubten Farben sind ebenfalls definiert:

![image](../assets/1cde32e97e840ad35cd7840e4d61b016/image.png)

### Rot
Rot wird als Signalfarbe an mehreren Stellen eingesetzt:

Rot kennzeichnet zum einen kritische Aktionen und wird somit beispielsweise als Rahmenfarbe für Fehlermeldungen verwendet. Auch das Icon in einer Fehlermeldung  ist rot gefärbt.

Zum anderen wird alles Neue (aus Sicht des jeweiligen Nutzes) in Rot hervorgehoben. So kommen rote Icons beispielsweise auf der Seite "Meine Veranstaltungen" zum Einsatz, wenn einer der Bereiche einer Veranstaltung für den Nutzer neue Inhalte enthält. Auch in den Bereichen der Veranstaltungen gibt es an mehreren Stellen rote Markierung für neue Beiträge.

Basisfarbton für rot ist: `#d60000`

### Grün
Grün wird lediglich für positive Rückmeldungen verwendet. Grün ist z. B. die Rahmenfarbe Meldungen mit Erfolgsbestätigung.

In der Gestaltung von Stud.IP.Inhalten oder anderen Elementen darf grün nicht eingesetzt werden!

### Gelb (activity-indikator)
Gelb wird lediglich als Markierungsfarbe genutzt.

Beispielsweise ist der Indikator, welche Ansicht in einer Seite mit mehreren Ansicht gewählt wurde, ein gelber Pfeil.

Im Forum oder dem Wiki markiert die Farbe Gelb Fundstellen in der Trefferliste.

Gelbe Verschiebepfeile zum Umsortieren von Objekten sind in der aktuellen Gestaltung nicht mehr zulässig.

Basisfarbe ist `#ffbd33`

### Schwarz und Weiß
Schwarz und Weiß werden als Schrift- und Kontrastfarbe verwendet. Schrift und Symbole werden je nach Hintergrund schwarz oder weiß gezeichnet.

### Hinweise
Die hexadezimalen Werte der Farben sind in LESS-Dateien (`public/assets/stylesheets/mixins/colors.less`) definiert und müssen bei Anpassungen mit einem entsprechenden Less-Compiler in die Stylesheets übertragen werden.
Das händische Anpassen einzelner Farbwerke in den CSS-Dateien wird ausdrücklich nicht empfehlen, da einige Farben auch in Abhängigkeiten zueinander (etwa von der Base-color) definiert/erzeugt werden.

Die Verwendung von Farben für Icons wird im Abschnitt zu [Icons](Visual-Style-Guide#Icons) ausführlicher beschrieben.

### Allgemeine Hinweise zur Farbauswahl

#### Farben mit Bedacht und sparsam verwenden
Farben sollten sparsam verwendet werden. Um Bereiche im [Inhaltsbereich einer Stud.IP Seite](http://hilfe.studip.de/develop/Style/DesignSeitenlayout) durch Farbe zu kennzeichnen, empfiehlt es sich laut ISO 9241-12 nicht mehr als sechs (zusätzlich zu schwarz und weiß) verschiedene Farben zu verwenden. Die verwendeten Farben sollten durch den Anwender gut unterscheidbar sein.

#### Farbe nicht als alleiniges visuelles Hilfsmittel verwenden
Farben sollten nicht als einziges visuelles Mittel verwendet werden, um Informationen zu vermitteln oder Elemente zu kennzeichnen. Für  farbenfehlsichtige Nutzer ist es möglicherweise schwierig zwei Objekte  zu unterscheiden, die sich nur in ihrer Farbe unterscheiden. Unterschiede sollten zusätzlich durch z. B. unterschiedliche Formen, Positionen oder eine textuelle Beschreibung gekennzeichnet werden.

#### Farbtöne mit gleichen Sättigungsgrad verwenden
Um eine harmonisches Farbdesign zu erreichen, sollten Farbtöne verwendet werden, die den gleichen Sättigungsgrad aufweisen. Sättigung (bzw. Buntheit) bezeichnet den Grauanteil einer Farbe. Je weniger Grau eine Farbe enthält, desto leuchtender wirkt sie.

Große Flächen sollten nicht in leuchtenden (gesättigten) Farben gestaltet werden. Diese werden schwer lesbar und können mitunter Kopfschmerzen verursachen.

#### Ist der Kontrast zwischen Elementen und ihrem Hintergrund ausreichend?
Wenn sich der Farbton von Vorder- und Hintergrundfarben zu sehr ähnelt, sind Unterschiede schwer erkennbar.

Tipps zur Überprüfung des Kontrastes einer Farbkombination:
* Um zu überprüfen, ob ein ausreichender Kontrast vorhanden ist, empfiehlt es sich die Seite schwarz-weiß zu drucken. Wenn der Ausdruck gut lesbar ist, ist typischerweise ein ausreichender Kontrast vorhanden.
* Mit dem Online-Tool [Color Contrast Checker](http://www.snook.ca/technical/colour_contrast/colour.html) kann direkt überprüft werden, ob ein ausreichender Kontrast zwischen zwei Farben vorhanden ist.
* Auf manchen Betriebsystemen kann auch die Darstellung bereits als Grundeinstellung in Graustufen geschaltet werden, um die Kontraste zu testen.

#### Unruhige und ablenkende Hintergründe vermeiden
Ungünstig sind Muster oder Bilder im Hintergrund, die einen ungleichmäßigen Kontrast verursachen, das Auge vom Text ablenken und damit die Lesbarkeit erschweren.

### Weiterführende Links
* Tutorial: Farben im Webdesign [http://metacolor.de/](http://metacolor.de/)
* [Farb/Kontrastanalysen mit Bezug auf a11y-Kriterien](http://www.blog.mediaprojekte.de/grafik-design/farb-kontrast-analyse-die-accessibility-der-farben-testen)
* http://e-campus.uibk.ac.at/planet-et-fix/M8/8.5.2_Praesentationen/links/farben.html
