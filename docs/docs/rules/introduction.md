---
id: introduction
title: Regeln für die Stud.IP-Entwicklung
sidebar_label: Entwicklungsregularien
---

Stud.IP ist Open-Source-Software. Offizielle Release-Versionen werden von der Stud.IP-Core-Group unter einer Open-Source-Lizenz veröffentlicht.

# Test- und Entwicklungsserver

Hauptkommunikationsinstrument für die Stud.IP-Entwicklung ist der Test- und Entwicklungsserver unter http://develop.studip.de. Jeder aktive Stud.IP-Entwickler soll diesen Server regelmäßig besuchen, um aktuelle Diskussionen verfolgen und eigene Fragen und Anregungen einbringen zu können.

# Versionskontrolle und Ticket-Verwaltung

Für die Stud.IP-Entwicklung wird eine git-Installation unterhalten, die jeweilige Hauptversion liegt unter https://gitlab.studip.de/studip/studip.git.
Zur Ticketverwaltung wird Gitlab genutzt, das unter http://gitlab.studip.de erreichbar ist.

## Vergabe und Nutzung von Schreibrechten

Schreibrechte im svn werden auf Anfrage von den jeweils Zuständigen der Core-Group vergeben.
Im main-Zweig und den Release-Zweigen dürfen nur Checkins vorgenommen werden, die den unten aufgeführten Regelungen entsprechen.

# Core-Group

## Zusammensetzung der Core-Group
Die Core-Group besteht aus Personen, die sich für die Entwicklung und Pflege der offiziellen Release-Version von Stud.IP engagieren
und dabei das Vertrauen der Core-Group besitzen. Es gibt keine Größenbeschränkung und keine Proporzregeln für die Core-Group.

## Aufnahme in die Core-Group
Potenzielle neue Mitglieder müssen von einem Mitglied der Core-Group in Form einer Aufnahmeabstimmung zur Aufnahme vorgeschlagen werden. Ein Aufnahmevorschlag gilt als akzeptiert, wenn mindestens 2/3 der Core-Group-Mitglieder zugestimmt haben. Das neu gewählte Core-Group-Mitglied muss der Aufnahme zustimmen.

*Die Abstimmung ist anonym.*

Eine Person soll regelmäßig dann zur Aufnahme in die Core-Group vorgeschlagen werden, wenn zu erwarten ist, dass sie längerfristiges persönliches Engagement für die Ziele der Core-Group zeigt. Die Core-Group-Mitglieder sollen bei der Abstimmung positiv berücksichtigen, ob ein potenzielles neues Mitglied einem noch nicht repräsentierten aktiv entwickelnden Standort entstammt. Die in der Person liegenden Gründe für oder gegen eine Aufnahme müssen aber ausschlaggebend sein.

## Aufgaben der Core-Group-Mitglieder
Die Core-Group definiert regelmäßig die aus ihrer Sicht notwendigen Aufgaben für die Entwicklung und Pflege der offiziellen Release-Version von Stud.IP. Beide Begriffe umfassen nicht ausschließlich technische Aspekte.

Jede dieser Aufgaben soll von mindestens einem Core-Group-Mitglied verantwortlich übernommen werden.

Jedes Core-Group-Mitglied soll mindestens eine dieser Aufgaben verantwortlich oder mitverantwortlich übernehmen. Das Mitglied kann Teilaufgaben auch an Nicht-Core-Group-Mitglieder übertragen, verantwortet die regelkonforme Ausführung aber gegenüber der gesamten Core-Group. Die Übertragung von Aufgaben geschieht durch die unwidersprochene Erklärung der Übernahme oder durch im Fall eines Widerspruchs durch Abstimmung mit 2/3-Mehrheit, die Abgabe oder Entziehung von Aufgaben durch Erklärung der Abgabe oder Abstimmung mit 2/3-Mehrheit.

Jedes Core-Group-Mitglied soll sich an organisatorischen Tätigkeiten und Treffen der Core-Group beteiligen.

Alle Core-Group-Mitglieder sollen sich an den Abstimmungen der Core-Group beteiligen.

