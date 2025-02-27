---
title: Kriterien für Barrierefreiheits-Reviews
slug: /a11y/review
sidebar_label: Reviews
---

Generelle Informationen zur Barrierefreiheit sind auf der Seite [Barrierefreiheit](start) zu finden.

Tests auf Barrierefreiheit sind in mehrere Bestandteile unterteilt:

1. Prüfung auf ausreichenden Kontrast und Kenntlichmachung bei GUI-Elementen
2. Prüfung auf Tastaturbedienbarkeit von Seitenelementen
3. Prüfung auf Nutzbarkeit von Seitenelementen mit Screenreadern

Die Fragestellungen der einzelnen Testschritte werden im folgenden aufgeführt.

## Prüfung auf ausreichenden Kontrast und Kenntlichmachung bei GUI-Elementen

- Haben Vordergrund- und Hintergrundfarbe einen ausreichenden Kontrast zueinander?
- Werden Links passend hervorgehoben?
- Sind die angezeigten Informationen auch ohne Farben erkennbar?

##  Prüfung auf Tastaturbedienbarkeit von Seitenelementen

Lässt sich ein neuer oder geänderter Seitenbestandteil per Tastatur bedienen?

- Kann ein Link, Button oder anderes interaktives Element per TAB angesprungen werden?
- Können die üblichen Tasten zur Steuerung von interaktiven Elementen genutzt werden?
    - Link, Button: Eingabetaste zum Aufrufen/Auflösen
    - Checkbox, Radio-Button: Leertaste zum Auswählen/Abwählen
    - Select-Box: Pfeiltasten zum Auswahl eines Eintrags

## Prüfung auf Nutzbarkeit von Seitenelementen mit Screenreadern

Wird ein neuer oder geänderter Seitenbestandteil korrekt für Screenreader ausgezeichnet?

- Werden Formularelemente und Aktionselemente korrekt vorgelesen?
    - Buttons als „Schalter“?
        - Ein Button ändert einen Teil der Seite oder löst eine Aktion auf der aktuellen Seite aus. Beispiele: Aufklappen von Bereichen, Löschen oder Sortieren von Elementen in einer Liste.
    - Links als „Link“?
        - Ein Link ruft eine neue Seite im Hauptbereich oder im Dialog auf oder der Link ist ein Anker zu einer Position der aktuellen Seite.
    - Select-Feld als „Auswahlfeld“?
    - ...
- Sind Icons, die nur Schmuckelemente sind, für Screenreader unsichtbar?
- Sind Bilder oder Icons, die eine wichtige Information mitliefern, mit einem Alternativtext versehen?

Getestet wird hauptsächlich mit der Kombination aus JAWS und Microsoft Edge. Beim Test mit anderen Screenreadern und Browsern sollten deren Marktanteile beachtet werden, damit eine getestete Lösung für möglichst viele Personen funktioniert: https://webaim.org/projects/screenreadersurvey9/
