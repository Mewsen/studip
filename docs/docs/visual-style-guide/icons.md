---
title: Icons & Grafiken
sidebar_label: Icons & Grafiken
---

Seit der Version 2.0 bringt Stud.IP ein einheitliches Icon-Set mit. Das Set zeichnet sich durch eine klare und einheitliche Gestaltung, nach Aufgaben und Einsatzbereichen getrennte Farb-Sets, barrierearme Bedienung durch klare Formen/optische Zusätze für bestimme Aufgaben und eine Reihe weiterer Merkmale, die die Stud.IP-Formensprache vereinheitlichen, aus. Des weiteren liegen alle Icons bereits als Vektorgrafiken vor, so dass eine Verwendung in anderen Kontexten (zB. Print) und eine Veränderung der Icon-Größe in Stud.IP (etwa für eine verbesserte Touch-Bedienung) zukünftig möglich ist.
Das Iconset findet grundsätzlich für alle grafischen Schaltflächen, Markierungen, Objekt-Darstellungen usw. Anwendung. Als einzige Ausnahme davon existieren in Stud.IP weiterhin die Textbuttons und einige wenige Sonderformen oder nicht-iconartige Grafiklelemente (zB. Linien oder Trenner).


Generell werden alle Grafiken im `assets`-Ordner `images` im `public`-Ordner gespeichert. Diese wurden in 2.0 neu strukturiert und aufgeräumt. Somit ergibt sich dort folgende Struktur:

| Pfad | Beschreibung |
| ---- | ---- |
| `public/assets/images/calendar` | Hier sind alle Hintergrundgrafiken drin, die für den Kalender und Stundenplan benötigt werden. |
| `public/assets/images/crowns` | Die Kronen, die ein Benutzer als Auszeichnung bekommen kann |
| `public/assets/images/header` | `deprecated` (Diese Icons werden noch gelöscht) |
| `public/assets/images/icons` | Der neue Ordner für alle Icons, ausführliche Informationen siehe unten. |
| `public/assets/images/infobox` | Alle Bilder mit abgerundeter Ecke in Schwarz-weiß für die Infoboxen |
| `public/assets/images/languages` | Länder-Icons für die vorhanden Sprachen | 
| `public/assets/images/locale` | Hier sind alle sprachabhängigen Icons | 
| `public/assets/images/logos` | Hier liegen alle Logos, die irgendwo in Stud.IP verwendet werden | 
| `public/assets/images/vendor` | Hier werden Grafiken abgelegt, die zu anderen Paketen, Frameworks etc gehören und nicht von uns erstellt wurden (wie z.B. jQuery-UI) |

### Gestaltung

Die Icon-Landschaft wurde deutlich entschlackt, moderner und effizienter gestaltet. Skalierbarkeit und eine einfache, klare Formensprache standen bei der Entwicklung im Mittelpunkt. Das neue Iconset ist funktional und selbsterklärend. So wird die Übersichtlichkeit der Funktionen und die intuitive Bedienung gefördert. Es gibt nun für alle Größen und Einsatzgebiete eine einheitliche Optik der neuen Icons. Die Verwendung von festen Grundformen zusammen mit förmlich und farblich abgegrenzten Zusätzen, die Funktionen und Zustände markieren, gewährleistet eine barrierearme Nutzbarkeit bei hohem Wiedererkennungseffekt.

Icons in Studip 2.0 sind nach einem festgelegten Raster gestaltet und minimalistisch in Farbe und Form. Sie sind grundsätzlich monochrom in festgelegten Farben gehalten. Linienstärke und Farbfüllungen werden so eingesetzt, dass die Icons ein einheitliches optisches Gewicht erhalten. Diese Regeln sollen künftig auf alle in Stud.IP verwendeten Icons übertragen werden.

Sicher gibt es zusätzlich zur normalen Projektentwicklung viele Plugins und Erweiterungen, bei denen einen Bedarf gibt, bestehende Icons anzupassen oder neue zu erstellen. Die dafür notwendigen Arbeiten übernimmt der Stud.IP e.V., der für die Entwicklung in Budget in gewissem Umfang bereithält. Koordiniert die Entwicklung über den Vorstand des Stud.IP e.V.

### Icon-Rollen

Ab der Version 3.4 werden Icons über die Icon-API angesprochen. Bisher wurden Icons wie Dateinamen eingebunden. Damit war die verwendete Farbe hartkodiert. Wenn in Stud.IP-Installationen Anpassungen an das Farbschema der Hochschule vorgenommen wurden, musste daher der Code geändert bzw. unschöne "Hacks" vorgenommen werden.

Ab Stud.IP v3.4 werden Icons nicht mehr mit ihrer Farbe referenziert, sondern mit der Rolle, die sie übernehmen wollen.
Ein Beispiel: Bisher wurden alle Icons, die als bzw. in einem Link angezeigt wurden, im Code mit der Farbe Blau hartkodiert: `Assets::img('icons/blue/seminar')` Wollte eine Hochschule alle Link-artigen Icons lieber in der Farbe
Grün darstellen, mussten dafür grüne Icons in das Verzeichnis "blue" gelegt werden (Was aber auch nicht immer funktioniert,
wenn z.B. die Hintergrundfarbe dann dieselbe wie die Icon-Farbe ist.) oder alle entsprechenden Vorkommnisse von `blue` im Code durch `green` ersetzen.