## Austritt und Ausschluss aus der Core-Group
Die Core-Group-Mitgliedschaft endet mit der Erklärung des Austritts oder einer erfolgreichen Ausschluss-Abstimmung mit 2/3-Mehrheit. Jedes Core-Group-Mitglied kann den Ausschluss eines Mitgliedes durch Start einer entsprechenden Abstimmung beantragen.

Ein Ausschlussantrag soll regelmäßig dann gestellt werden, wenn ein Mitglied den in 3. genannten Aufgaben ohne hinreichende Begründung nicht nachkommt.

Die Abstimmung ist anonym.

## Wiederaufnahme
Die Wiederaufnahme in die Core-Group ist möglich.

## Änderungen der Regeln
Jedes Core-Group-Mitglied kann Änderungsvorschläge für diese Regeln einbringen.
Ein Änderungsvorschlag gilt als akzeptiert, wenn 2/3 der Mitglieder zustimmen.

# StEPs

![image](../assets/dc7253456d69ace6eb97258059ce0a87/image.png)

Stud.IP-Enhancement-Proposals (StEPs) sind Änderungsvorschläge für eine kommende Stud.IP-Release-Version. Sie sind das Standardinstrument zur Diskussion und strukturierten Umsetzung aller Änderungen, die nicht der Fehlerbehebung (BIESTs), der Einführung kleiner unstrittiger Änderungen (TICs) oder der Etablierung längerfristiger Codeänderungen (Lifters) dienen.

Jeder Entwicklungsinteressierte darf Proposals einbringen. Proposals müssen konkrete Maßnahmen mit Umsätzungsplan vorschlagen und nicht lediglich ein Problem oder einen Wunsch identifizieren und um Lösungsvorschläge bitten. In diesen Fällen sind weiterhin die etablierten Foren (Developer-Board etc.) zu nutzen.

Die Core-Group entscheidet über die Annahme eines StEPs.

## Proposal
Ein StEP muss folgendes beinhalten

### Ziel
Sehr knappe Zusammenfassung des Proposals (Titel/Überschrift)

### Beschreibung:
* Problembeschreibung bzw. Ziel der Änderung
* Begründung, warum das Proposal sinnvoll bzw. notwendig erscheint

### Maßnahmen (Überblick)
* Knappe Beschreibung einer Lösung ohne Implementationsdetails
* sollte dennoch in Grundzügen auf die wichtige Punkte eingehen (neue DB-Tabellen, Benutzung von vorhandenen Funktionalitäten,...).

### Maßnahmen (Details)
* konkrete Beschreibung, die keine wesentlichen Fragen hinsichtlich technischer Umsetzung und Interfacegestaltung offen lässt.
* kann schrittweise entwickelt werden

### Kurzbezeichnung des Integrationsaufwandes
* gering: eigenständiges Modul, das über Konfigurationsschalter komplett ein-/ausschaltbar ist oder Änderungen an einzelnen Dateien, die keine Änderungen an Datenbank oder systemweit genutzten Datenstrukturen erfordern
* mittel: tief greifende Änderung an wenigen Dateien, die geringe Modifikationen der Datenbankstruktur erfordern und keine systemweiten Auswirkungen haben, oder geringfügige Änderungen an vielen Dateien, die gut überschaubare systemweite Auswirkungen haben
* hoch: alle Änderungen mit gravierenden systemweiten Auswirkungen

### Durchführung
* Verbindliches Angebot eines Durchführungsplanes, bestehend aus:
* klar benannter Zuständigkeit für die Implementation, ggf. auch die Pflege
* mit dem Releasezyklus abgestimmter Zeitplan für die Implementation

## Kommentar- und Diskussionphase
Die Mitglieder der Core-Group, aber auch alle anderen Entwicklungsinteressierten sind unmittelbar nach Veröffentlichung eines Proposals aufgerufen, es zu kommentieren und zu diskutieren. Der StEP-Text darf bis zur Abstimmung beliebig häufig geändert werden

## Abstimmungsphase
Nach Abschluss der Kommentar- und Diskussionphase startet ein Core-Group-Mitglied in der Veranstaltung "Stud.IP Enhancement Proposals" eine Abstimmung über Annahme oder Ablehnung eines Proposals in der dann aktuellen Form. Der StEP-Autor und die im Umsetzungsplan Genannten müssen ihre Zustimmung dazu signalisieren. Eine Abstimmung sollte nur dann erfolgen, wenn der Diskussionsverlauf eine Annahme als wahrscheinlich erscheinen lässt.

