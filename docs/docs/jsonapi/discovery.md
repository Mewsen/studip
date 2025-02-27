---
title: Discovery
---


Auch wenn JSON:APIs von Haus aus einiges mehr "discoverable" als
herkömmliche REST-APIs sind, schadet es nicht, eine spezielle Route
anzubieten, um alle verfügbaren Routen anzuzeigen.

## Schemata


### Schema "slim-routes"

Ressourcen vom Typ "slim-routes" repräsentieren die aktiven Routen der Stud.IP-JSON:API.

### Attribute

Attribut    | Beschreibung
--------    | ------------
methods     | ein Vektor von HTTP-Verben wie GET, POST, PATCH und DELETE
pattern     | ein URI-Pattern wie "/file-refs/{id}"

### Relationen

keine Relationen vorhanden


## Alle Routen anzeigen
```shell
curl --request GET \
    --url https://example.com/discovery \
    --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`"
```

Mit dieser Route erhält man eine Liste aller aktiven Routen der Stud.IP-JSON:API.

### HTTP Request

`GET /discovery`

### URL-Parameter

keine URL-Parameter

### Autorisierung

Jeder eingeloggte Nutzer darf diese Route aufrufen.