Mit der neuen Icon-API wird nun die Rolle hartkodiert. Die globale Zuordnung von Rollen zu Farben übernimmt dann die entsprechende Übersetzung.

### Rollen

Derzeit (Stud.IP v3.4) findet man die Zuordnung von Rollen zu Farben
in der Klasse "Icon" (`lib/classes/Icon.class.php`):

| Rolle | Farbe | Bedeutung |
| ---- | ---- | ---- |
| `Icon::ROLE_INFO`          | black | Alte Beschreibung: *Diese Icons werden ausschließlich in den Infoboxen verwendet und sind nie klickbar. Sie erläutern grafisch die Aktionen, die die Infobox anbietet. So können hier Aktionen mit dem zugehörigen Icon gezeigt werden, Informationen mit einem "I" illustriert werden oder Verweise auf andere Systembereiche mit den zu diesen Bereichen passenden Icons ergänzt werden.* | 
| `Icon::ROLE_CLICKABLE`     | %blue%blue | Alte Beschreibung: *Die blauen Icons sind der Standard für alle klickbaren Icons, unabhängig davon, in welchem Kontext sie verwendet werden (Ausnahme: Übersicht "Meine Veranstaltungen"). Insbesondere freistehende Aktionen sollten immer neben dem Linktext ein solches Icon zeigen. * | 
| `Icon::ROLE_ACCEPT`        | %green%green | Alte Beschreibung: *Grün kommt nur in dem Fall, dass der Bestätigungs-Haken gezeigt wird, zum Einsatz.* | 
| `Icon::ROLE_STATUS_GREEN`  | %green%green | Alte Beschreibung: *Grün kommt nur in dem Fall, dass der Bestätigungs-Haken gezeigt wird, zum Einsatz.* | 
| `Icon::ROLE_INACTIVE`      | %grey%grey | Alte Beschreibung: *Diese Variante kommt zum Einsatz, wenn Icons nicht klickbar sind und nicht innerhalb von Infoboxen eingesetzt werden. Sie drücken auch oft einen Status aus und werden für alle Objekte wie News, Votings oder Dateien verwendet, um ein solches Objekt zu klassifizieren. Eine Ausnahme bildet "Meine Veranstaltungen". Auch hier drücken die grauen Icons einen Zustand aus ("nur bekannte Objekte eines Types") aber können trotzdem geklickt werden, da von hier aus auch die jeweiligen Bereiche direkt angesprungen werden können. Dieser Sonderfall gilt jedoch nur für "Meine Veranstaltungen"* | 
| `Icon::ROLE_NAVIGATION`    | %ltblue%lightblue | | 
| `Icon::ROLE_NEW`           | red | Alte Beschreibung: *Die Farbe Rot dient dazu, Neues darzustellen oder hervorzuheben. Rote Icons kommen auf "Meine Veranstaltungen" zum Einsatz, wenn einer der Bereiche einer Veranstaltung für den Nutzer neue Inhalte führt. Aus Gründen der barrierearmen Bedienung reicht die rote Färbung allein nicht aus, es muss immer auch der Zusatz "neu" verwenden werden, um dem Icon auch eine eindeutige Form zu geben, es sei denn, die Kombination von Farbe und Form des Icons selbst ist eindeutig (etwa ein rotes X).* | 
| `Icon::ROLE_ATTENTION`     | red | Alte Beschreibung: *Die Farbe Rot dient dazu, Neues darzustellen oder hervorzuheben. Rote Icons kommen auf "Meine Veranstaltungen" zum Einsatz, wenn einer der Bereiche einer Veranstaltung für den Nutzer neue Inhalte führt. Aus Gründen der barrierearmen Bedienung reicht die rote Färbung allein nicht aus, es muss immer auch der Zusatz "neu" verwenden werden, um dem Icon auch eine eindeutige Form zu geben, es sei denn, die Kombination von Farbe und Form des Icons selbst ist eindeutig (etwa ein rotes X).* | 
| `Icon::ROLE_STATUS_RED`    | red | Alte Beschreibung: *Die Farbe Rot dient dazu, Neues darzustellen oder hervorzuheben. Rote Icons kommen auf "Meine Veranstaltungen" zum Einsatz, wenn einer der Bereiche einer Veranstaltung für den Nutzer neue Inhalte führt. Aus Gründen der barrierearmen Bedienung reicht die rote Färbung allein nicht aus, es muss immer auch der Zusatz "neu" verwenden werden, um dem Icon auch eine eindeutige Form zu geben, es sei denn, die Kombination von Farbe und Form des Icons selbst ist eindeutig (etwa ein rotes X).* | 
| `Icon::ROLE_INFO_ALT`      | %bgcolor=black white%white  | Alte Beschreibung: *Die weiße Variante wird immer dann verwendet, wenn Icons innerhalb einer dunklen Umgebung (in der Regel die Kopfzeile von frei schwebenden Fenstern mit dunkelblauer Zeile) vewendet werden. Auch graue Tabellenköpfe erhalten ggf. weiße Icons. In der Regel sind diese nicht klickbar. Das weiße Icon kann auf weißem Hintergrund nicht gesehen werden.* | 
| `Icon::ROLE_SORT`          | %bgcolor=black yellow%yellow | Alte Beschreibung: *Gelbe Icons werden ausschließlich zum Verschieben von Objekten benutzt. Daher existieren in diesem Set auch nur Dreiecke und Pfeile in allen Varianten.* | 
| `Icon::ROLE_STATUS_YELLOW` | %bgcolor=black yellow%yellow | Alte Beschreibung: *Gelbe Icons werden ausschließlich zum Verschieben von Objekten benutzt. Daher existieren in diesem Set auch nur Dreiecke und Pfeile in allen Varianten.* | 