Die Abstimmung wird mittels eines nichtanonymen Stud.IP-Votings in der Veranstaltung "Stud.IP Enhancement Proposals" vorgenommen, bei dem jedes Mitglied der Core-Group eine Stimme hat. Als Abstimmungszeitraum sind mindestens 14 Tage vorgesehen. Ein positives Abstimmungsergebnis gilt für 12 Monate.

Für jedes Proposal sind folgende Abstimmungsmöglichkeiten vorzusehen:
* Annahme (ja)
* Ablehnung (nein)
* Enthaltung

Dabei gelten folgende Regeln:
* Für die Annahme eines StEPs müssen mindestens doppelt soviele Ja- wie Nein-Stimmen abgegeben werden
* Für eine gültige Abstimmung ist eine Mindestbeteiligung von 2/3 der Coregroup-Mitgliedern notwendig
* Sobald eine absolute 2/3-Mehrheit der Coregroup-Mitglieder mit "Ja" gestimmt hat, kann das Voting auch schon vor Ablauf der zwei Wochen beendet werden

Grundlage für das Abstimmungsverhalten sollten folgende Fragen sein:
* ist das Feature sinnvoll?
* ist das Feature mit der Stud.IP-Philosophie vereinbar?
* ist die inhaltliche Konzeption akzeptabel und hinreichend generisch?
* ist die technische Konzeption akzeptabel?
* sind eventuelle Auswirkungen auf andere Stud.IP-Bereiche berücksichtigt?
* sind alle offenen Fragen beantwortet?

## Umsetzungsphase
Nach der Annahme eines StEPs dürfen die im Proposal genannten Zuständigen innerhalb von 12 Monaten eine dem StEP-Text entsprechende Implementation in einen für diesen StEP angelegten branch des Stud.IP-gitlab einchecken bzw. einchecken lassen. Eine Verlängerung der Frist ist nach entsprechender Core-Group-Abstimmung möglich.

Bis zum Codefreeze muss ein öffentlicher branch der Umsetzung vorliegen sowie ein MR (ohne Draft) und die entsprechenden QM::?-Labels an dem Issue müssen gesetzt sein.

## Test- und Vetophase
Die Core-Group definiert regelmäßig eine Liste von Zuständigkeiten mit Vetorecht, die die Qualität des Stud.IP-Releases sichern sollen. Die jeweils aktuelle Liste der Zuständigkeiten wird zusammen mit diesen StEP-Regeln veröffentlicht und umfasst u.a. Aspekte wie Einhaltung der Code-Konventionen, Code-Qualität, Einhaltung der GUI-Richtlinien und hinreichende Dokumenation. Das Vetorecht darf (gemeinschaftlich) von den jeweils zuständigen Core-Group-Mitglieder vom ersten Check-In des StEPs bis zum Ende des Testzeitraumes ausgeübt werden.

Erst wenn alle Qualitätsbeauftragten ihre Zustimmung zur Umsetzung des StEPs in Form eines "+" abgegeben haben darf der StEP in den main überführt werden. Verantwortliche Core-Group-Mitglieder sind verpflichtet, möglichst frühzeitig nach dem Einchecken eines angenommenen StEPs Rückmeldung zu kritischen Punkten zu geben und konstruktive Vorschläge zur Behebung erkannter Missstände zu machen. Ein endgültiges Veto ("-") erfolgt, wenn diese Vorschläge nicht beachtet werden und der Zeitplan für das Release keine andere Wahl lässt. StEPs sollten möglichst in der Reihenfolge getestet und in den trunk überführt werden, in der sie von den Entwicklern zum Testen freigegeben wurden.

## Release-Branch
Zu einem am Anfang des Release-Zyklus festgelegten Termin erfolgt das Ausbranchen des neuen Releases aus dem main.

## Status
Jeder StEP befindet sich in genau einem Status, der jeweils in der Beschreibung aktualisiert wird:

