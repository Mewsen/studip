---
id: etask-tables
title: Beschreibung der `etask_*`-Tabellen
sidebar_label: etask_* - Tabellen
---

## etask_tasks - Aufgaben
Ab Stud.IP 4.0 gibt es gemeinsame Tabellen für Aufgaben in Stud.IP, die von verschiedenen Tools (und auch Plugins) verwendet werden können. Da viele Tools spezifische Zusatzinformationen ablegen wollen, gibt es in vielen Tabellen ein zusätzliches Feld `options`, das (fast) beliebige weitere Daten im JSON-Format aufnehmen kann. Für darüber hinaus gehende Anforderungen können natürlich auch weiterhin eigene Tabellen angelegt werden.

Die Tabelle `etask_tasks` enthält die einzelnen Aufgaben. Eine Aufgabe kann dabei in verschiedenen Kontexten verwendet werden, daher stehen z.B. die Informationen zur Bewertung (falls es so etwas gibt) nicht direkt bei der Aufgabe. Der Typ sollte eigentlich ein PHP-Klassenname sein, allerdings gibt es im Kern noch keine definierten Klassen für die Aufgabentypen, daher gibt es da aktuell nur den Typ "`multiple-choice`".

| Attribut | Beschreibung |
| ---- | ---- |
| **id** | ID der Aufgabe (Primärschlüssel, Auto-Increment) |
| **type** | Aufgabentyp (Multiple-Choice, Text, Zuordnung o.ä.), geplant: PHP-Klassenname |
| **title** | Aufgabentitel (ohne Formatierung) |
| **description** | Aufgabentext (mit Formatierung) |
| **task** | Aufgabeninhalte in einer typspezifischen JSON-Repräsentierung (siehe Beispiele unten) |
| **user_id** | Ersteller der Aufgabe |
| **mkdate** | Erstellungsdatum |
| **chdate** | Änderungsdatum |
| **options** | weitere, nicht typspezifische Daten im JSON-Format (nach Bedarf) |


## etask_task_tags - Schlüsselworte für Aufgaben

Falls ein Tool Schlüsselworte zu Aufgaben zuweisen möchte, kann dazu die Tabelle `etask_task_tags` verwendet werden. Jeder Nutzer sieht dabei nur seine eigenen Schlüsselworte.

| Attribut | Beschreibung |
| ---- | ---- |
| **task_id** | ID der Aufgabe |
| **user_id** | Nutzer des Schlüsselworts |
| **tag** | Schlüsselwort|


## etask_tests - Aufgabensammlungen

Aufgaben können zu Aufgabensammlungen zusammengefaßt werden (etwa so wie Dateien in Ordnern). Jede Aufgabensammlungen ist in der Tabelle `etask_test` abgelegt.

| Attribut | Beschreibung |
| ---- | ---- |
| **id** | ID der Aufgabensammlung (Primärschlüssel, Auto-Increment) |
| **title** | Titel der Aufgabensammlung (ohne Formatierung) |
| **description** | Beschreibung der Aufgabensammlung (mit Formatierung) |
| **user_id** | Ersteller der Aufgabensammlung |
| **mkdate** | Erstellungsdatum |
| **chdate** | Änderungsdatum |
| **options** | weitere Daten im JSON-Format (nach Bedarf) |

## etask_test_tags - Schlüsselworte für Aufgabensammlungen

Falls ein Tool Schlüsselworte zu Aufgabensammlung zuweisen möchte, kann dazu die Tabelle `etask_test_tags` verwendet werden. Jeder Nutzer sieht dabei nur seine eigenen Schlüsselworte.

| Attribut | Beschreibung |
| ---- | ---- |
| **test_id** | ID der Aufgabensammlung |
| **user_id** | Nutzer des Schlüsselworts |
| **tag** | Schlüsselwort |


## etask_test_tasks - Zuordnung der Aufgaben zu den Aufgabensammlungen

Die Tabelle `etask_test_tasks` speichert die Zuordnung der einzelnen Aufgaben zu den Aufgabensammlungen sowie ggf. weitere an der Zuordnung hängende Informationen wie die Punktzahl.

| Attribut | Beschreibung |
| ---- | ---- |
| **test_id** | ID der Aufgabensammlung |
| **task_id** | ID der Aufgabe |
| **position** | Position in der Sammlung (numeriert ab 1) |
| **points** | erreichbare Punkte (optional) |
| **options** | weitere Daten im JSON-Format (nach Bedarf) |
 

## etask_assignments - gestellte Aufgabensammlungen (Aufgabenblätter)

