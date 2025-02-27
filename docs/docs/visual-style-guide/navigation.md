---
title: Navigation
sidebar_label: Navigation
---

Die Navigation in Stud.IP ist in mehreren Ebenen organisiert. Es ist zu unterscheiden in:

* Hauptnavigation: Die Kopfzeile des Systems. Von hier aus werden komplette Funktionsbereiche erschlossen. Jeder
  dieser Bereiche entspricht einem der Hauptpunkte in der Sitemap und jeder dieser Bereiche präsentiert sich mit einem eigenen Reitersystem.
* Scopes: Jeder Hauptbereich (zB. Profil oder Community) bringt einen Scope mit, der die Funktionen eines Bereiches
  aufnimmt. Ein Scope entspricht jeweils der zweiten Ebene in der Sitemap. Eine Funktion darf nur an einer einzigen
  Stelle in einem Scope eingehangen werden. Somit hat jede Funktion eine eindeutige Zuordnung zu einem der Hauptbereiche.
* Navigation in der Sidebar: Verschiedene Aufgaben innerhalb einer Funktion finden sich im Navigationsbereich der
  Sidebar. Diese führen an dieser Stelle zu einem neuen Seitenaufruf (im Gegensatz zu Aktionen in der Sidebar).
* Aus der Navigation der Sidebar sind auch Links in andere Hauptbereiche möglich, sollten jedoch vermieden werden.
  Im Idealfall bleibt auch die Navigation einer Funktion innerhalb ihrer eigenen Aufgaben bzw. innerhalb des
  jeweiligen Scopes. Ein Eintrag in der Navigation der Sidebar entspricht der dritten Ebene in der Navigation.