* **neu** - initialer Status, der StEP wurde vorgestellt und befindet sich in der Diskussions- oder 1. Abstimmungsphase
* **angenommen** - die erste Abstimmung ist positiv verlaufen, der StEP darf in den trunk eingespielt werden
* **test** - der Code wurde in einen eigenen branch eingebaut und ist nach Meinung des Verantwortlichen vollständig und weitgehend fehlerfrei; allgemeine Aufforderung zum Testen an alle
* **veto** - gegen den eingespielten StEP hat ein zuständiges Core-Group-Mitglied Einwände erhoben, der StEP kann in dieser Form nicht in den trunk gelangen
* **tested** - alle Qualitäts-Zuständigen haben ihr Einverständnis gegeben, der StEP darf in den trunk übernommen werden
* **release** - es wurde endgültig kein Veto erhoben und der StEP ist in den Release-Branch eingegangen
* **abgelehnt** - die erste Abstimmung ist negativ verlaufen oder liegt länger als 12 Monate zurück, oder ein Veto hat verhindert, dass der StEP in das Release gelangt; das Verfahren muss neu gestartet werden oder der StEP wird verworfen
* **verworfen** - die Funktionalität wird nicht mehr benötigt, ist durch anderen StEP abgedeckt, etc.

![image](../assets/dc7253456d69ace6eb97258059ce0a87/image.png)

# TICs

Nicht jede Änderung muss den StEP-Prozess durchlaufen. TICs (tiny improvement commits) sind eingecheckte Code-Änderungen, die keiner Abstimmung bedürfen.

## Erstellen

Ein TIC wird durch Anlegen eines Tickets vom Typ TIC im Gitlab erzeugt. Alle Entwickler mit Schreibrechten im svn-Repository für den Release-Zweig dürfen TICs anlegen. Das Ticket muss eine Beschreibung der Änderungen enthalten.

## Einbauen

Alle TIC-bezüglichen Commits müssen dem Ticket zugeordnet werden. Commits sind nur bis zum Codefreeze erlaubt. Bis zum Codefreeze muss ein öffentlicher branch der Umsetzung vorliegen sowie ein MR (ohne Draft).

## Widerspruch

Sämtlicher einem TIC zugeordneten Commits müssen rückgängig gemacht werden, wenn ein Core-Group-Mitgliedes Widerspruch gegen den TIC einlegt. Die Widerspruchsfrist endet mit dem Release-Branch.

TICs, gegen die Widerspruch eingelegt wurde, können in StEPs umgewandelt und dann diskutiert werden.

# Lifters

* **L**aufende, **i**nkrementell **f**ortschreitende **Te**chnik**r**enovierung für **S**tud.IP*

Einige wichtige, sinnvolle und gewünschte Überarbeitungen des Stud.IP-Quellcodes erfordern Eingriffe an vielen Stellen des Codes. Beschränkte Ressourcen bei Entwicklungskapazität und Qualitätssicherung machen aber eine Umstellung "auf einen Schlag" unmöglich. Insbesondere will es die Entwickler-Community vermeiden, laufende Entwicklungen bis zur Fertigstellung einer runderneuerten Version des Codes zu stoppen. Verzögerungen bei der - häufig rein technisch motivierten und nach außen "unichtbaren" - Runderneuerung könnten das gesamte Projekt gefährden.

Die "Stud.IP-Lifters" etablieren ein Verfahren, solche Überarbeitungen in die laufenden Release-Zyklen einzubinden. Sie setzen klare Ziele und definieren eindeutige Anweisungen für Entwickler, wie sie bestehenden Code überarbeiten und neuen Code Lifter-konform gestalten können.

"Lifters" definieren Mittel zur Fortschrittskontrolle. Zwar haben sie keinen verbindlichen Endtermin oder ein verbindliches Fertigstellungs-Release, es ist aber zu jedem Zeitpunkt nachvollziehbar, wie weit die Umsellungsarbeiten fortgeschritten sind.

"Lifters" helfen den Entwicklern. Sie geben klare Richtlinien, wie bislang oft individuell zu lösende Codierungs-Probleme zu handhaben sind. Sie geben Einsteigern und Entwicklern, die dem Projekt "etwas Gutes" tun wollen, ohne genau zu wissen, welche Arbeiten gerade sinnvoll und erwünscht sind, wohlportionierte Arbeitspakete an die Hand.