Eine Aufgabensammlung ist zunächst mal nur eine Menge von Aufgaben ohne Zuordnung. Über ein `etask_assignments` wird die Aufgabensammlung einer Menge von Teilnehmern gestellt (z.B. in Form eines Aufgabenblatts oder eine Abstimmung). Bei einer Einfachzuordnung zu einem Kontext steht die Zuordnung direkt in dieser Tabelle, bei Mehrfachzuordnung in der Tabelle `etask_assignment_ranges`.

| Attribut | Beschreibung |
| ---- | ---- |
| **id** | ID des Aufgabenblatts (Primärschlüssel, Auto-Increment) |
| **test_id** | ID der Aufgabensammlung |
| **range_type** | Kontext-Typ, aktuell sind definiert: `course`, `global`, `group`, `institute`, `user` |
| **range_id** | Kontext der Zuordnung, z.B. die ID einer Veranstaltung |
| **type** | Präsentationsmodus als Text, vom Tool abhängig (nicht vordefiniert) |
| **start** | Start des Bearbeitungszeitraums (optional) |
| **end**| Ende des Bearbeitungszeitraums (optional) |
| **active**| sichtbar/unsichtbar für Teilnehmer, kann z.B. für einen Entwurfsmodus verwendet werden |
| **options**| weitere Daten im JSON-Format (nach Bedarf) |

## etask_assignment_ranges -Mehrfachzuordnung von Aufgabensammlungen zu Kontexten

Wenn Aufgabensammlungen mehreren Kontexten zugewiesen werden sollen, sind diese in `etask_assignment_ranges` abgelegt.

| Attribut | Beschreibung |
| ---- | ---- |
| **id** | ID der Zuordnung (Primärschlüssel, Auto-Increment) |
| **assignment_id** | ID des Aufgabenblatts |
| **range_type** | Kontext-Typ, aktuell sind definiert: `course`, `global`, `group`, `institute`, `user` |
| **range_id** | Kontext der Zuordnung, z.B. die ID einer Veranstaltung |
| **options** | weitere Daten im JSON-Format (nach Bedarf) |

## etask_assignment_attempts - individueller Lösungsversuch zu einem Aufgabenblatt

In `etask_assignment_attempts` wird pro Teilnehmer abgelegt, ob ein Aufgabenblatt bereits angefangen wurde (und wann), und ggf. auch ein indivduelles Enddatum der Bearbeitung gespeichert. Zusätzlich können auch weitere Informationen je nach Tool gespeichert werden.

| Attribut | Beschreibung |
| ---- | ---- |
| **id** | ID des Lösungsversuchs (Primärschlüssel, Auto-Increment) |
| **assignment_id** | ID des Aufgabenblatts |
| **user_id** | Teilnehmer |
| **start** | individueller Start der Bearbeitung (optional) |
| **end** | individuelles Ende der Bearbeitung (optional) |
| **options** | weitere Daten im JSON-Format (nach Bedarf) |

## etask_responses - Antworten bzw. Lösungen zu einzelnen Aufgaben

Die Antworten auf die einzelnen Aufgaben werden in der Tabelle `etask_responses` abgelegt. Die Antworten selbst sind natürlich typspezifisch und werden wie die Aufgabeninhalte in einem JSON-Fomat gespeichert.

| Attribut | Beschreibung |
| ---- | ---- |
| **id** | ID der Antwort (Primärschlüssel, Auto-Increment) |
| **assignment_id** | ID des Aufgabenblatts |
| **task_id** | ID der Aufgabe |
| **user_id** | Teilnehmer |
| **response** | Antwort im JSON-Format (abhängig vom Aufgabentyp) |
| **state** | Status (numerisch, vom Tool zu definieren) |
| **points** | Bewertung in Punkten (optional) |
| **feedback** | Feedback zur Antwort (mit Formatierung) |
| **grader_id** | Nutzer-ID des Feedback-Gebers |
| **mkdate** | Erstellungsdatum |
| **chdate** | Änderungsdatum |
| **options** | weitere Daten im JSON-Format (nach Bedarf) |


## JSON-Formate für Aufgabentypen

In diesem Abschnitt ist das JSON-Format der wichtigsten Aufgabentypen definiert. Zu beachten ist, daß der Aufgabentext nicht Teil dieser JSON-Beschreibung ist, sondern direkt in der Spalte `description` in der Tabelle `etask_tasks` gespeichert ist. Für weitere Aufgabentypen müßte die Auflistung hier entsprechend ergänzt werden.

#### Multiple-Choice

Es gibt ein gemeinsames Schema für alle Arten von Aufgaben mit Antwortwahl (mit Ausnahme des Lückentexts).

