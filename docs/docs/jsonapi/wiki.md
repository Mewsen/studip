---
title: Wiki
---

:::info
Wikis bieten Dozenten und Studenten die Möglichkeit,
gemeinsame Dokumente zu erstellen. Zu verschiedenen Themen in der
Veranstaltungen können Wikis erstellt und gepflegt werden. Eine
Versionskontrolle ermöglicht, sich Änderungen zwischen den
Dokumentenversionen anzuschauen.
:::

## Schema "wiki-pages"
Neben dem Inhalt und Namen enthält jede Wiki-Seite Metadaten zur aktuellen Version.
### Attribute

Attribut | Beschreibung
-------- | ------------
keyword  | Der Name der Wiki-Seite
content  | Der Inhalt der Wiki-Seite
chdate   | Das Datum der letzten Änderung
version  | Die aktuelle Versionsnummer

### Relationen

 Relation | Beschreibung
--------  | ------------
author    | Der Verfasser der Wiki-Seite
range     | Der Range einer Wiki-Seite ist der entsprechende Kurs

## Wiki-Seiten eines Kurses
   `GET /courses/{id}/wiki-pages`

### Parameter

   Parameter | Beschreibung
  ---------- | ------------
  id         | Die ID des Kurses

  ```shell
  curl --request GET \
      --url https://example.com/courses/<COURSE-ID>/wiki-pages \
      --header "Content-Type: application/vnd.api+json" \
      --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`" \
  ```
### Autorisierung
      Der Nutzer sollte Mitglied des entsprechenden Kurses sein.

> Der Request liefert JSON ähnlich wie dieses:

```json
{
  "meta": {
    "page": {
      "offset": 0,
      "limit": 30,
      "total": 2
    }
  },
  "links": {
    "first": "/?page[offset]=0&page[limit]=30",
    "last": "/?page[offset]=0&page[limit]=30"
  },
  "data": [
    {
      "type": "wiki-pages",
      "id": "a07535cf2f8a72df33c12ddfa4b53dde_ulyq",
      "attributes": {
        "keyword": "ulyq",
        "content": "Es gibt im Moment in diese Mannschaft, oh, einige Spieler vergessen ihren Profi was sie sind. Ich lese nicht sehr viele Zeitungen, aberich habe geh\u00f6rt viele Situationen. Erstens: Wir haben nicht offensivgespielt.",
        "chdate": "2019-04-23T12:10:26+02:00",
        "version": 1
      },
      "relationships": {
        "author": {
          "data": {
            "type": "users",
            "id": "e7a0a84b161f3e8c09b4a0a2e8a58147"
          },
          "links": {
            "related": "jsonapi.php/v1/users/e7a0a84b161f3e8c09b4a0a2e8a58147"
          }
        },
        "range": {
          "data": {
            "type": "courses",
            "id": "a07535cf2f8a72df33c12ddfa4b53dde"
          },
          "links": {
            "related": "jsonapi.php/v1/courses/a07535cf2f8a72df33c12ddfa4b53dde"
          }
        }
      }
    },
    {
      "type": "wiki-pages",
      "id": "a07535cf2f8a72df33c12ddfa4b53dde_yxilo",
      "attributes": {
        "keyword": "yxilo",
        "content": "Es gibt im Moment in diese Mannschaft, oh, einige Spieler vergessen ihren Profi was sie sind. Ich lese nicht sehr viele Zeitungen, aberich habe geh\u00f6rt viele Situationen. Erstens: Wir haben nicht offensivgespielt.",
        "chdate": "2019-04-23T12:10:26+02:00",
        "version": 1
      },
      "relationships": {
        "author": {
          "data": {
            "type": "users",
            "id": "e7a0a84b161f3e8c09b4a0a2e8a58147"
          },
          "links": {
            "related": "jsonapi.php/v1/users/e7a0a84b161f3e8c09b4a0a2e8a58147"
          }
        },
        "range": {
          "data": {
            "type": "courses",
            "id": "a07535cf2f8a72df33c12ddfa4b53dde"
          },
          "links": {
            "related": "jsonapi.php/v1/courses/a07535cf2f8a72df33c12ddfa4b53dde"
          }
        }
      }
    },
    "[...]"
  ]
}
```

## Wiki-Seite

Gibt eine Wiki-Seite zurück.

   `GET /wiki-pages/{id}`

   Parameter | Beschreibung
  ---------- | ------------
  id         | Die ID der Wiki-Seite


  ```shell
  curl --request GET \
      --url https://example.com/wiki-pages/<ID> \
      --header "Content-Type: application/vnd.api+json" \
      --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`" \
      --data
  ```
### Autorisierung
        Der Nutzer sollte Mitglied des entsprechenden Kurses sein.
  > Der Request liefert JSON ähnlich wie dieses:

```json
{
  "data": {
    "type": "wiki",
    "id": "48101a5a47c34f80999cc01266b32536_tastyTest",
    "attributes": {
      "keyword": "tastyTest",
      "content": "This is dsdsadsad",
      "chdate": "2018-06-05T14:12:29+02:00",
      "version": 1
    },
    "relationships": {
      "author": {
        "data": {
          "type": "users",
          "id": "76ed43ef286fb55cf9e41beadb484a9f"
        },
        "links": {
          "related": "/stud35/plugins.php/argonautsplugin/users/76ed43ef286fb55cf9e41beadb484a9f"
        }
      },
      "range": {
        "data": {
          "type": "courses",
          "id": "48101a5a47c34f80999cc01266b32536"
        },
        "links": {
          "related": "/stud35/plugins.php/argonautsplugin/courses/48101a5a47c34f80999cc01266b32536"
        }
      }
    },
    "links": {
      "self": "/stud35/plugins.php/argonautsplugin/courses/48101a5a47c34f80999cc01266b32536/wiki/tastyTest"
    }
  }
}
```


## Wiki-Seite anlegen

Legt eine Wiki-Seite an.

   `POST /courses/{id}/wiki-pages`

   Parameter | Beschreibung
  ---------- | ------------
  id         | Die ID der Veranstaltung

  ```shell
  curl --request POST \
      --url https://example.com/courses/<COURSE-ID>/wiki \
      --header "Content-Type: application/vnd.api+json" \
      --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`" \
      --data
      '{"data": {"type": "wiki-pages","attributes": {"keyword": "testing","content": "wiki created"}}}'
  ```
### Autorisierung
          Der Nutzer sollte Mitglied des entsprechenden Kurses sein.


## Wiki-Seite ändern

Aktualisiert eine Wiki-Seite.

   `PATCH /wiki-pages/{id}`

   Parameter | Beschreibung
  ---------- | ------------
  id         | Die ID der Wiki-Seite

```
curl --request PATCH \
      --url https://example.com/wiki-pages/<ID> \
      --header "Content-Type: application/vnd.api+json" \
      --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`" \
      --data
      '{"data": {"type": "wiki-pages","attributes": {"content": "wiki changed"}}}'
```

### Autorisierung
Der Nutzer sollte Mitglied des entsprechenden Kurses sein.
