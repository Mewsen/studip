---
title: Planer
---


In diese Kategorie fällt alles, was mit dem Stundenplan oder dem
Terminkalender zu tun hat. Dabei ist der Stundenplan ein
semesterabhängiger Wochenplan, der wöchentlich wiederkehrende Termine
enthält, die entweder selbst eingetragen wurden oder aufgrund von
besuchten Veranstaltungen dort hinein gelangen.

## Schemata

### Schema "calendar-events"

Ressourcen dieses Typs sind selbst in den Terminkalender eingetragene Termine.


#### Attribute

Attribut    | Beschreibung
--------    | ------------
title       | der Titel des Eintrags
description | die Beschreibung des Eintrags
start       | der Beginn des Eintrags (ISO 8601)
end         | das Ende des Eintrag (ISO 8601)
categories  | die Kategorie des Eintrags (als String)
location    | der Ort des Eintrags
mkdate      | das Erstellungsdatum des Eintrags
chdate      | das Datum der letzten Änderung des Eintrags
recurrence  | Informationen über Wiederholungen des Eintrags

#### Relationen

Relation | Beschreibung
-------- | ------------
owner    | der Nutzer, der den Eintrag erstellt hat

### Schema "course-events"

Ressourcen dieses Typs repräsentieren einmalige Termine von belegten Veranstaltungen.

#### Attribute

Attribut    | Beschreibung
--------    | ------------
title       | der Titel des Eintrags
description | die Beschreibung des Eintrags
start       | der Beginn des Eintrags (ISO 8601)
end         | das Ende des Eintrag (ISO 8601)
categories  | die Kategorie des Eintrags (als String)
location    | der Ort des Eintrags
mkdate      | das Erstellungsdatum des Eintrags
chdate      | das Datum der letzten Änderung des Eintrags
recurrence  | Informationen über Wiederholungen des Eintrags


#### Relationen

Relation | Beschreibung
-------- | ------------
owner    | die **Veranstaltung**, zu der der Eintrag gehört


### Schema "schedule-entries"

Ressourcen dieses Typs stellen Einträge in den Stundenplan dar, die
ein Nutzer selbst eingetragen hat.

#### Attribute

Attribut    | Beschreibung
--------    | ------------
title       | der Titel des Eintrags
description | die Beschreibung des Eintrags
start       | die Uhrzeit des Beginns des Eintrags ("hh:mm")
end         | die Uhrzeit des Endes des Eintrags ("hh:mm")
weekday     | der Wochentag des Eintrags (0-6)
color       | die Farbe des Eintrags ("#rrggbb")

#### Relationen

Relation | Beschreibung
-------- | ------------
owner    | der Nutzer, der den Eintrag erstellt hat


### Schema "seminar-cycle-dates"

Ressourcen dieses Typs stellen Einträge in den Stundenplan dar, die
sich aus den regelmäßigen Terminen einer Veranstaltung zusammensetzen.

#### Attribute

Attribut    | Beschreibung
--------    | ------------
title       | der Titel des Eintrags
description | die Beschreibung des Eintrags
start       | die Uhrzeit des Beginns des Eintrags ("hh:mm")
end         | die Uhrzeit des Endes des Eintrags ("hh:mm")
weekday     | der Wochentag des Eintrags (0-6)
recurrence  | Informationen über Wiederholungen des Eintrags
locations   | alle Orte, an denen dieser Eintrag stattfindet

#### Relationen

Relation | Beschreibung
-------- | ------------
owner    | die **Veranstaltung**, zu der der Eintrag gehört

## Alle Kalendereinträge auslesen

```shell
curl --request GET \
    --url https://example.com/users/<ID>/events \
    --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`"
