---
title: Stud.IP JSON:API
slug: /jsonapi/
sidebar_label: Einführung
---

Willkommen bei der Dokumentation der Stud.IP-JSON:API! Mit dieser API
kann auf viele Daten einer Stud.IP-Installation zugegriffen werden.
Die API verhält sich konform zur <a rel="noopener noreferrer" href="http://jsonapi.org/">JSON:API-Spezifikation</a>.

# Authentifizierung

Stud.IP JSON:API verwendet drei verschiedene Verfahren um Nutzer
zu authentifizieren:

* HTTP Basic access authentication
* Stud.IP-Session-Cookies
* [OAuth2](../functions/oauth2)

Für HTTP-Basic-Access-Authentication benötigt man die Zugangsdaten, die auch
für ein „normales“ Login verwendet werden.

# Paginierung

Viele Routen der Stud.IP JSON:API liefern ihre Ergebnisse seitenweise.
Die zu betrachtende Seite und die Anzahl der Einträge von Seiten
können durch URL-Parameter beeinflusst werden.

Routen, die ihre Ergebnisse seitenweise liefern, enthalten
entsprechende `meta` und `links` in ihren Antworten:

```json title="GET jsonapi.php/v1/courses"
{
  "meta": {
    "page": {
      "offset": 0,
      "limit": 30,
      "total": 347
    }
  },
  "links": {
    "first": "/studip/jsonapi.php/v1/courses?page%5Boffset%5D=0&page%5Blimit%5D=30",
    "last": "/studip/jsonapi.php/v1/courses?page%5Boffset%5D=300&page%5Blimit%5D=30",
    "next": "/studip/jsonapi.php/v1/courses?page%5Boffset%5D=30&page%5Blimit%5D=30"
  },
  "data": [
    "[...]"
  ]
}
```

In diesem Fall wurden alle Veranstaltungen abgefragt, die Route
liefert jedoch nur die erste Seite mit 30 der 347 Einträge zurück.
Unterhalb von `links` werden auf die URLs der ersten, letzten und
nächsten Seite verwiesen. In jedem Fall enthalten diese URLs die
URL-Parameter `page[offset]` und `page[limit]`.

Die Gesamtheit aller Ergebnisse wird auf mehrere Seiten verteilt und
man erhält jeweils nur einen Ausschnitt. Dieser Ausschnitt kann durch
diese URL-Parameter beeinflusst werden

Page-Parameter | Beschreibung
-------------- | ------------
page[offset]   | der Paginierungsoffset
page[limit]    | das Paginierungslimit

Der `page`-Parameter wird der JSON:API-Spezifikation entsprechend verwendet.

```json title="GET jsonapi.php/v1/courses?page[offset]=7&page[limit]=17"
{
  "meta": {
    "page": {
      "offset": 7,
      "limit": 17,
      "total": 347
    }
  },
  "links": {
    "first": "/studip/jsonapi.php/v1/courses?page%5Boffset%5D=0&page%5Blimit%5D=17",
    "last": "/studip/jsonapi.php/v1/courses?page%5Boffset%5D=323&page%5Blimit%5D=17",
    "next": "/studip/jsonapi.php/v1/courses?page%5Boffset%5D=24&page%5Blimit%5D=17"
  },
  "data": [
    "[...]"
  ]
}
```
