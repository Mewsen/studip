---
title: Fehler berichten
---

Für Meldung und Verwaltung von Fehlern wird das [Gitlab](http://gitlab.studip.de) auf dem Entwicklungsserver verwendet. 
Dort sind alle bekannten und behobenen Fehler dokumentiert, und auch die Planung für die zukünftigen Stud.IP-Releases wird dort organisiert.

## Wie berichte ich einen Fehler?

Für die Meldung von Fehlern in Stud.IP gibt es verschiedene Wege:

* Direkt über das Formular im [Gitlab](https://gitlab.studip.de/studip/studip/-/issues/new) (Registrierung im Entwickler- und Anwenderforum erforderlich)
* Im Developer-Board im Forum berichten [Veranstaltung Bugboard (BIESTs)](https://develop.studip.de/studip/dispatch.php/course/forum/index?cid=28e888802838a57bc1fbac4e39f0b13a) im [Entwickler- und Anwenderforum](https://develop.studip.de/studip/) (Registrierung erforderlich)
* per E-Mail an [studip-users@lists.sourceforge.net](mailto:studip-users@lists.sourceforge.net)
* Oder im [Entwickler-Chat](https://develop.studip.de/studip/assets/images/icons/blue/chat.svg) uns mitteilen.

**Bitte geben Sie in jedem Fall unbedingt an:**

* In welcher Stud.IP-Version bzw. an welchem Standort tritt der Fehler auf? (z.B.: Version 5.4, Uni Göttingen)
* Welchen Browser in welcher Version verwenden Sie? (z.B.: Internet Explorer 7)
* In welcher Rolle sind Sie im System unterwegs? (z.B. "Dozent")
* Beschreiben Sie möglichst genau, was unter welchen Umständen getan werden muß, um den Fehler zu reproduzieren.

### Fehlerbericht über das gitlab

Der "normale" Weg, einen Fehler in Stud.IP zu melden, ist die Verwendung des entsprechenden Formulars im gitlab: https://gitlab.studip.de/studip/studip/-/issues/new

Beachten Sie, dass es für die Entwickelnden hilfreich ist, wenn Sie möglichst präzise Angaben zu dem Fehler machen. 

- Was genau muss man tun, um den Fehler reproduzieren zu können? 
- Tritt der Fehler in jedem Browser auf oder nur in manchen (Chrome, Firefox, Safari, Edge)
- In welcher Version von Stud.IP tritt der Fehler auf (Ihre Stud.IP-Version steht meist im Impressum)?
- Idealerweise laden Sie einen Screenshot mit hoch, wo auch die Adresszeile des Browsers sichtbar ist.


### Fehlerbericht via E-Mail

Alternativ können Fehler auch per Mail an die Adresse [studip-users@lists.sourceforge.net](mailto:studip-users@lists.sourceforge.net) berichtet werden. Diese landen allerdings nicht automatisch in unserem Stud.IP-Ticketsystem, es kann daher gelegentlich passieren, daß diese längere Zeit unbearbeitet sind oder sogar wieder ganz in Vergessenheit geraten.

Daher sollten Fehler, wenn möglich, nicht via E-Mail berichtet werden, sondern über den unter 1.1 oder 1.2 genannten Weg.

## Wie berichte ich einen Verbesserungsvorschlag?

Die erste Anlaufstelle für Erweiterungs- oder Verbesserungsvorschläge sollte das Forum der Veranstaltung [Developer-Board](https://develop.studip.de/studip/forum.php?cid=a70c45ca747f0ab2ea4acbb17398d370&view=tree) im [Entwickler- und Anwenderforum](https://develop.studip.de/studip/) sein. Dort kann man mit den Stud.IP-Entwicklern diskutieren, ob und ggf. in welcher Forum die eigenen Ideen umgesetzt werden könnten. Verbesserungsvorschläge sollten (von Ausnahmefällen abgesehen) nicht ohne vorherige Diskussion ins gitlab eingetragen werden.

### Typen von Tickets

Es gibt folgende Ticket-Typen:

| Typ | Beschreibung |
| ---- | ---- |
| BIEST | ein Fehler im offiziellen Release |
| Lifters | eine langfristig angelegte Überarbeitung, muß zuvor von der Core-Group abgestimmt werden |
| StEP | ein Verbesserungsvorschlag, muß zuvor von der Core-Group abgestimmt werden |
| TIC | ein "kleiner" Verbesserungsvorschlag |


### Milestone
// TODO: muss überarbeitet werden

Ein Milestone im gitlab entspricht jeweils einem offiziellen Release von Stud.IP (wie 5.3 oder 5.4) und wird als ein Label abgebildet. 
Über die Milestone-Angabe im Ticket wird verwaltet, welche Tickets für welches Stud.IP-Release erfolgreich geschlossen worden sind oder noch erledigt werden müssen. Nur Tickets, die sich auf das offizielle Release beziehen, haben einen Milestone, und der Milestone sollte nur von der Person bearbeitet werden, der das Ticket zugewiesen ist (oder einem der Release-Verantwortlichen). Dabei gelten folgende Regeln:

| Typ | Beschreibung |
| ---- | ---- |
| **BIEST** | Ein offenes BIEST hat keinen Milestone. Wenn das BIEST geschlossen wird, gibt der Milestone die erste Version von Stud.IP an, die diese Korrektur enthält, das ist in der Regel der jeweils aktuelle Release-Branch. |
| **StEP**, **TIC** | Der Milestone ist die Version, für die der StEP bzw. TIC eingebaut werden soll. |
| **Lifters**| Ein Lifters hat keinen Milestone. |

**Wichtig**: Der Milestone gibt *nicht* an, in welcher Version der Fehler aufgetreten ist. Das sollte Teil des Beschreibungstextes sein.


### Zusätzliche Felder zur Qualitätssicherung

Bei Tickets der Typen StEP und TIC gibt es zusätzliche Felder, die der Qualitätssicherung durch die Coregroup dienen und an die entsprechend mit Veto-Vollmacht ausgestatteten Zuständigkeiten gekoppelt sind. Derzeit werden folgende Felder verwendet:

| Typ| Beschreibung |
| ---- | --- |
| **Code-Qualität?** | Code-Review erwünscht |
| **Code-Qualität+** | Code-Review positiv |
| **Code-Qualität-** | Code-Review negativ, d.h. Veto des Verantwortlichen |
| **Sicherheit?** | Security-Review erwünscht |
| **Sicherheit+** | Security-Review positiv |
| **Sicherheit-** | Security-Review negativ, d.h. Veto des Verantwortlichen |
| **Code-Konventionen?** | Review der formalen Code-Konventionen erwünscht |
| **Code-Konventionen+** | Review der formalen Code-Konventionen positiv |
| **Code-Konventionen-** | Review der formalen Code-Konventionen negativ, d.h. Veto des Verantwortlichen |
| **Entwickler-Dokumentation?** | Entwicklerdokumentations-Review erwünscht |
| **Entwickler-Dokumentation+** | Entwicklerdokumentations-Review positiv |
| **Entwickler-Dokumentation-** | Entwicklerdokumentations-Review negativ, d.h. Veto des Verantwortlichen |
| **Anwender-Dokumentation?** | Anwenderdokumentations-Review erwünscht |
| **Anwender-Dokumentation+** | Anwenderdokumentations-Review positiv |
| **Anwender-Dokumentation-** | Anwenderdokumentations-Review negativ, d.h. Veto des Verantwortlichen |
| **Funktionalität?** | Funktionstest aus Anwendersicht-Review erwünscht |
| **Funktionalität+** | Funktionstest aus Anwendersicht-Review positiv |
| **Funktionalität-** | Funktionstest aus Anwendersicht-Review negativ, d.h. Veto des Verantwortlichen |
| **GUI-Richtlinien?** | Review bezüglich der Nutzeroberfläche erwünscht |
| **GUI-Richtlinien+** | Review bezüglich der Nutzeroberfläche positiv |
| **GUI-Richtlinien-** | Review bezüglich der Nutzeroberfläche negativ, d.h. Veto des Verantwortlichen |


## Keywords

Über die Standardattribute hinausgehende Markierungen an einem Ticket werden über Keywords abgebildet.