```

Mit dieser Route kann der Kalender des Nutzer abgefragt werden. Ohne
weitere Parameter werden alle Einträge der nächsten zwei Wochen zurück
geliefert.

### HTTP Request

`GET /users/{id}/events`

Parameter | Beschreibung
--------- | ------------
id        | die ID des Nutzers

### URL-Parameter

Parameter         | Beschreibung
---------         | ------------
filter[timestamp] | Startzeitpunkt der gelieferten Kalendereinträge (als Sekunden seit dem 01.01.1970)

Wenn "filter[timestamp]" nicht gesetzt ist, werden alle
Kalendereinträge der nächsten zwei Wochen zurück geliefert.

Mittels "filter[timestamp]" kann dieser Startzeitpunkt verändert
werden. Es werden jedoch immer Kalendereinträge der nächsten zwei
Wochen ausgeliefert.

### Autorisierung

Jeder Nutzer darf diese Route für sich selbst verwenden. Andere Nutzer
haben nur Zugriff auf ihre eigenen Kalender.


## Alle Kalendereinträge (iCalendar)

:::danger
Diese Route ist keine JSON-API-konforme Route.
:::

```shell
curl --request GET \
    --url https://example.com/users/<ID>/events.ics \
    --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`"
```

Mit dieser Route kann der Kalender des Nutzer abgefragt werden. Die
Daten werden im iCalendar-Datenformat ausgeliefert. Es werden **alle**
Kalendereinträge zurück gegeben.

### HTTP Request

`GET /users/{id}/events.ics`

Parameter | Beschreibung
--------- | ------------
id        | die ID des Nutzers

### URL-Parameter

keine URL-Parameter

### Autorisierung

Jeder Nutzer darf diese Route für sich selbst verwenden. Andere Nutzer
haben nur Zugriff auf ihre eigenen Kalender.


## Alle Termine einer Veranstaltung

```shell
curl --request GET \
    --url https://example.com/courses/<ID>/events \
    --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`"
```

Mit dieser Route können alle Termine einer Veranstaltung abgefragt werden.

### HTTP Request

`GET /courses/{id}/events`

Parameter | Beschreibung
--------- | ------------
id        | die ID der Veranstaltung

### URL-Parameter

Parameter    | Default | Beschreibung
---------    | ------- | ------------
page[offset] | 0       | der Offset (siehe Paginierung)
page[limit]  | 30      | das Limit (siehe Paginierung)


### Autorisierung

Die Termine einer Veranstaltung sind für alle Teilnehmenden sichtbar.


## Stundenplan auslesen
```shell
curl --request GET \
    --url https://example.com/users/<ID>/schedule \
    --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`"
```

Mit dieser Route kann man seinen Stundenplan abfragen. Wird kein
filter-Parameter angegeben, wird der Stundenplan des aktuellen
Semesters ausgeliefert.

### HTTP Request

`GET /users/{id}/schedule`

Parameter | Beschreibung
--------- | ------------
id        | die ID des Nutzers

### URL-Parameter

Parameter         | Default                        |  Beschreibung
---------         | -------                        |  ------------
filter[timestamp] | Beginn des aktuellen Semesters |  Startzeitpunkt des gewünschten Semesters (in Sekunden seit 01.01.1970)

### Autorisierung

Nur der eigene Stundenplan kann ausgelesen werden.


## Eigene Stundenplaneinträge
```shell
curl --request GET \
    --url https://example.com/schedule-entries/<ID> \
    --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`"
```

Mit dieser Route kann man einen einzelnen, selbst verfassten Eintrag
in den Stundenplan auslesen.

### HTTP Request

`GET /schedule-entries/{id}`

Parameter | Beschreibung
--------- | ------------
id        | die ID des Eintrags

### URL-Parameter

keine URL-Parameter

### Autorisierung

Nur der Nutzer, der den Eintrag verfasst hat, darf den Eintrag auch auslesen.

## Regelmäßige Veranstaltungstermine auslesen
```shell
curl --request GET \
    --url https://example.com/seminar-cycle-dates/<ID> \
    --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`"
```

Beschreibung

### HTTP Request

`GET /seminar-cycle-dates/{id}`

Parameter | Beschreibung
--------- | ------------
id        | die ID des Termins

### URL-Parameter

keine URL-Parameter

### Autorisierung

Alle Teilnehmenden einer Veranstaltung können den Termin sehen.
