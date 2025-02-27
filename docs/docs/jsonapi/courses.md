---
title: Veranstaltungen
---

:::info
Veranstaltungen sind Gruppen für Seminare, Vorlesungen,
Übungen etc. Innerhalb von Veranstaltungen werden Materialien geteilt,
Plugins verwendet, Termine festgelegt uvm. Viele Funktionen von
Stud.IP sind nur für eine bestimmte Veranstaltung sichtbar.
:::

## Schema "courses"

### Attribute

Attribut      | Beschreibung
------------- | ------------
course-number | ID des Kurses
title         | Titel des Kurses
subtitle      | Untertitel des Kurses
course-type   | Art des Kurses (Seminar, Vorlesung...)
description   | Beschreibung des Kurses
location      | Ort der Veranstaltung
miscellaneous | sonstiges


### Relationen

 Relation        | Beschreibung
---------------- | ------------
institute        | Die zugewiesene Institution
start-semester   | Anfangs-Semester der Veranstaltung
end-semester     | End-Semester der Veranstaltung
files            | Referenz auf Files innerhalb der Veranstaltung
documents        | Referenz auf Dokumente innerhalb der Veranstaltung
document-folders | Ordner für Dateien innerhalb der Veranstaltung

## Schema "course-memberships"

Zeigt die Teilnahme an einer Veranstaltung mit Ihrer Rolle an.

### Attribute

Attribut      | Beschreibung
------------- | ------------
permission    | Rolle des Nutzers (Autor, Dozent, etc...)
position      | Anordnung in der Teilnehmer-Liste
group         | Anordnung in der Teilnehmer-Liste
mkdate        | Erstellungsdatum
label         | die "Funktion" des Teilnehmers (s. Weboberfläche)
notification  | Bekomme ich einmal am Tag eine E-Mail-Benachrichtigung über neue Inhalte in dieser Veranstaltung?
comment       | Teilnehmerkommentar für Lehrende
visible       | Sichtbarkeit im Kurs

Das Feld "visible" ist nur für einen selbst bzw. die Lehrenden der
Veranstaltung zu sehen.

### Relationen

 Relation        | Beschreibung
---------------- | ------------
course           | Die Veranstaltung für die Teilnehmer
user             | Nutzer der Veranstaltung

### URL-Parameter

Parameter        | Default | Beschreibung
---------        | ------- | ------------
page[offset]     | 0       | der Offset (siehe Paginierung)
page[limit]      | 30      | das Limit (siehe Paginierung)
filter[q]        | -       | ein Suchbegriff (mind. 3 Zeichen)
filter[fields]   | all     | in welchen Feldern gesucht werden soll
filter[semester] | all     | in welchem Semester gesucht werden soll

Der Parameter "filter[fields]" darf folgende Werte annehmen: 'all', 'title_lecturer_number', 'title', 'sub_title', 'lecturer', 'number', 'comment', 'scope'.

## Alle Veranstaltungen
Mit dieser Route können alle Veranstaltungen ausgelesen werden.

### HTTP Request
  `GET /courses`

### Parameter

Diese Route benötigt keine Parameter


### Autorisierung

Jeder eingeloggte Nutzer kann diese Route verwenden.

### Parameter

Parameter |  Beschreibung
--------- | -------
id        | ID des Kurses

### Autorisierung

Jeder Teilnehmer des Kurses kann diese Route nutzen.

```shell
curl --request GET \
    --url https://example.com/courses \
    --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`"
```

## Eine Veranstaltung

Gibt eine Veranstaltung wieder.

### HTTP Request
   `GET /courses/{id}`

### Parameter

Parameter |  Beschreibung
--------- | -------
id        | ID des Kurses

### Autorisierung

Jeder Teilnehmer des Kurses oder Root kann diese Route nutzen.

   ```shell
   curl --request GET \
       --url https://example.com/courses/<course-id> \
       --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`"
   ```

## Alle Veranstaltungen eines Nutzers
Gibt alle Veranstaltungen eines Nutzers zurück.

### HTTP Request

   `GET /users/{id}/courses`

### Parameter

Parameter |  Beschreibung
--------- | -------
id        | ID des Nutzers

### Autorisierung

Jeder eingeloggte Nutzer kann diese Route nutzen.

   ```shell
   curl --request GET \
       --url https://example.com/users/<user-id>/courses \
       --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`"
   ```

## Teilnahmen einer Veranstaltung

Gibt alle Kurse mit dem jeweiligen Teilnehmerstatus eines Nutzers zurück.

### HTTP Request
   `GET /courses/{id}/memberships`

### Parameter

Parameter |  Beschreibung
--------- | -------
id        | ID des Kurses

### URL-Parameter

Parameter          | Default | Beschreibung
---------          | ------- | ------------
filter[permission] | -       | Rolle des Nutzers in der Veranstaltung

### Autorisierung

Nutzer mit mindestens Adminstatus oder Teilnehmer des Kurses können diese Route benutzen.

   ```shell
   curl --request GET \
       --url https://example.com/courses/<course-id>/memberships \
       --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`"
   ```

## IDs der Teilnahmen

Gibt die Referenzen auf die Teilnehmer eines Kurses zurück.

### HTTP-Request
   `GET /courses/{id}/relationships/memberships`

### Parameter

Parameter |  Beschreibung
--------- | -------
id        | ID des Kurses

### Autorisierung

Nutzer mit mindestens Adminstatus oder Teilnehmer des Kurses können diese Route benutzen.

   ```shell
   curl --request GET \
       --url https://example.com/courses/<course-id>/relationships/memberships \
       --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`"
   ```

## Eine Teilnahme auslesen

   ```shell
   curl --request GET \
       --url https://example.com/course-memberships/<ID> \
       --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`"
   ```

Gibt eine Teilnahme wieder.

### HTTP Request
   `GET /course-memberships/{id}`

### Parameter

Parameter |  Beschreibung
--------- | -------
id        | ID der Teilnahme

### Autorisierung

Nur der Teilnehmer selbst kann die Teilnahme auslesen


## Eine Teilnahme ändern

```shell
   curl --request PATCH \
       --url https://example.com/course-memberships/<ID> \
       --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`" \
       --header "Content-Type: application/vnd.api+json" \
       --data '{"data": {
           "type": "course-memberships",
           "id": "<ID>",
           "attributes": {"group":2,"visible":"no"}
       }}'
```

Mit dieser Route kann man die Attribute einer Teilnahme an einer
Veranstaltung ändern.

### HTTP Request
   `PATCH /course-memberships/{id}`

### Parameter

Parameter |  Beschreibung
--------- | -------
id        | ID der Teilnahme

### Autorisierung

Nur der Teilnehmer selbst kann die Teilnahme ändern.