"Lifters" laufen quasi "nebenher" zur normalen Stud.IP-Entwicklung. sie sollen klar und einfach beschrieben, wie Code-Überarbeitungen, die "ohnehin" passieren, gleich weitere Aspekte mitberücksichtigen können. "Lifters" sind damit keine neue Art von Vorschrift, sondern vereinheitlichen bisherige Entwicklungsleitlinien.

"Lifters" durchlaufen einen ähnlichen Prozess wie StEPs. Sie werden von einem Core-Group-Mitglied vorgeschlagen, öffentlich diskutiert und schließlich in der Core-Group verbindlich abgestimmt. Nach der ersten Abstimung hat ein Lifter den Status "in der Umsetzung". SVN-Checkins, die Lifter-Richtlinien umetzen, bedürfen keines gesonderten StEPs (Stichwort: Kein Check-In ohne StEP), müssen aber im Rahmen der Fortschrittskontrolle (s.u.) bekannt gemacht und getestet werden.

Damit haben Lifter folgende Stadien:
* **neu** = Vorschlag in der Diskussion
* **angenommen** = Lifter zur Umsetzung verabschiedet. Angenommene Lifter sind verbindlich für alle neuen Entwicklungen, ausgenommen Bugfixes.
* **abgeschlossen** = Alle Arbeiten sind abgeschlossen. Abgeschlossene Lifter sind verbindlich für alle zukünftigen Entwicklungen.
* **inaktiv** = Alter Lifter, der nicht mehr beachtet werden muss.

## Formulieren
Lifter werden über das Formular im Wiki der Veranstaltung "Stud.IP Lifters" erstellt und anchließend im Wiki gepflegt.
Dabei wird ein Ticket im Gitlab angelegt.
Alle folgenden Lifter-bezogenen Checkins müssen sich auf dieses Ticket beziehen.

Lifter-Vorschläge müssen folgende Anforderungen erfüllen:
* eindeutig spezifizierte Umstellungsregeln (umfassende Beschreibung von Vorher- und Nachher-Zustand)
* alter Code muss in der Übergangssphase lauffähig bleiben
* soweit automatisierte Umstellungshilfen erstellt werden können (z.B. sed-Skripte), solten diese vom Lifter-Autor erstellt werden und ihre Anwendung wie manuelle Umstellungen dokumentiert werden. Auch automatische Umstellungen müssen wie manuelle kontrolliert werden.
* Im Rahmen der Diskussion sollen Testszenarien, d.h. belastbare Aussagen über den Zielzustand definiert werden, die laufend qualitätssichernd, vor allem vor Release-Terminen, durchgeführt werden können. Ausreichende Testszenarien müssen VOR der Core-Group-Abstimmung vorgelegt werden.
* Fortschrittskontrolle. Ein Lifter-Vorschlag muss eine abarbeitbare Liste von Arbeitspaketen enthalten, die zur Erledigung des Lifters notwendig sind. Dies kann z.B. durch Markierung der zu bearbeitenden Dateien (s.u.) passieren.
* Ergeben sich im Laufe der Arbeiten an einem Lifter neuen Anforderungen, die von der ursprünglichen Beschreibung abweichen, muss entweder:
    * für die fragliche Anpassung ein StEP erstellt werden,
    * die Lifter-Beschreibung ergänzt und die Ergänzung zur Abstimmung gestellt werden, oder,
    * der Lifter per Abstimmung (s.u.) eingestellt werden.
* Lifter können mit der gleichen Mehrheit, die zu ihrer Annahme nötig war, als inaktiv oder bloße Empfehlung gekennzeichnet werden. Sollten die schon vorhandenen Änderungen rückgängig gemacht werden, ist dies in offensichtlich einfachen Fällen unmittelbar nach der Abstimmung zulässig, in komplexeren Fällen nur nach Annahme eines entsprechenden StEPs oder Lifters.
* Neu erstelle Dateien und Arbeiten an StEPs müssen nach Annahme eines Lifters dessen Anforderungen genügen.
* Bugfixes müssen sich bei Dateien, die noch nicht Lifter-konform sind, nicht an die Lifter-Anforderungen halten.