Beispiel:
```json
{
   "select":"multiple",
   "optional":false,
   "answers":[
      {
         "text":"Ist der Himmel blau?",
         "score":1,
         "feedback":"..."
      },
      {
         "text":"Findet man am Ende des Regenbogens einen Topf voll Gold?",
         "score":0,
         "feedback":"..."
      }
   ]
}
```

| Attribut | Beschreibung |
| ---- | ---- |
| **select** | `single` oder `multiple` |
| **optional** | `true` (Antwort ist optional) oder `false` (Antwort muß gegeben werden) |
| **answers** | Liste der Antwortmöglichkeiten (ggf. inkl. automatischem Feedback) |

Antwort:
```json
[
    1, 0
]
```

Wenn eine Frage unbeantwortet bleibt (optionale Antwort), wird ein Wert von `-1` eingetragen.

#### Textaufgaben

Für Textaufgaben gibt es ein Schema ähnlich wie bei den Multiple-Choice mit kleinen Erweiterungen. Die vordefinierten Antworten sind hier aber keine Antwortoptionen zur Auswahl, sondern die automatisch auswertbaren Antworten mit entsprechender Bewertung (die Liste kann leer sein, wenn es keine Auswertung gibt).

Beispiel:
```json
{
   "layout":"textarea",
   "template":"...",
   "compare":"ignorecase",
   "answers":[
      {
         "text":"Paris",
         "score":1,
         "feedback":"..."
      }
   ]
}
```

| Attribut | Beschreibung |
| ---- | ---- |
| **layout** | `input` (einzeilig) oder `textarea` (mehrzeilig) |
| **template** | initiale Textvorgabe für die Lösung der Teilnehmer |
| **compare** | Vergleichskriterum für die Auswertung, z.B. `ignorecase` oder `levenshtein` |
| **answers** | Liste der automatisch ausgewerteten Antworten (ggf. inkl. automatischem Feedback) |


Antwort:
```json
[
"foobar"
]
```

#### Lückentexte

Beim Lückentext sind die Antworten getrennt vom eigentlichen Lückentext abgelegt, in dem nur die Lücken ausgezeichnet sind. Eine Lücke mit dabei im Text mit `[=[]()=]` markiert. Der Lückentext kann dabei auch formatiert sein (Stud.IP-Formatierung bzw. WYSIWYG-Editor).

Beispiel:
```json
{
   "text":"Die Vase steht []() dem []().",
   "select":true,
   "compare":"ignorecase",
   "answers":[
      [
         {
            "text":"auf",
            "score":1
         },
         {
            "text":"neben",
            "score":0,
            "feedback":"..."
         },
         {
            "text":"unter",
            "score":0,
            "feedback":"..."
         }
      ],
      [
         {
            "text":"Stuhl",
            "score":0
         },
         {
            "text":"Tisch",
            "score":1
         },
         {
            "text":"Teppich",
            "score":0
         }
      ]
   ]
}
```


| Attribut | Beschreibung |
| ---- | ---- |
| **text** | Text des Lückentexts (mit Formatierung), Lücken sind mit `[=[]()=]` markiert |
| **select** | `true` (Auswahl aus Liste) oder `false` (Eingabe als Text) |
| **compare** | Vergleichskriterum für die Auswertung, z.B. `ignorecase` oder `levenshtein` |
| **answers** | Liste der Antwortmöglichkeiten bzw. der automatisch ausgewerteten Antworten (ggf. inkl. automatischem Feedback) |


Antwort;
```json
[
"auf", "Tisch"
]
```

#### Zuordnungen

Zuordnungen bestehen aus einer Liste von Gruppen (Kategorien) und einer Liste von Antworten, die diesen Gruppen zugeordnet werden können. Es kann Antworten geben, die unzugeordnet bleiben müssen - diese bekommen die Gruppe mit dem Index "`-1`" zugewiesen.

Beispiel:
```json
{
    "select":"single",
    "groups":[
        "Instrument",
        "Werkzeug"
    ],
    "answers":[
        {
            "id":42,
            "text":"Hammer",
            "group":1
        },
        {
            "id":7,
            "text":"Geige",
            "group":0
        }
    ]
}
```


| Attribut | Beschreibung |
| ---- | ---- |
| **select** | (Einfachzuordnung) oder `multiple` (Mehrfachzuordnung in jeder Gruppe) |
| **groups** | Liste der Gruppen für die Zuordnung (numeriert ab 0) |
| **answers** | Liste der Antwortmöglichkeiten (ggf. inkl. automatischem Feedback) und Gruppenzuordnung |

Antwort:
```json
{
"42": 0, "7": 1
}
```

Wenn eine Antwort nicht zugeordnet bleibt, wird ein Wert von "`-1`" eingetragen.
