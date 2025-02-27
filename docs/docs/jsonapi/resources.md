---
title: Räume/Gebäude
---


In dieser Kategorie ist alles versammelt, was mit Ressourcenverwaltung zu tun hat.


## Schema "resources-objects"

Alle Ressourcenobjekte der Ressourcenverwaltung werden mit diesem Schema repräsentiert.

### Attribute

Attribut        | Beschreibung
--------        | ------------
name            | der Name der Ressource
description     | die Beschreibung der Ressource
is-room         | Handelt es sich bei dieser Ressource um einen Raum?
multiple-assign | Darf diese Ressource zeitgleich mehrfach belegt werden?
requestable     | Kann man zu dieser Ressource eine Raumanfrage stellen?
lockable        | Ist diese Ressource betroffen von einer globalen Sperrzeit?
mkdate          | Erstellungsdatum
chdate          | Änderungsdatum

### Relationen

Relation  | Beschreibung
--------  | ------------
category  | Kategorie der Ressource


## Schema "resources-categories"

Dieses Schema beschreibt Ressourcenarten.

### Attribute

Attribut    | Beschreibung
--------    | ------------
name        | der Name der Art
description | die Beschreibung der Art
system      |
is-room     | Handelt es sich bei dieser Art um einen Raum?
icon        | Nummer des zu verwendenden Icons

### Relationen

keine Relationen


## Schema "resources-assign-events"

Alle Ressourcenbelegungen werden mit diesem Schema abgebildet.

### Attribute

Attribut        | Beschreibung
--------        | ------------
repeat-mode     | in welchem Abstand und in welcher Frequenz wird diese Ressourcenbelegung ausgeführt
start           | das Datum des Beginns der Belegung
end             | das Datum des Endes der Belegung
owner-free-text | Freitextangabe für den Besitzer dieser Belegung

### Relationen

Relation         | Beschreibung
--------         | ------------
owner            | (optional) der Besitzer der Belegung
resources-object | die belegte Ressource


## Alle Ressourcen
```shell
curl --request GET \
    --url https://example.com/resources-objects \
    --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`"
```

Diese Route liefert alle Ressourcenobjekte.

### HTTP Request

`GET /resources-objects`

### URL-Parameter

keine URL-Parameter

### Autorisierung

Jeder eingeloggte Nutzer kann die Liste der Ressourcenobjekte sehen.


## Alle Belegungen einer Ressource
```shell
curl --request GET \
    --url https://example.com/resources-objects/<ID>/assignments \
    --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`"
```

Mit dieser Route können die Belegungen einer Ressource abgefragt werden.

### HTTP Request

`GET /resources-objects/{id}/assignments`

Parameter | Beschreibung
--------- | ------------
id        | die ID des Ressourcenobjekts

### URL-Parameter

Parameter     | Default  | Beschreibung
---------     | -------  | ------------
filter[start] | (heute)  | optional; Zeitpunkt (in Sekunden seit 1.1.1970), ab dem die Belegungen angezeigt werden sollen
filter[end]   | (morgen)  | optional; Zeitpunkt (in Sekunden seit 1.1.1970), bis zu dem die Belegungen angezeigt werden sollen

Die Parameter "filter[start]" und "filter[end]" müssen als Integer angegeben werden (Sekunden seit 1.1.1970 00:00:00 UTC). Werden sie nicht angegeben, werden die Belegungen des heutigen Tages angezeigt.

### Autorisierung

Jeder eingeloggte Nutzer kann die Liste der Ressourcenbelegungen sehen.
