---
title: Semester
---

Semester geben einen bestimmten Studien-Zeitraum in Stud.IP an.
Sie dienen auch als Filter für Veranstaltungen und Suchen.

## Schema "semesters"

Neben dem Titel und der Beschreibung beinhalten Semester Meta-Daten über Start- und End-Zeitpunkt des Semesters
### Attribute

Attribut    | Beschreibung
--------    | ------------
title       | Name des Semesters
description | Weitere Angaben zum Semester
start       | Startzeitpunkt
end         | Endzeitpunkt

### Relationen

keine

## Alle Semester
   GET /semesters

   ```shell
   curl --request GET \
       --url https://example.com/semesters \
       --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`" \
   ```

## Ein Semester
   GET /semesters/{id}

   ```shell
   curl --request GET \
       --url https://example.com/semesters/<semester-id> \
       --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`" \
   ```