## Abstimmung
Die Abstimmung wird mittels eines nichtanonymen Stud.IP-Votings in der Core-Group-Veranstaltung vorgenommen, bei dem jedes Mitglied der Core-Group eine Stimme hat. Als Abstimmungszeitraum sind mindestens 14 Tage vorgesehen.

Für jedes Proposal sind folgende Abstimmungsmöglichkeiten vorzusehen:
* Annahme (ja)
* Ablehnung (nein)
* Enthaltung

Dabei gelten folgende Regeln:
* Für die Annahme eines Lifters müssen mindestens doppelt soviele Ja- wie Nein-Stimmen abgegeben werden
* Eine Enthaltung zählt unabhängig vom Komplexitätsgrad immer als Enthaltung
* Für eine gültige Abstimmung ist eine Mindestbeteiligung von 2/3 der Coregroup-Mitgliedern notwendig
* Sobald eine absolute 2/3-Mehrheit der Coregroup-Mitglieder mit "Ja" gestimmt hat, kann das Voting auch schon vor Ablauf der zwei Wochen beendet werden

Grundlage für das eigene Abstimmungsverhalten sollten folgende Fragen sein:
* ist das Feature sinnvoll?
* ist das Feature mit der Stud.IP-Philosophie vereinbar?
* ist die inhaltliche Konzeption akzeptabel und hinreichend generisch?
* ist die technische Konzeption akzeptabel?
* sind eventuelle Auswirkungen auf andere Stud.IP-Bereiche berücksichtigt?
* sind alle offenen Fragen beantwortet?

### Umsetzungsphase und Fortschrittskontrolle
In aller Regel betreffen Lifters PHP-Dateien. In diesen Fällen ist nach Annahme eines Lifters ein SVN-Checkin durchzuführen, der in allen betroffenen Dateien einen Kommentar der Form
```php
<?
# Lifter001: TODO - evtl. genauere Beschreibung.. (alles nach dem - ist Beschreibung)
?>
```
einfügt.

Ist ein Lifter für eine Datei komplett oder teilweise erledigt, ist der Kommentar zu ändern in:

```php
<?
# Lifter001: TEST - evtl. genauere Beschreibung der Maßnahmen.. (alles nach dem - ist Beschreibung)
?>
```

Bei teilweiser Erledigung kann zusätzlich erläutert werden, welche Arbeiten noch verbleiben.

In HTML- oder CSS-Dateien werden ebenfalls den obigen Konventionen entsprechende Kommentare angebracht.

Bei zu bearbeitenden Binärdateien ist eine Markierung in der Datei nicht möglich. Stattdessen ist in der Lifter-Beschreibung eine Liste von noch anzupassenden Dateien zu pflegen, die jeweils aktualisiert wird. Da davon auszugehen ist, dass solche Typen seltener sind, werden genauere Regelungen erst bei Bedarf getroffen.

## Qualitätskontrolle
Lifter erfordern erhöhte Aufmerksamkeit beim Testen. In kritischen Phasen eines Branches  (Betatestphase im Release-Branch) dürfen dort keine Lifter-bezogenen Änderungen eingecheckt werden. Bei größeren Checkins bzw. besonders kritischen Codeteilen verpflichtet sich die Core-Group angemessene Tests durchzuführen.

Jedes Lifter-bezogene Checkin wird zunächst mit dem Status "TEST" versehen (s.o.). Ein anderer Entwickler mit Schreibrechten im SVN muss den Code überprüfen und testen und anschließend die TEST-Markierung entfernen und den Code neu einchecken. Damit erscheint er als Tester im Revisions-Log. Wurde eine Datei nur teilweise bearbeitet, setzt der Tester wieder eine TODO-Markierung ein und beschreibt ggf. die noch verbleibenden Maßnahmen.

LIFTERN ist nur erlaubt bis zur TIC-Deadline und unterliegt dann demselben Test- und Vetoverfahren wie StEPs und TICs.

