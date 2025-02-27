---
id: gitlab-workflows
title: Unsere Workflows in GitLab
sidebar_label: Gitlab-Workflows
---

tl;dr

- Jeder Commit auf den Branch `main` muss eine Referenz auf ein Issue beinhalten (`..., fixes #23` oder `..., re #23`).
- Jede Änderung sollte über einen Merge Request in den Branch `main` gelangen (Ausnahmen sind triviale Änderungen wie beispielsweise Rechtschreibfehler).
- Jeder Merge Request sollte von mindestens einer weiteren Person angeschaut und genehmigt werden.

## BIESTs

Für Bugreports und Bugfixes in Stud.IP (a.k.a. [BIESTs](Regeln#biests)) gelten die folgenden Regeln:

- Es muss ein Issue im [Stud.IP-Projekt](https://gitlab.studip.de/studip/studip) erstellt werden.
- Dieses Issue muss das Label "BIEST" haben sowie eines der Labels "Version::xy", das die niedrigste Version angibt, in der der Fehler auftaucht. Wenn dies nicht bekannt ist, kann das Label weggelassen werden. Dann sollte aber die Version eingetragen werden, in der das Problem beobachtet wurde.
- Optional kann (und sollte) eines der Label "Komponente::xy" eingetragen werden, um die Issues nach Komponente filtern zu können.
- Für Bugfixes sollte ein Merge Request erstellt werden, der das Ticket referenziert. Ausnahmen sind triviale Änderungen wie Rechtschreibfehler, die direkt auf den Branch `main` geschoben werden können.
- Die Merge Requests sollten dabei von einem Branch im Hauptprojekt erstellt werden und einer der beiden folgenden Notationen folgen:
  <ul><li><code>biest-&lt;nummer&gt;</code></li><li><code>&lt;nummer&gt;-beschreibung</code></li></ul>
  Bevorzugt wird die erste Variante, da sie klar erkennen lassen, worauf sich der entsprechende Branch bezieht. Die zweite Variante ist die, die von gitlab erzeugt wird, wenn zu einem Issue über die GUI ein Merge Request erstellt wird.
- Wenn das Problem behoben ist, muss das Ticket geschlossen werden, damit die Maintainer des Projektes erkennen können, dass der Bugfix noch in andere Versionen transportiert werden muss.
- Erst wenn der Bugfix in alle notwendigen Versionen verteilt wurde, wird der Meilenstein am Issue gesetzt.

Eine [Übersicht aller offenen Issues](https://gitlab.studip.de/studip/studip/-/issues?scope=all&state=opened&label_name%5B%5D=BIEST) erhält man, wenn in der Übersicht der Issues nach offenen Issues mit dem Label "BIEST" filtert.

## StEPs

Für Stud.IP-Enhancement-Proposals ([StEPs](Regeln#steps)) gelten die folgenden Regeln:

- Es muss ein Issue im [Stud.IP-Projekt](https://gitlab.studip.de/studip/studip) erstellt werden.
- Dieses Issue muss das Label "StEP" haben und als Meilenstein muss die Version von Stud.IP angegeben werden, für die der StEP entwickelt wird.
- Optional kann eines der Label "Komponente::xy" angegeben werden falls sich der StEP auf eine Komponente bezieht.
- Die Entwicklung des StEPs geschieht in einem Branch, der idealerweise am Hauptprojekt hängen sollte (aber nicht muss) und einer der beiden folgenden Notationen folgen muss:
  <ul><li><code>step-&lt;nummer&gt;</code></li><li><code>&lt;nummer&gt;-beschreibung</code></li></ul>
  Bevorzugt wird die erste Variante, da sie klar erkennen lassen, worauf sich der entsprechende Branch bezieht. Die zweite Variante ist die, die von gitlab erzeugt wird, wenn zu einem Issue über die GUI ein Merge Request erstellt wird.
- Idealerweise sollte frühzeitig ein Merge Request erstellt werden. Solange der StEP in Entwicklung ist, sollte der Merge Request den "Draft"-Status haben, damit man schnell erfassen kann, welche StEPs sich noch in Entwicklung befinden und welche bereits abgeschlossen sind.
- Ist die Entwicklung beendet, sollten die relevanten Label für das Qualitätsmanagement am Issue gesetzt werden, damit die Qualitätsbeauftragten mit dem Review beginnen können. Das Codereview erfolgt am Merge Request und erst, wenn der Merge Request genehmigt wurde, kann das entsprechende Label am Issue geändert werden.
- Für jeden StEP sollte frühzeitig ein Testsystem zur Verfügung gestellt werden, damit die Qualitätsbeauftragten dort testen können.
- Hat ein StEP alle relevanten Qualitätstests durchlaufen, kann der Merge Request in das Hauptsystem (also den Branch `main`) überführt werden.
- Das Issue kann geschlossen werden sobald der Merge Request überführt wurde.
- Werden bei den neu implementierten Funktionen des StEPs Fehler entdeckt und das Issue ist bereits geschlossen, müssen diese als BIEST eingetragen. Das Issue für den StEP muss dann als "Linked Issue" eingetragen werden. Dies geschieht über die GUI, indem man in einem Issue unterhalb des Beschreibungstextes die entsprechende Funktion aufruft. Solange das Issue des StEPs noch offen ist, können Fehler auch dort als Kommentar berichtet werden.

## TICs

Für _Tiny Improvement Commits_ ([TICs](Regeln#tics)) gelten die folgenden Regeln:

- Es muss ein Issue im [Stud.IP-Projekt](https://gitlab.studip.de/studip/studip) erstellt werden.
- Dieses Issue muss das Label "TIC" haben und als Meilenstein muss die Version von Stud.IP angegeben werden, für die der TIC entwickelt wird.
- Optional kann eines der Label "Komponente::xy" angegeben werden falls sich der TIC auf eine Komponente bezieht.
- Die Entwicklung des TIC geschieht in einem Branch, der am Hauptprojekt hängen sollte und einer der beiden folgenden Notationen folgen:
  <ul><li><code>tic-&lt;nummer&gt;</code></li><li><code>&lt;nummer&gt;-beschreibung</code></li></ul>
  Bevorzugt wird die erste Variante, da sie klar erkennen lassen, worauf sich der entsprechende Branch bezieht. Die zweite Variante ist die, die von gitlab erzeugt wird, wenn zu einem Issue über die GUI ein Merge Request erstellt wird.
- Idealerweise sollte frühzeitig ein Merge Request erstellt werden. Solange der TIC in Entwicklung ist, sollte der Merge Request den "Draft"-Status haben, damit man schnell erfassen kann, welche TICs sich noch in Entwicklung befinden und welche bereits abgeschlossen sind.
- Ist die Entwicklung beendet, sollten die relevanten Label für das Qualitätsmanagement am Issue gesetzt werden, damit die Qualitätsbeauftragten mit dem Review beginnen können. Das Codereview erfolgt am Merge Request und erst, wenn der Merge Request genehmigt wurde, kann das entsprechende Label am Issue geändert werden.
- Wurde der Merge Request für den TIC akzeptiert, kann er ab diesem Zeitpunkt in das Hauptsystem (also den Branch `main`) überführt werden.
- Das Issue kann geschlossen werden sobald der Merge Request überführt wurde.
- Werden bei den neu implementieren Funktionen des TICs Fehler entdeckt und das Issue ist bereits geschlossen, müssen diese als BIEST eingetragen. Das Issue für den TIC muss dann als "Linked Issue" eingetragen werden. Dies geschieht über die GUI, indem man in einem Issue unterhalb des Beschreibungstextes die entsprechende Funktion aufruft. Solange das Issue des TICs noch offen ist, können Fehler auch dort als Kommentar berichtet werden.

## Lifters

Für _Laufende, inkrementell fortschreitende Technikrenovierungen für Stud.IP_ ([Lifters](Regeln#lifters)) gelten die folgenden Regeln:

- Es muss ein Issue im [Stud.IP-Projekt](https://gitlab.studip.de/studip/studip) erstellt werden.
- Dieses Issue muss das Label "LifTer" haben. Der Meilenstein bleibt leer, da sich ein Lifters selten innerhalb einer einzigen Version von Stud.IP abschliessen lässt.
- Optional kann eines der Label "Komponente::xy" angegeben werden falls sich der Lifters auf eine Komponente bezieht.
- Die Entwicklung des Lifters erfolgt nun in jeweils einzelnen Issues, die den Regeln von TICs folgen. Die jeweiligen Issues müssen über die Linked Issues mit dem Issue des Lifters verknüft werden, damit man eine Übersicht der für den Lifters erfolgten Änderungen in einem Issue hat.

## Pipelines / CI/CD

Für jeden Commit in jedem Branch des Hauptprojekts wird eine Pipeline angestossen, die folgende Aktionen ausführt:

- Linting/Syntax Check
- Ausführen der Unit Tests

In GitLab können die Ergebnisse der Ausführungen der Pipeline unter dem Punkt [CI/CD](https://gitlab.studip.de/studip/studip/-/pipelines) abgefragt werden. Gibt es zu einem Branch einen Merge Request, so ist das Ergebnis auch dort sichtbar. Schlägt eine Pipeline für den Branch eines Merge Requests fehl, so darf dieser Branch nicht in den Branch `main` überführt werden.

Die Pipelines befinden sich aktuell noch im Aufbau und werden laufend erweitert.

Das Releasemanagement erfolgt auch über eine Pipeline, die angestossen wird, wenn ein entsprechendes Releasetag erstellt wird.

## Mergen von Änderungen in andere Versionen

Eine Übersicht der noch zu transportierenden Bugfixes findet man entweder über die Übersicht der Issues in gitlab oder über das [Dashboard-Plugin im DevBoard](https://develop.studip.de/studip/plugins.php/tractogitlabplugin/merge). Die Überführung der Änderungen erfolgt dabei mittels [Cherry Picking](https://www.atlassian.com/git/tutorials/cherry-pick) der Commits auf die entsprechenden Versionsbranches.

Das Übertragen der Bugfixes kann von jeder Person vorgenommen werden, die Schreibrechte auf die entsprechenden Branches hat. Möchte man dies nicht tun, werden in unregelmässigen Abständen oder auf Zuruf sämtliche aufgelaufenen Änderungen gesammelt in die entsprechenden Versionen überführt.

Beim Portieren der Bugfixes gilt die Regel, dass immer die aktuelle Version von Stud.IP sowie die beiden Versionen davor mit Bugfixes versorgt werden.