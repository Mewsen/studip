---
title: Stud.IP-System
---

Alle Routen, die mit dem Stud.IP-System an sich zu tun haben, sind in dieser Kategorie versammelt.

## Schemata

### Schema "studip-properties"

Ausgewählte Konfigurationseinstellungen und Merkmale der Stud.IP-Installation werden mit diesem Schema abgebildet.

### ID

Die ID der Einstellung ist kein MD5-Hash sondern ein festes Kürzel für eine Einstellung/ein Merkmal.

### Attribute

Attribut    | Beschreibung
--------    | ------------
description | eine Beschreibung der Einstellung/des Merkmals
value       | der Wert der Einstellung/des Merkmals

### Relationen

keine Relationen vorhanden

## Alle Stud.IP-Properties auslesen
```shell
curl --request GET \
    --url https://example.com/studip/properties \
    --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`"
```

Liefert alle Stud.IP-Einstellungen/Merkmale

### HTTP Request

`GET /studip/properties`


### URL-Parameter

keine URL-Parameter

### Autorisierung

Jeder eingeloggte Nutzer darf diese Route aufrufen.