Entdeckte Fehler (die vermutlich mit einem Lifter zusammenhängen) werden als ganz normale BIESTs über das PlugIn in der BIEST-Veranstaltung oder direkt im Gitlab dokumentiert.

## Abschluss und Dokumentation
Insbesondere für die Außendarstellung ist es wichtig, Lifter als einmalige Anstrengung als abgeschlossen zu erklären: In der Regel wird damit ein positiv vermarktbarer Zustand erreicht (Multi-Tab-Browsing geht jetzt, Stud.IP basiert vollständig auf Layout-Templates, ...).

Um einen Lifter als abgeschlossen zu erklären ist eine Abstimmung der Core-Group erforderlich, die den gleichen Bedingungen wie zur Annahme unterliegt. Damit ist sichergestellt, dass sich die Core-Group fortan mit dem erreichten Status identifiziert und öffentliche Aussagen über den erreichten Status mitträgt.

Ein abgeschlossener Lifter bekommt den Status "done" und ist weiterhin verbindlich für alle Neuentwicklungen, Erweiterungen und Bugfixes. Werden anschließend Stellen identifiziert, die nicht Lifter-konform sind, sind sie als normale Biests zu melden.

Verworfene oder obsolet gewordene Lifter werden mit dem Status "inaktiv" markiert und sind nicht verbindlich für die weitere Entwicklung.

# BIESTs

## Fehler melden

BIESTer werden über das gitlab des Stud.IP-Projektes gemeldet und mit den entsprechenden Labels "BIEST" und "Version::x.x" für die früheste Version, in der der Fehler aufgetreten ist ausgezeichnet. Optional kann noch die betroffene Komponente angegeben werden.

## Fehler beheben

Checkins, die ein BIEST beheben, müssen dem jeweiligen Ticket zugeordnet werden.

Komplexe Fehler sollten in einem eigenen branch behandelt werden und die Fixes wenn möglich von einer zweiten Person approved werden.

Bugfixes müssen immer in einem einzigen Commit auf den main gebracht werden. Kommt es zu einem Fehler und das Issue ist noch nicht gemerged, muss der Commit rückgängig gemacht und der Bug in einem MR gelöst werden. Ist das Issue bereits in alte Versionen portiert, so muss ein neues Issue aufgemacht werden, was dieses Problem behebt.

# Release

## Service-Release erstellen

Die folgenden Schritte sind zum Erstellen eines Service-Releases (z.B. 4.5.5) durchzuführen:
* Klären ob alle relevanten Bugfixes "nach unten" portiert wurden (nachfragen bei André oder Jan-Hendrik)
* Prüfen ob alle Issues & Merge requests am entsprechenden Milestone geschlossen wurden, gegebenenfalls offene Issues an nächsten Milestone verschieben
* Auschecken des aktuellen Standes des Release-Branches
* Kompilieren der notwendigen Assets
* Test der wichtigsten Stud.IP-Seiten
* gegebenenfalls Übernahme der Changelog-Einträge vorhergehender Service-Releases (z.B. von 4.5.5 -> 4.6.3)
* Extrahieren aller Issues des Milestones, umformatieren und oben zum Changelog hinzufügen
* Link zu den Issues des Milestones im gitlab oben zum Changelog hinzufügen
* aktuelles Datum und version number oben zum Changelog hinzufügen
* version number in ./VERSION und ./lib/bootstrap.php aktualisieren
* aktuellen Stand des Release-Branches mit der version number taggen
* Milestone schließen
* Ankündigung auf dem Developer-Server erstellen bzw. aktualisieren

## Main-Release erstellen

Beim Erstellen eines Main-Releases (z.B. 4.6) sind zusätzlich zu der Liste oben die folgenden Schritte notwendig:
* Erstellen des entsprechenden Branches
* Vervollständigung der Übersetzung und Aktualisieren der Übersetztungsdateien
* history.txt Aktualisieren
* gegebenenfalls Aktualisierung von ./AUTHORS ./INSTALL ./README
* Umstellen von DEFAULT_ENV = 'production' in ./lib/bootstrap.php
* Test einer Neuinstallation
* Tests eines Upgrades von der letzten nicht mehr unterstützten Stud.IP-Version aus
* gegebenenfalls Anpassung von https://www.studip.de/home/download/
