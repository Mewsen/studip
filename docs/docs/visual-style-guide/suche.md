---
title: Suche
sidebar_label: Suche
---

* Objekte, nach denen gesucht werden kann (und was man dann damit macht)
    * Personen
        * Adressbuch: zum Adressbuch hinzufügen; zu einer Gruppe im Adressbuch hinzufügen
        * Globale Einstellungen: Globale Eigenschaften ändern
        * Veranstaltungen: als Dozent/Tutor zu einer Veranstaltung hinzufügen
        * Veranstaltungen: als Teilnehmer zu einer Veranstaltung hinzufügen
        * Messaging: zur Empfängerliste hinzufügen
        * allg. Personensuche: persönliche Homepage aufrufen, Nachricht schicken
        * Einrichtungen: zur Mitarbeiterliste hinzufügen
        * Ressourcenverwaltung: als lokalen/globalen Ressourcenadmin eintragen
    * Veranstaltungen
        * Veranstaltungssuche für Studis: zu einzelnen Veranstaltungen wechseln (= zur Übersichtsseite wechseln); persönliche Homepage einzelner Veranstaltungen aufrufen
        * Veranstaltungssuche für Admins: Veranstaltung zum Bearbeiten auswählen, diverse "Batch-Aktionen" (Sichtbarkeit, Sperrebenen usw.) ausführen
        * im Archiv: diverse Aktionen (Details aufrufen, Dateisammlung herunterladen, endgültig löschen, ...)
        * Veranstaltungshierarchie: Veranstaltung(en) zu Studienbereichen hinzufügen
    * Einrichtungen
        * Zur Einrichtung in Stud.IP wechseln
        * Zur Website der Einrichtung wechseln
        * eine E-Mail an den Ansprechpartner schreiben
    * Ressourcen
        * diverse Aktionen (die man auch sonst an Ressourcen vornehmen kann: Belegungsplan aufrufen, Eigenschaften bearbeiten, ...)
    * Bereiche (in News-, Voting- und Evaluationsverwaltung)
        * Neues Voting/neuen Test in einem Bereich erstellen
        * Bereich auswählen (um dort anschließend eine News zu erstellen/bearbeiten)
    * Forenbeiträge
        * diverse Aktionen (die man auch sonst an Forenbeiträgen vornehmen kann: antworten, zitieren, bearbeiten, ...)
        * Öffentliche Evaluationsvorlagen
        * zu den eigenen Evaluationsvorlagen hinzufügen
    * Wikiseiten
        * Wikiseiten aufrufen
    * Literatur (= Einträge in Literaturlisten)
        * Details aufrufen
        * in Merkliste eintragen
        * zum Eintrag im externen Katalog (OPAC) wechseln
* Formulierung der Suchanfrage
    * Variante 1: nur ein einzeiliges Textfeld
    * Variante 2: Textfeld(er) plus weitere Formularfelder (z. B. Veranstaltungssuche, Personensuche, Ressourcensuche)

* Auto-Complete
    * ...

* Auslösen der Suchanfrage
    * Variante 1: Klick auf Icon "Lupe" (z. B. Suche nach Dozenten auf admin_seminare1.php, Suchen eines Wunschraums auf admin_room_request.php)
    * Variante 2: Klick auf Button "Suche starten" (z. B. Suche nach Veranstaltungen auf sem_portal.php, Suche nach Ressourcen, Suchen im Archiv,
    * Variante 3: Klick auf Button "suchen" (z. B. Suche nach Personen auf browse.php, Suche nach Literatur auf lit_search.php)

## Mögliche Guidelines
* Die Eingabe von Suchbegriffen erfolgt grundsätzlich in ein einzeiliges Texteingabefeld (input type="text").
* Sofern das Suchformular nur aus diesem Textfeld besteht, wird die Suche durch Klicken auf ein rechts neben dem Textfeld angebrachtes Lupen-Icon ausgelöst.
* Besteht das Suchformular aus mehreren Formularfeldern (z. B. zum Einschränken der Suchergebnisse), so wird die Suche grundsätzlich durch einen Button mit dem Text "Suche starten" ausgelöst.

## Darstellung von Suchergebnissen

### Stand der Dinge
* **Variante 1:** Dropdownliste ersetzt Eingabefeld (Beispiele: Zuweisen eines Dozenten zu einer Veranstaltung auf admin_seminare1.php; Auswählen eines Wunschraums beim Formuliren einer Raumanfrage)
* **Variante 2:** Aufklappbare (bzw. bereits aufgeklappte) Listenelemente (z. B. Ressourcensuche, Literatursuche)
* **Variante 3:** Einfache Liste (z. B. Veranstaltungssuche sem_portal.php, Personensuche browse.php und_new_user_md5.php)
* **Variante 4:** Aufgeklappte Elemente innerhalb einer hierarchischen Liste von ansonsten zugeklappten Elementen (Suche nach Einrichtungen institut_browse.php)
* **Variante 5:** Mehrzeilige Selectbox (Freie Suche nach Personen in der Gruppenverwaltung in Einrichtungen, Gruppenverwaltung im Adressbuch)

### Mögliche Guidelines
* Oberhalb der Suchergebnisse soll die Anzahl gefundener Elemente ausgegeben werden.
* Unterscheidung nach Verwendungszweck der Ergebnisse
    * **Auswählen genau eines Elements der Ergebnisliste** (z. B. Zuordnung eines Dozenten zu einer Veranstaltung, Auswählen eines Wunschraumes in einer Raumanfrage)
    * **Auswählen ggf. mehrerer Elemente der Ergebnisliste** (z. B. Zuordnung von Veranstaltungen zu Studienbereichen in der Studienbereichsverwaltung, Zuordnen von per Suche gefundenen Personen in der Gruppenverwaltung in Einrichtungen)
    * **Anklicken genau eines Elements der Ergebnisliste, um es anzusteuern** (z. B. Veranstaltungssuche [sem_portal], Personensuche [browse.php])

### Fragen/Ideen
* Wie hängen die Guidelines zu den Suchergebnissen mit denen zu Elementlisten zusammen? M.a.W.: Wann sollen Suchergebnisse in Form von Elementlisten ausgegeben werden, für die dann automatisch die dort definierten Regeln greifen?
    * Idee: Suchergebnisse werden grundsätzlich in Form von Elementlisten dargestellt. (Ggf. mit definierten Ausnahmen wie z.B. Dozentensuche auf admin_seminare1.php)
* Wovon kann/sollte man die Art der Darstellung abhängig machen?
    * Verwendungszweck der Suchergebnisse (s.o.)
    * Art und Darstellung des Suchformulars, aus dem die Suchergebnisse stammen
    * Zur Verfügung stehender Platz (z.B. innerhalb von auf- und zuklappbaeren Elementlisten, Beispiel: Personensuche innerhalb der Gruppenverwaltung in Einrichtungen)
    * Art der gesuchten Elemente (Personen vs. Veranstaltungen vs. Ressourcen vs. ...)
    * Vorhersagbarkeit der Treffermenge (iframe oder selectbox bei potenziell großen Treffermengen)
    * %blue% Denkbar ist eine Matrix mit zwei Kriterien, in der für jede Kombination aus Kriterienausprägungen ein eigenes Set von Darstellungsregeln definiert ist
* Blättern
    * Soll (grundsätzlich bzw. nach definierten Kriterien) innerhalb der Suchergebnisse geblättert werden können?
    * Wenn ja, wie soll das Blättern dargestellt werden? (Ggf. generelle Regeln für Blätterfunktion)