### Zusätze

Es existieren eine Reihe von grafischen Zusätzen, die vielfältig eingesetzt werden können, um Aktionen, die hinter Icons liegen oder auch Zustände zu visualisieren. In der Regel werden Zusätze immer in rot dargestellt. Eine Ausnahme bildet der gelbe Zusatz Verschieben.

Zusätze an Icons werden ab Stud.IP v3.4 über die Icon-API referenziert. Möchte man zB ein `seminar`-Icon als Link mit dem Zusatz `add` (also Hinzufügen) einbauen: `Icon::create('seminar+add')`

| Status | Bild |Beschreibung
| ---- | ---- | ---- |
|  `accept`     | ![](https://develop.studip.de/studip/assets/images/icons/blue/accept/seminar.svg)  | **Akzeptieren**: Der Haken drückt aus, dass hier eine Bestätigung im Kontext des Objekts dargestellt wird. | 
|  `add`  | ![](https://develop.studip.de/studip/assets/images/icons/blue/add/seminar.svg) | **Hinzufügen**: Das Plus-Zeichen drückt aus, dass hier ein neues Objekt erzeugt werden kann. Das Anlegen eines Objektes oder der Sprung auf einen Bereich, in dem das Anlegen möglich ist, wird mit diesem Zusatz belegt. Er kann an blauen Icons mit Klick oder schwarzen und grauen Icons verwendet werden.| 
|  `decline`  | ![](http://develop.studip.de/studip/assets/images/icons/blue/icons/grey/decline/trash.svg) | **Aktion gesperrt**: Zuweilen werden Aktionen als "nicht möglich" dargestellt. In diesem Fall erhalten die Aktions-Icons ein X als Zusatz. | 
|  `edit`       | ![](https://develop.studip.de/studip/assets/images/icons/blue/edit/seminar.svg)  | **Bearbeiten**: Der Stift an einem Objekt drückt aus, das mit einem Klick auf dies Icon ein Objekt bearbeitet werden kann. | 
|  `export`     | ![](https://develop.studip.de/studip/assets/images/icons/blue/export/seminar.svg)  | **Exportieren**: Über dieses Icon werden ein oder mehrere Objekte des entsprechenden Types exportiert (z. B. als CSV-Datei)| 
|  `move_right`  | ![](https://develop.studip.de/studip/assets/images/icons/blue/move_right/mail.svg) | **Verschieben**: Um ein Objekt zu verschieben, gibt es den Zusatz eines Pfeils. Bis zur Version 2.4 waren diese Zusätze gelb, seit der Version 2.5 sind alle Zusätze in rot zu halten.
|  `new`  | ![](http://develop.studip.de/studip/assets/images/icons/blue/icons/red/new/seminar.svg) | **Neu**: Das Sternchen drückt einen neuen Inhalt aus. Das Sternchen wird (außer in der Kopfzeile) mit einem roten Icon kombiniert.| 
|  `remove`  | ![](https://develop.studip.de/studip/assets/images/icons/blue/remove/seminar.svg) | **Löschen/Entfernen**: Mit einem Minus wird die Möglichkeit, das entsprechende Objekt zu löschen, markiert.| 
|  `visibility-visible` | ![](https://develop.studip.de/studip/assets/images/icons/blue/visibility-visible/seminar.svg)  | **Sichtbar**: Ein Objekt wird bei Klick auf dieses Icon sichtbar geschaltet. | 
|  `visibility-invisible` | ![](https://develop.studip.de/studip/assets/images/icons/blue/visibility-invisible/seminar.svg)  | **Unsichtbar**: Ein Objekt wird bei Klick auf dieses Icon unsichtbar geschaltet.  | 


Das Icon-Set enthält im Bausatz noch weitere Zusätze, die aktuell jedoch nicht verwendet werden. So finden sich Pfeile in alle vier Richtungen, Pause-Zeichen, Bestätigen-Zeichen (als Gegenstück zum "X"), Minus-Zeichen (als Gegenstück zum Hinzufügen) und Aktualisieren.

### Größen

Icon-Größen können über die Render-Methoden der Icon-API angegeben werden. Inzwischen werden Icons als frei skalierbare SVG ausgeliefert.
Seit der Version 5.0 wird die Größe im Icon nicht mehr mitgegeben (vormals 16 * 16 Pixel, außer anders angegeben).

### Verzeichnisstruktur

War früher die modulare Verzeichnisstruktur wichtig, verbirgt die Icon-Klasse nun diese Implementationsdetails. Bei Verwendung der Icon-API kommt man damit nicht mehr in Berührung.

Historisch lagen die Icons in etwa dieser Verzeichnisstruktur:
`icons/<Größe>/<Farbe>/<Zuatz>/<Name des Icons>.png`

### Liste der verfügbaren Icons

#### Stammicons

Für alle in Stud.IP vorhandenen Objekte existieren Stammicons, von denen alle weiteren Formen oder Varianten logisch abgeleitet werden. Gegenwärtig existieren folgende Stammicons:

(In vielen Fällen existieren sowohl gefüllte und transparente bzw. invertierte Varianten zu einem Stammicon. Hier ist in der Regel die normale Version und nicht die Alternative einzusetzen.)

| Bild | Lizenz | Beschreibung
| ---- | ---- | ---- |
| ![](https://develop.studip.de/studip/assets/images/icons/blue/60a.svg)               | 60a               | **Lizenz nach §60a** Dokument-Weitergabe im Rahmen der §60a Lizenz (aktuell)| 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/cc.svg)               | CC               | **Lizenz nach CC** Dokument-Weitergabe im Rahmen von CC | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/license.svg)               | license               | **Lizenz allgemein**  | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/own-license.svg)               | own-license               | **Eigene Lizenz** Dokument-Weitergabe im Rahmen einer eigenen Lizenz/selbst erstellt | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/public-domain.svg)               | public-domain              | **Freie Lizenz** Dokument-Weitergabe im Rahmen einer freien Lizenz| 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/accept.svg)               | accept               | **Akzeptieren/akzeptiert** Dieses Symbol ist die Grundform für positive Rückmeldungen an den Nutzer. | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/action.svg)               | action              | **Aktionsmenu** Icon zur Initiierung bzw. Verankerung des Aktionsmenu| 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/activity.svg)             | activity             | **Activity-Stream** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/no-activity.svg)          | no-activity          | **keine Aktivität im Activity-Stream** leerer Activity-Stream | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/add.svg)                  | add                  | **Hinzufügen** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/add-circle.svg)           | add-circle           | **Hinzufügen** für Popover | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/admin.svg)                | admin                | **Administration** Alle Administrationen des Systems | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/archive.svg)              | archive              | **Archiv** für alles, was mit dem Archivieren zu tun hat | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/archive2.svg)             | archive2             | **Archiv Alternative** Alternative, die auch für Ordner o.ä. verwendet werden kann | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/archive3.svg)             | archive3             | **Archiv Alternative** Alternative, die auch für Ordner o.ä. verwendet werden kann | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/assessment.svg)           | assessment           | **Prüfungen/Asssessments** allgemeines Icon für Prüfungen | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/audio.svg)           | audio           | **Audio-Element** allgemeines Icon für Audio-Inhalt | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/audio3.svg)           | audio3           | **Audio-Element** allgemeines Icon für Audio-Inhalt, Variante für Audio-Medienobjekt | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/billboard.svg)            | billboard            | **Schwarzes Brett** Icon für schwarze Bretter in Stud.IP | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/block-accordion.svg)           | block-accordion           | **Block-Icon für Akkordeon** Icon für den Courseware-Block Akkorden| 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/block-canvas.svg)           | block-canvas           | **Block-Icon für Canvas** Icon für den Courseware-Block Canvas| 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/block-comparison.svg)           | block-comparison           | **Block-Icon für Comparison** Icon für den Courseware-Block Bildvergleich| 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/block-eyecatcher.svg)           | block-eyecatcher           | **Block-Icon für Eye-catcher** Icon für den Courseware-Block Blickfang| 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/block-eyecatcher2.svg)           | block-eyecatcher2           | **Block-Icon für Eye-catcher** Alternativ-Icon für den Courseware-Block Blickfang| 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/block-gallery.svg)           | block-gallery           | **Block-Icon für Galerie** Icon für den Courseware-Block Bildergalerie| 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/block-gallery2.svg)           | block-gallery           | **Block-Icon für Galerie** Alternativ-Icon für den Courseware-Block Bildergalerie| 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/block-imagemap.svg)           | block-imagemap           | **Block-Icon für Imagemap** Icon für den Courseware-Block Imagemap| 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/block-imagemap2.svg)           | block-imagemap2           | **Block-Icon für Canvas** Alternativ-Icon für den Courseware-Block Imagemap | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/block-tabs.svg)           | block-tabs           | **Block-Icon für Tabs** Icon für den Courseware-Block Tabs | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/block-typewriter.svg)           | block-tabs           | **Block-Icon für Schreibmaschinen** Icon für den Courseware-Block Schreibmaschine | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/blubber.svg)              | blubber              | **Blubber** Icon für die Blubber-Funktion | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/brainstorm.svg)              | brainstorm              | **Brainstorm** Icon für das Brainstorm-Plugin | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/bus.svg)                  | bus                  | **Bus** Icon für Navigationsfunktionen, zB. in Campus-Apps | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/campusnavi.svg)           | campusnavi           | **Campus-Navi** Icon für Navigationsfunktionen allgemein, zb. in Campus-Apps | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/category.svg)             | category             | **Kategorie** allgemeines Icon für Kategorien | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/cellphone.svg)             | cellphone             | **Telefon/Handy** Telefonnummer, Smartphone usw. | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/chat.svg)                 | chat                 | **Chat** Chat/Forum/Messenger) | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/check-circle.svg)         | check-circle         | **Akzeptieren/akzeptiert** für Popover | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/checkbox-checked.svg)     | checkbox-checked     | **markierte Checkbox** Checkbox in Form eines Icons, markiert | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/checkbox-unchecked.svg)   | checkbox-unchecked   | **unmarkierte Checkbox** Checkbox in Form eines Icons, unmarkiert | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/checkbox-indeterminate.svg)   | checkbox-indeterminate   | **uneindeutige Checkbox** Checkbox in Form eines Icons, uneindeutig (für Mehrfachselektion) | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/radiobutton-checked.svg)  | radiobutton-checked  | **markierter Radiobutton** Radiobutton in Form eines Icons, markiert | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/radiobutton-unchecked.svg) | radiobutton-unchecked| **unmarkierter Radiobuttom** Radiobutton in Form eines Icons, unmarkiert | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/classbook.svg)            | classbook            | **Klassenbuch** Klassenbuch | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/clipboard.svg)            | clipboard            | **Zwischenablage** Kopieren in Zwischenablage | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/cloud.svg)                 | cloud                 | **Cloud-Service** generisches Icon für Cloudservices | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/code.svg)                 | code                 | **Programmcode** generisches Icon für Programmcode | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/code-qr.svg)              | code-qr              | **QR-Code** QR-Code | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/computer.svg)             | computer             | **Computer** allgemeines Computer-Icon | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/comment.svg)              | comment              | **Kommentar** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/comment2.svg)              | comment2              | **Kommentar** Alternative für Kommentare| 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/community.svg)            | community            | **Community** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/computer.svg)            | computer            | **Computer** allgemeines Icon für Computer (analog zu Telefon/Smartphone)| 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/consultation.svg)         | consultation         | **Sprechstunden** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/content2.svg)         | content         | **Inhalte** allgemeines Icon für Inhalte| 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/courseware.svg)         | courseware         | **Basis-Icon Courseware** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/crop.svg)         | crop         | **Beschneiden** Beschneiden von Bildern (zB. Avatar-Bilder) | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/crown.svg)                | crown                | **Krone** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/date.svg)                 | date                 | **Termin** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/date-single.svg)                 | date-single                 | **Einzeltermin** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/date-cycle.svg)                 | date-cycle                 | **regelmäßiger Termin** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/date-block.svg)                 | date-block                 | **Blocktermin** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/decline.svg)              | decline              | **ablehnen** Dieses Symbol ist die Grundform für negative Rückmeldungen an den Nutzer. | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/decline-circle.svg)              | decline-circle              | **ablehnen** Ablehnen-Variante im Kreis| 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/dialog-cards.svg)         | dialog-cards         | **Visitenkarten** Icon für Visitenkarten/Adressen | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/doctoral-cap.svg)         | doctoral-cap         | **Prüfungen/Abschlüsse** allgemeines Icon für Prüfungen | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/doit.svg)                 | doit                 | **Do.IT**Do.IT-Plugin und andere aufgabenbezogene Funktionen | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/door-enter.svg)           | door-enter           | **Login/Betreten** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/door-leave.svg)           | door-leave           | **Logout/Verlassen** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/download.svg)             | download             | **Download** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/dropbox.svg)             | dropbox             | **Cloud-Service Dropbox**Alternative | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/edit.svg)                 | edit                 | **Editieren** allgemeines Editieren-Icon | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/elmo.svg)                 | elmo                 | **Elmo** Icon für Elmo-Plugin | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/eportfolio.svg)           | eportfolio           | **ePortfolio-Icon**  | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/euro.svg)                 | euro                 | **Euro** Währungszeichen/Geld | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/evaluation.svg)           | evaluation           | **Evaluation** generelles Icon für Evaluationen | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/exclaim.svg)              | exclaim              | **Hinweis** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/exclaim-circle.svg)       | exclaim-circle       | **Hinweis** für Popover | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/export.svg)               | export               | **Export** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/facebook.svg)             | facebook             | **Facebook** Facebook-Anbindung oder Verknüpfung * | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/favorite.svg)             | favorite             | **Favorit/Like** Favoriten-Icon * | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/file.svg)                 | file                 | **Dokument** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/files.svg)                | files                | **Dokumente/Dateibereich** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/file-archive.svg)         | file-archive         | **Zip-Datei** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/file-audio.svg)           | file-audio           | **Audio-Datei** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/file-audio2.svg)           | file-audio2           | **Audio-Datei** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/file-sound.svg)           | file-sound           | **Audio-Datei** Alternative, zB. für laute Audiodateien| 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/file-pic.svg)             | file-pic             | **Bilddatei** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/file-pic2.svg)             | file-pic2             | **Bilddatei** Alternative| 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/file-pdf.svg)             | file-pdf             | **PDF-Datei** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/file-presentation.svg)             | file-presentation             | **Präsentation-Datei** generische Variante, ohne Power Point-Bezug
| ![](https://develop.studip.de/studip/assets/images/icons/blue/file-spreadsheet.svg)             | file-spreadsheet             | **Tabellenkalkulation'-Datei** generische Variante, ohne Excel-int-Bezug
| ![](https://develop.studip.de/studip/assets/images/icons/blue/file-office.svg)          | file-office          | **Office-Dokument** Word/PowerPoint/Excel | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/file-excel.svg)          | file-excel          | **Excel-Dokument**  | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/file-video.svg)             | file-video             | **Video-Datei** |  
| ![](https://develop.studip.de/studip/assets/images/icons/blue/file-video2.svg)             | file-video2             | **Video-Datei** Alternative |  
| ![](https://develop.studip.de/studip/assets/images/icons/blue/file-word.svg)          | file-word          | **Word-Dokument**  | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/file-ppt.svg)          | file-ppt          | **PPT-Dokument**  | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/file-text.svg)            | file-text            | **Textdatei** (zB. Word) | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/file-generic.svg)         | file-generic         | **generischer Dateityp** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/filter.svg)         | filter         | **Suchfilter, Ansichtsfilter** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/filter2.svg)         | filter         | **Suchfilter, Ansichtsfilter** Alternative| 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/fishbowl.svg)         | fishbowl         | **Goldfisch im Glas** ungenutzt| 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/folder-broken.svg)         | folder-broken         | **nicht erreichbarer Ordner** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/folder-date-full.svg)         | folder-date-full         | **gefüllter Terminordner** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/folder-date-empty.svg)         | folder-date-empty       | **leerer Terminordner** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/folder-edit-empty.svg)         | folder-edit-empty         | **leerer editierbarer Ordner** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/folder-edit-full.svg)         | folder-edit-full         | **voller editierbarer Ordner** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/folder-empty.svg)         | folder-empty         | **leerer Ordner** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/folder-full.svg)          | folder-full          | **gefüllter Ordner** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/folder-group-empty.svg)          | folder-group-empty         | **leerer Gruppenordner**| 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/folder-group-full.svg)          | folder-group-full         | **gefüllter Gruppenordner**| 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/folder-home-empty.svg)          | folder-home-empty          | **leerer Home-Ordner**| 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/folder-home-full.svg)          | folder-home-full          | **gefüllter Home-Ordne**| 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/folder-inbox-full.svg)          | folder-inbox-full      | **gefüllter Inbox-Ordner** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/folder-inbox-empty.svg)          | folder-inbox-empty          | **leerer Inbox- Ordner**| 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/folder-lock-full.svg)          | folder-lock-full         | **gefüllter geschützter Ordner** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/folder-lock-empty.svg)          | folder-lock-empty          | **leerer geschützter Ordner** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/folder-parent.svg)        | folder-parent        | **übergeordneter Ordner** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/folder-plugin-market-empty.svg)        | folder-plugin-market-empty.svg)         | **Ordner Plugin-Marktplatz leer** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/folder-plugin-market-full.svg)        | folder-plugin-market-full.svg)         | **Ordner Plugin-Marktplatz gefüllt** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/folder-public-empty.svg)        | folder-public-empty.svg)         | **öffentlicher Ordner, leer** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/folder-public-full.svg)        | folder-public-full.svg)         | **öffentlicher Ordner, gefüllt** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/folder-topic-empty.svg)        | folder-topic-empty       | **leerer Themenordner** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/folder-topic-full.svg)        | folder-topic-full        | **gefüllter Themenordner** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/forum.svg)                | forum                | **Forum** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/fullscreen-on.svg)                | fullscreen-on                | **Vollbild ein** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/fullscreen-off.svg)                | fullscreen-off              | **Vollbild aus** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/globe.svg)                | globe                | **Globus/Weltkarte** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/glossary.svg)                | glossary                | **Glossar** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/graph.svg)                | graph                | **Graph/Auswertung** generelles Icon für grafische Auswertungen (zB. Evalautionsauswertung) | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/group.svg)                | group                | **Permalink** neu, ehemals Gruppieren | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/group2.svg)               | group2               | **Gruppe/gruppieren** Gruppen (Menschen) | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/group3.svg)               | group3               | **Gruppe/Hierarchie** Gruppen/Hierarchie | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/group4.svg)               | group4               | **Gruppe/gruppieren** Gruppieren nach Farbe | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/guestbook.svg)            | guestbook            | **Gästebuch** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/hamburger.svg)                 | hamburger                 | **Hamburger-Menu** für mobile Ansicht| 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/home.svg)                 | home                 | **Startseite** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/info.svg)                 | info                 | **Information** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/info-circle.svg)          | info-circle          | **Information** für Popover | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/infopage.svg)             | infopage             | **Freie Infoseite** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/inbox.svg)                | inbox                | **Nachrichten Eingang** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/outbox.svg)               | outbox               | **Nachrichten Ausgang** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/install.svg)              | install              | **Plugin Installation** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/institute.svg)            | institute            | **Einrichtung** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/item.svg)                | item                | **Allgemeines Objekt für Kommentare** Kommentarobjekt | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/key.svg)                  | key                  | **Password** Password(-verwaltung) | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/knife.svg)                  | knife                  | **Taschenmesser/Tool** alternative für Tool-Icon | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/learnmodule.svg)          | learnmodule          | **Lernmodul** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/lightbulb.svg)          | lightbulb          | **Glühbirne** etwa für Tipps, Ideen oder Brainstorming| 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/link-extern.svg)          | link-extern          | **externer Link** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/link-intern.svg)          | link-intern          | **interner Link** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/link2.svg)         | link2         | **externer Link** Alternative, rechts-orientierte Seiten| 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/link3.svg)         | link3          | **externer Link** Alternative, links-orientierte Seiten| 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/literature-request.svg)           | literature-request           | **Literaturanfrage** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/literature.svg)           | literature           | **Literatur** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/lock-locked.svg)          | lock-locked          | **Schloss im geschlossenen Zustand** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/lock-unlocked.svg)        | lock-unlocked        | **Sperren/Schloss im geöffneten Zustand** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/log.svg)                  | log                  | **Protokoll/Log** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/mail.svg)                 | mail                 | **Nachricht** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/maximize.svg)                 | maximize                 | **Maximieren** für Widgetsystem| 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/medal.svg)                | medal                | **Prüfungen/erreichte Leistungen** allgemeines Icon für Prüfungen | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/mensa.svg)                | mensa                | **Mensa** Mensa, vegetarisch | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/mensa2.svg)               | mensa2               | **Mensa** Mensa | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/metro.svg)                | metro                | **U-Bahn, Bahn** zB. für Campus-App | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/microphone.svg)                | microphone                | **Mikrofon** zB. für Medien | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/module.svg)               | module               | **Modul** in Abgrezung zu Lernmodul oder Plugin | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/money.svg)                | money                | **Mensa** Bezahlvorgänge, Kostenpflichtiges | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/network.svg)                 | network                 | **Netzwerk** ungenutzt | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/news.svg)                 | news                 | **Ankündigungen** Ankündigungen | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/notification.svg)        | notification        | **Notifikation** Notifikation | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/notification2.svg)        | notification2        | **Notifikation** Notifikation, Alternative | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/outer-space.svg)        | outer-space        | **Planet/Weltall** ungenutztes Icon, frei zur Nutzung | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/oer-campus.svg)        | oer-campus        | **OER-Campus** Basisicon für den OER-Campus| 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/opencast.svg)        | opencast        | **Opencast** Icon für das Opencast-Plugin| 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/perle.svg)                | perle                | **Perle** Icon für Perle Plugin | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/permalink.svg)                | permalink                | **Permalink** Icon zum Abrufen/Verlinken eines Permalinks | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/person.svg)               | person               | **Person/Profil** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/persons.svg)              | persons              | **Personen** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/person-online.svg)        | person-online        | **Person online** Person ist online | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/picture.svg)               | picture               | **Bild** allgemeines Icon für Bilder, zB. in Courseware| 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/phone.svg)                | phone                | **Telefon** klassisches Telefon, abgegrenzt von Handy| 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/place.svg)                | place                | **Ort** Ort/Geoinformation/Place | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/plugin.svg)                | plugin                | **Plugin** Allgemeines Icon für Plugins | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/powerfolder.svg)                | powerfolder                | **Clound-Dienst Powerfolder**  | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/print.svg)                | print                | **Drucken** Druckfunktionen, Druckansicht | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/privacy.svg)              | privacy              | **Privatsphäreneinstellungen** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/remove.svg)               | remove               | **Entfernen** Entfernen, auch im Sinne von Verringen (korrespondiert mit dem Entfernen-Zusatz) | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/add.svg)                  | add                  | **Hinzufügen** Hinzufügen, auch im Sinne von Erhöhen (korrespondiert mit dem Hinzufügen-Zusatz) | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/question.svg)             | question             | **Frage** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/question-circle.svg)      | question-circle      | **Frage** für Popover | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/ranking.svg)              | ranking              | **Ranking**  | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/radar.svg)              | radar.              | **Radar**  | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/refresh.svg)              | refresh              | **aktualisieren** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/resources.svg)            | resources            | **Ressource/Ressourcenverwaltung** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/resources-broken.svg)            | resources-broken            | **kaputte Ressource** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/resource-label.svg)            | resource-label            | **Ressourcen-Label** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/rescue.svg)               | rescue               | **Support/Hilfe Alternative** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/roles.svg)                | roles                | **Rollen** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/roles2.svg)               | roles2               | **Alternative für Rollen/Rechte** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/rotate-left.svg)               | rotate-left               | **Bildbearbeitung drehen links** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/rotate-right.svg)               | rotate-right               | **Bildbearbeitung drehen rechts** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/room.svg)           | room          | **Basisicon für Räume** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/room2.svg)           | room2          | **Basisicon für Räume**  Alternative | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/room-clear.svg)           | room-clear           | **Raum frei** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/room-occupied.svg)        | room-occupied        | **Raum belegt** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/room-request.svg)         | room-request         | **Raum anfrage** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/remove.svg)           | remove          | **Entfernen** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/remove-circle.svg)           | remove-circle          | **Entfernen** für Popover | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/rotate-left.svg)           | rotate-left          | **Drehen gegen den Uhrzeigersinn** für Bildbearbeitung | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/rotate-right.svg)           | rotate-right          | **Drehen im Uhrzeigersinn** für Bildbearbeitung | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/rss.svg)                  | rss                  | **RSS-Feed** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/schedule.svg)             | schedule             | **Kalender/Ablaufplan** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/settings.svg)             | settings           | **Einstellungen**  | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/settings2.svg)             | settings2            | **Einstellungen** Alternative für Einstellungen | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/share.svg)             | share            | **Teilen/Exportieren** allgemeines Icon für das Teilen von Objekten | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/search.svg)               | search               | **Suche** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/seminar.svg)              | seminar              | **Veranstaltung** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/seminar-archive.svg)      | seminar-archive      | **Veranstaltungsarchiv** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/smiley.svg)              | smiley              | **Smiley/Emoji** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/skype.svg)                | skype                | **Skype** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/span-empty.svg)               | span-empty               | **Füllstand/Progress: 0%** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/span-1quarter.svg)               | span-1quarter              | **Füllstand/Progress: 25%** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/span-2quarter.svg)               | span-2quarter               | **Füllstand/Progress: 50%** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/span-3quarter.svg)               | span-3quarter               | **Füllstand/Progress: 75%** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/span-1third.svg)               | span-1third               | **Füllstand/Progress: 33%** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/span-2third.svg)               | span-2third               | **Füllstand/Progress: 66%** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/span-full.svg)               | span-full               | **Füllstand/Progress: 100%** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/spiral.svg)                | spiral                | **Spirale** ungenutzt | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/sport.svg)                | sport                | **Sport** zB. für Campus-App | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/smiley.svg)               | smiley               | **Smiley** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/staple.svg)               | staple               | **Anhang** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/star.svg)                 | star                 | **Bewertungsstern** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/stat.svg)                 | stat                 | **Statistiken** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/studygroup.svg)           | studygroup           | **Studiengruppe** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/support.svg)              | support              | **Support** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/table-of-contents.svg)                  | table-of-contents                  | **Inhaltsverzeichnis**  | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/tag.svg)                  | tag                  | **Tag** Tags an Systemobjekten | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/tan.svg)                  | tan                  | **TAN** Vergabe, Nutzung von TANs, Prüfungen | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/tan2.svg)                  | tan2                  | **TAN** Vergabe, Nutzung von TANs, Prüfungen, Alternative | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/test.svg)                 | test                 | **Test** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/timetable.svg)            | timetable            | **Timetable** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/tools.svg)                | tools                | **Tools** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/topic.svg)                | topic                | **Thema** für Themen in Veranstaltungen | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/trash.svg)                | trash                | **Mülleimer/löschen** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/twitter.svg)              | twitter              | **Twitter** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/twitter2.svg)             | twitter2             | **Alternative Twitter** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/twitter3.svg)             | twitter3             | **Alternative Twitter** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/ufo.svg)               | ufo               | **Ufo** gibt es sie wirklich?| 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/unit-test.svg)            | unit-test            | **Unit-Tests** Unit-Tests | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/upload.svg)               | upload               | **Upload** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/vcard.svg)                | vcard                | **vCard/Visitenkarte** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/video.svg)                | video                | **Video** Video | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/video2.svg)                | video2                | **Video** Video/Film | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/view-list.svg)                | view-list                | **Umschalter Liste/Kacheln Liste** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/view-wall.svg)                | view-wall                | **Umschalter Liste/Kacheln Kacheln/Wall** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/visibility-checked.svg)   | visibility-checked   | **Sichtbarkeit an** Umschalter Sichtbarkeit: an | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/visibility-visible.svg)   | visibility-visible   | **sichtbar/Sichtbarkeit an** Objekt ist sichtbar oder Umschalter Sichtbarkeit: aus | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/visibility-invisible.svg) | visibility-invisible | **unsichtbar** Objekt ist unsichtbar | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/vote.svg)                 | vote                 | **Umfrage** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/vote-stopped.svg)         | vote-stopped         | **angehaltene Umfrage** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/wiki.svg)                 | wiki                 | **Wiki** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/wizard.svg)                 | wizard                 | **Assistent** Icon für Assistenten | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/youtube.svg)              | youtube              | **Youtube** | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/zoom-in.svg)              | zoom-in              | **Zoom in** Zoomen für Bildupload | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/zoom-in2.svg)              | zoom-in2              | **Zoom in** Zoomen für Bildupload, Alternative | 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/zoom-out.svg)              | zoom-out          | **Zoom out**  Zoomen für Bildupload| 
| ![](https://develop.studip.de/studip/assets/images/icons/blue/zoom-out2.svg)              | zoom-out2             | **Zoom out**Zoomen für Bildupload, Alternative | 


#### Play Pause und Stop

![](https://develop.studip.de/studip/assets/images/icons/blue/play.svg)

![](https://develop.studip.de/studip/assets/images/icons/blue/stop.svg)

![](https://develop.studip.de/studip/assets/images/icons/blue/pause.svg)


#### Listen und Pfeile
![](https://develop.studip.de/studip/assets/images/icons/blue/arr_1down.svg)

![](https://develop.studip.de/studip/assets/images/icons/blue/arr_1left.svg)

![](https://develop.studip.de/studip/assets/images/icons/blue/arr_1right.svg)

![](https://develop.studip.de/studip/assets/images/icons/blue/arr_1up.svg) Pfeile, Blätterfunktion (zB. Seite weiter, vor/zurück)\\

![](https://develop.studip.de/studip/assets/images/icons/blue/arr_2down.svg)

![](https://develop.studip.de/studip/assets/images/icons/blue/arr_2left.svg)

![](https://develop.studip.de/studip/assets/images/icons/blue/arr_2right.svg)

![](https://develop.studip.de/studip/assets/images/icons/blue/arr_2up.svg) Pfeile zum Verschieben (zB. Objekt eintragen, Vertauschen von Objekten use.)\\

![](https://develop.studip.de/studip/assets/images/icons/blue/arr_eol-down.svg)

![](https://develop.studip.de/studip/assets/images/icons/blue/arr_eol-left.svg)

![](https://develop.studip.de/studip/assets/images/icons/blue/arr_eol-right.svg)

![](https://develop.studip.de/studip/assets/images/icons/blue/arr_eol-up.svg) Pfeile zum Springen an das Ende einer Liste (Springen an das Ende einer Tabelle, finer Liste von Objekten usw.)