Weitere Hinweise zum Aufbau der Sidebar und ihrer unterschiedlichen Widgets findet sich im entsprechenden [Bereich
des Styleguides](#Sidebar)

## Kopfzeile

Die Kopfzeile leitet jede Seite ein und bietet Zugriff auf alle Kernbestandteile von Stud.IP:

Mini:kopfzeile.png

Je nach Rechtestufe des angemeldeten Nutzers und eingerichteten Systemplugins werden unterschiedliche Systembereiche von hier aus zugänglich gemacht.

Die Kopfzeile sieht den größten Gestaltungsspielraum für Anpassungen an die Corporate Identity des Betreibers vor.

Folgende Anpassungen sind hier möglich:
* Einfügen des eigenen Logos an beliebiger Position (Vorschlag: Rechts neben dem Stud.IP Logo)
* Einfügen weiterer eigener Links in der Kopfzeile (Vorschlag: links neben der globalen Suchen)

Noch einige Hinweise zur Eigenanpassung der Kopfzeile:
* Entfernen Sie nicht die Icons aus der Kopfzeile, da die Icons ihre Gestaltung innerhalb des Systems wiederholt
  auftauchen und damit eine Verbindung zu dieser Navigation schaffen
* Entfernen Sie nicht die Beschriftung der Icons, da die Nutzer über diese Beschriftung wichtige Erklärungen erhalten
  und der Text auch in anderen Systemsprachen zur Verfügung steht.
* Ändern Sie nicht die Reihenfolge der Icons oder teilen Sie die Icons in mehrere Zeilen auf.
* Ordnen Sie Kopfzeile nicht an andere Stellen (etwa als Seitenleiste) an. Das Stud.IP-System benötigt an einigen
  Stellen teilweise eine sehr breite Darstellung. Die Kopfzeile ist in dieser Form am besten auf das System abgestimmt.

## Reite (Scopes)
Scopes fassen die Funktion eines Hauptbereiches (etwa alle Funktionen innerhalb einer Veranstaltung oder innerhalb des Nachrichtensystems) zusammen.

Mini:style_reiter.jpg

Stud.IP ergänzt in einem Scope (ebenso wie in der Hauptnavigation) automatisch einen "Überlauf", der in einem
Drop-Down-Menü alle Icons aufnimmt, die nicht mehr in die horizontale Darstellung (je nach Bildschirmbreite) passen
würde. Grundsätzlich sollte beim Entwerfen neuer Funktionen darauf achten, möglichst knappe Bezeichnungen zu wählen,
sodass möglichst viele Funktionen nebeneinander Platz finden. Die Breite der jeweiligen Beschriftungen bedingt die Breite des Scopes!

## Sidebar

### Vorbemerkung

Das Konzept der Infoboxen (Stud.IP-Versionen bis 3.0) hat sich grundlegend geändert zum Sidebar-Konzept (ab Stud.IP
3.1), das viele der Funktionen aus den alten Infoboxen aufnimmt, jedoch nicht direkt ersetzt. In Rahmen dieser
Umstellung wurde die 3. Navigationsebene als Zeile unterhalb der Reiter in ein Navigationswidget der Sidebar verlegt.

Attach:Style/Sidebar-dafault.jpg

### Kurzbeschreibung
Die Sidebar befindet sich an fester Position am linken Rand einer Stud.IP-Seite. Die Sidebar ersetzt die Infobox älterer Stud.IP-Versionen und enthält mindestens eins, meistens mehrere Widgets. In der Sidebar befinden sich innerhalb von diesen Widgets die Elemente der 3. Navigationsebene, Aktionen, Ansichtsoptionen, seiteninterne Suchmöglichkeiten und Exportfunktionen. Sofern diese Standardwidgets nicht passend sind, kann eine Seite weitere Widgets haben.
Die Sidebar besitzt zudem Orientierungsbild im Kopfbereich, das den Namen der Seite enthält, das Baisisicon des jeweiligen Bereiches zeigt und einen Avatar aufnehmen kann.
Jede Seite sollte eine Sidebar besitzen.

### Aufbau & Elemente

#### Orientierungsbild
Das Orientierungsbild ist 520px breit und 200px hoch. Zu allen Basisfunktionen (bzw. aufbauend auf deren Icons) werden entsprechende Orientierungsbilder ausgeliefert. Grundsätzlich können Standorte diese Bilder tauschen, sollten aber darauf achten, dass Bildinhalt und Helligkeit zum umgebenden Design passen. Im Zweifel steht die Stud.IP-GUI-Gruppe bereit, weitere Bilder zu erstellen oder Tipps zu geben, wie man eigenen Bilder integrieren kann.

#### Typen von Widgets
| Typ | Beschreibung |
| ---- | ---- |
| Navigation | Enthält automatisch die 3. Navigationsbene entsprechend der Stud.IP-Navigationsstruktur (ehemals 3. Navigationsebene unterhalb der Reiterleiste). Navigationspunkte springen auf andere Seiten aber bleiben idealerweise innerhalb eines Navigationskontextes (=Reitersystem). Die aktuell gewählte Seite wird mit einem blauen Pfeil markiert. Navigationspunkte zeigen keine Icons. |
| Aktionen | Enthält Aktionen, die den Inhalt der aktuellen Seite beeinflussen. Aktionen öffnen grundsätzlich einen Dialog und verlassen somit nicht den aktuellen View, den der Nutzer sieht. |
| Ansichten | Diese enthalten Ansichtsoptionen bzw. Filter, die den angezeigten Content auf der jeweiligen Seite einschränken. Die jeweils gewählte Ansicht bzw. der Filter ist mit einem gelben Pfeil markiert. |
| Suche | Ein Such-Widget ist seitenspezifisch, ermöglicht also das Suchen innerhalb des Contents der Seite. Idealerweise gilt, dass eine Suche hier nur innerhalb des Contents filtert, den ich auf dieser Seite insgesamt sehen kann bzw. erreichen kann. Wenn der Content einer Seite selbst ein Suchergebnis liefert (z.B. bei allen Suchfunktionen in Stud.IP) muss diese Suche außerhalb der Sidebar, z.B. in einer Content-Box im Content-Bereich der Seite realisiert werden. Ein Suchwidget könnte dann theoretisch den gefunden Content dynamisch Einschränken, idealerweise ohne Reload der Seite |
| Export | Hier werden alle Funktionen aufgenommen, die konkret eine Datei (z.B. PDF, XLS-Export, CSV-Datei) zum Download anbieten. |

Grundsätzlich beginnen Seiten mit der Navigation und den Aktionen, dann folgende weitere Widgets (in der Regel Suche, Ansichten oder Export). Die weitere Widgets können je nach Nutzungshäufigkeit der jeweiligen Seite platziert werden, die ersten beiden Positionen sind in der Reihenfolge fest vorgegeben.


#### Weitere Typen von Widgets

Gelegentlich tauchen folgende Type auf:

| Type | Beschreibung |
| ---- | ---- |
| Einstellungen | Für Einstellungen, die sich direkt auf die Seite auswirken und schnell in der Sidebar vorgenommen werden sollen |
| Merkliste | Für das Zwischenspeichern von beliebigen Objekten |


### Was nicht in die Sidebar gehört

* Hilfetexte: Bisher oft in der Infobox verwendet, gehören erklärende oder einleitende Texte über die Funktion einer Seite nicht mehr in die Sidebar. Der beste Platz dafür ist die in der Version 3.1 neu geschaffene Hilfe-Lasche, in der auch Touren gestartet werden und der Link zum Hilfe-Wiki zu finden ist.
* Formulare: Mit Ausnahme eines Eingabefeldes für das Such-Widget gehören Formulare nicht in die Sidebar.

### Sonst noch zu beachten

* Für die Sidebar gibt es eine feststehende API, die für die Erstellung verwendet werden muss.
* Die Umstellung des Admin-Bereiches erfolgt voraussichtlich im Rahmen der Arbeiten der Version 3.2, bis dahin ist nur die Navigation in das entsprechende Widget verlegt.
* Außer im Navigationswidget sollten in der Sidebar eindeutige und passende Icons in der Farbe blau verwendet werden und klickbar sein. Insbesondere Aktionen profitieren von der leichten Auffindbarkeit durch Icon + Text.
