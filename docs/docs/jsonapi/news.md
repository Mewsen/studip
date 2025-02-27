---
title: Ankündigungen (News)
---

:::info
  Ankündigungen informieren Stud.IP-Nutzer über neuste Ereignisse rund um
  die Lehre. In Stud.IP können Ankündigungen z.B. systemweit (global) oder für
  einen bestimmten Nutzerkreis erstellt werden.
:::

## Schema "news"

Ankündigungen bestehen aus ihrem Inhalt und einigen Meta-Daten. Die Dauer der
Sichtbarkeit einer Anküdnigung wird durch ihre Attribute publication-start und
end bestimmt (siehe Relationen).

### Attribute

Attribut          | Beschreibung
--------          | ------------
title             | Name einer news
content           | Inhalt einer News
mkdate            | Erstellungs-Datum einer News
chdate            | Datum der letzten Änderung
publication-start | Start der Sichtbarkeit für den Nutzerkreis einer News
publication-end   | Ende der Sichtbarkeit für den Nutzerkreis einer News
comments-allowed  | Bestimmung, ob Kommentare erlaubt sind (Boolean)

Ein Beispiel zum erstellen einer News anhand des Schemas folgt in News anlegen.

### Relationen

 Relation | Beschreibung
--------  | ------------
author    | Ersteller einer News
ranges    | global, institute, semester, course, users

Der Range einer News gibt an wo sie publiziert wird und somit auch für wen
sie sichtbar ist.

## Schema "comments"

Kommentare werden in Stud.IP an eine Ankündigung angehangen, wenn der Ersteller
der News die Erlaubnis vergeben hat.

### Attribute

Attribut          | Beschreibung
--------          | ------------
content           | Inhalt eines Kommentars
mkdate            | Erstellungs-Datum
chdate            | Datum der letzten Änderung

### Relationen

 Relation | Beschreibung
--------  | ------------
author    | Der Ersteller des Kommentars
news      | Die kommentierte News

## News anlegen
  Das Anlegen einer News ist in verschiedenen Kontexten möglich. Sie kann
  global als systemweite News, kursintern oder nutzerbezogen angelegt werden.

## Eine globale News anlegen

### Route
   `POST /news`
### Autorisierung
Die Erstellung einer globalen News erfordern zur Zeit noch Root-Rechte.
Es wird diskutiert ob hier Adminrechte reichen.

   ```shell
   curl --request POST \
       --url https://example.com/news \
       --header "Content-Type: application/vnd.api+json" \
       --header "Authorization: Basic `echo -ne "root@studip:testing" | base64`" \
       --data
       '{"data": {"type": "news","attributes": {"title": "Neue News","comments-allowed": true,"publication-start": "2020-01-01T12:12:12+00:00","publication-end": "2021-01-01T12:12:12+00:00","content": "Eine neue News sieht das Tageslicht."}}}'
   ```

## Eine Kurs-News anlegen

   `POST /courses/{id}/news`

   ```shell
   curl --request POST \
       --url https://example.com/courses/<COURSE-ID>/news \
       --header "Content-Type: application/vnd.api+json" \
       --header "Authorization: Basic `echo -ne "test_dozent:testing" | base64`" \
       --data
       '{"data": {"type": "news","attributes": {"title": "Neue News","comments-allowed": true,"publication-start": "2020-01-01T12:12:12+00:00","publication-end": "2021-01-01T12:12:12+00:00","content": "Eine neue News sieht das Tageslicht."}}}'
   ```
   Parameter | Beschreibung
  ---------- | ------------
  id         | Die ID des Kurses
### Autorisierung
Der Nutzer muss mindestens Dozenten- oder Adminrechte innerhalb des Kurses haben.
## Eine Nutzer-News anlegen
   `POST /users/{id}/news`

   ```shell
   curl --request POST \
       --url https://example.com/users/<USER-ID>/news \
       --header "Content-Type: application/vnd.api+json" \
       --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`" \
       --data
       '{"data": {"type": "news","attributes": {"title": "Neue News","comments-allowed": true,"publication-start": "2020-01-01T12:12:12+00:00","publication-end": "2021-01-01T12:12:12+00:00","content": "Eine neue News sieht das Tageslicht."}}}'
   ```

   Parameter | Beschreibung
  ---------- | ------------
  id         | Die ID des Users
### Autorisierung
  Der Nutzer muss mindestens User-Rechte haben.

## Einen Kommentar anlegen
   `POST /news/{id}/comments`

   ```shell
   curl --request POST \
       --url https://example.com/news/<NEWS-ID>/comments \
       --header "Content-Type: application/vnd.api+json" \
       --header "Authorization: Basic `echo -ne "test_dozent:testing" | base64`" \
       --data
       '{"data": {"type": "comments","attributes": {"content": "Ein Kommentar wurde geupdatet"}}}'
   ```
   ### Autorisierung
     Der Nutzer muss mindestens User-Rechte haben.

   Parameter | Beschreibung
  ---------- | ------------
  id         | Die ID der News
## Eine News ändern
   `PATCH /news/{id}`

   Parameter | Beschreibung
  ---------- | ------------
  id         | Die ID der News

  Die Data-Felder beim Update einer News sind optional.

  ```shell
  curl --request PATCH \
      --url https://example.com/news/<NEWS-ID> \
      --header "Content-Type: application/vnd.api+json" \
      --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`" \
      --data
      '{"data": {"type": "news","attributes": {"title": "Aenderungen","comments-allowed": true,"publication-start": "2020-01-01T12:12:12+00:00","publication-end": "2021-01-01T12:12:12+00:00","content": "Eine News wurde geaendert."}}}'
  ```
### Autorisierung
    Der Nutzer muss Inhaber der News sein oder die entsprechenden Root-Rechte
    besitzen.
## Eine News ansehen
   `GET /news/{id}`

   Parameter | Beschreibung
  ---------- | ------------
  id         | Die ID der News
  ```shell
  curl --request GET \
      --url https://example.com/news/<NEWS-ID> \
      --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`" \
  ```
### Autorisierung
      Der Nutzer muss Inhaber der News sein oder die entsprechenden Range-Rechte
      besitzen.
  > Der Request liefert JSON ähnlich wie dieses:

```json
{
  "data": {
    "type": "news",
    "id": "6a8be7e4859e9c781ecc47a2c3498435",
    "attributes": {
      "title": "A testing title",
      "content": "Lorem ipsum dolor sit amet, consectetur adipisicing elit",
      "mkdate": "2019-04-23T12:10:26+02:00",
      "chdate": "2019-04-23T12:10:26+02:00",
      "publication-start": "2019-04-23T12:10:26+02:00",
      "publication-end": "2019-05-07T12:10:26+02:00",
      "comments-allowed": true
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
      "ranges": {
        "data": [

        ],
        "links": {
          "self": "jsonapi.php/v1/news/6a8be7e4859e9c781ecc47a2c3498435/relationships/ranges"
        }
      }
    },
    "links": {
      "self": "jsonapi.php/v1/news/6a8be7e4859e9c781ecc47a2c3498435"
    }
  }
}
```

## Alle Kurs-News
   `GET /courses/{id}/news`

   Parameter | Beschreibung
  ---------- | ------------
  id         | Die ID des Kurses

  ```shell
  curl --request GET \
      --url https://example.com/course/<COURSE-ID>/news \
      --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`" \
  ```
### Autorisierung
  Der Nutzer muss mindestens Teilnehmer des Kurses sein.

  > Der Request liefert JSON ähnlich wie dieses:

```json
{
  "meta": {
    "page": {
      "offset": 0,
      "limit": 30,
      "total": 4
    }
  },
  "links": {
    "first": "/stud35/plugins.php/argonautsplugin/courses/1b7d3834e42c1569947e0eab7b63ed19/news?page%5Boffset%5D=0&page%5Blimit%5D=30",
    "last": "/stud35/plugins.php/argonautsplugin/courses/1b7d3834e42c1569947e0eab7b63ed19/news?page%5Boffset%5D=0&page%5Blimit%5D=30"
  },
  "data": [
    {
      "type": "news",
      "id": "9dc34d7414e9d6c2789923649a64673e",
      "attributes": {
        "title": "Fakenews",
        "content": "This is fakenews232",
        "mkdate": "2018-06-20T10:40:43+02:00",
        "chdate": "2018-06-20T10:57:37+02:00",
        "publication-start": "2018-06-21T12:00:00+02:00",
        "publication-end": "2066-12-09T22:00:00+01:00",
        "comments-allowed": true
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
        "ranges": {
          "data": [
            {
              "type": "courses",
              "id": "1b7d3834e42c1569947e0eab7b63ed19"
            }
          ],
          "links": {
            "self": "/stud35/plugins.php/argonautsplugin/news/9dc34d7414e9d6c2789923649a64673e/relationships/ranges"
          }
        }
      },
      "links": {
        "self": "/stud35/plugins.php/argonautsplugin/news/9dc34d7414e9d6c2789923649a64673e"
      }
    },
    {
      "type": "news",
      "id": "0e8df7da383d7515c4dc081bfe889897",
      "attributes": {
        "title": "Fakenews",
        "content": "This is fakenews232",
        "mkdate": "2018-06-19T16:08:51+02:00",
        "chdate": "2018-06-20T09:50:02+02:00",
        "publication-start": "2018-06-19T16:08:51+02:00",
        "publication-end": "2066-12-05T15:08:51+01:00",
        "comments-allowed": true
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
        "ranges": {
          "data": [
            {
              "type": "courses",
              "id": "1b7d3834e42c1569947e0eab7b63ed19"
            }
          ],
          "links": {
            "self": "/stud35/plugins.php/argonautsplugin/news/0e8df7da383d7515c4dc081bfe889897/relationships/ranges"
          }
        }
      },
      "links": {
        "self": "/stud35/plugins.php/argonautsplugin/news/0e8df7da383d7515c4dc081bfe889897"
      }
    },
    {
      "type": "news",
      "id": "191ce64590a28b3e038b09e85ea53178",
      "attributes": {
        "title": "Fakenews",
        "content": "This is fakenews",
        "mkdate": "2018-05-08T11:42:11+02:00",
        "chdate": "2018-05-08T11:42:11+02:00",
        "publication-start": "2018-05-08T11:42:11+02:00",
        "publication-end": "2066-09-19T20:24:22+01:00",
        "comments-allowed": true
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
        "ranges": {
          "data": [
            {
              "type": "courses",
              "id": "1b7d3834e42c1569947e0eab7b63ed19"
            }
          ],
          "links": {
            "self": "/stud35/plugins.php/argonautsplugin/news/191ce64590a28b3e038b09e85ea53178/relationships/ranges"
          }
        }
      },
      "links": {
        "self": "/stud35/plugins.php/argonautsplugin/news/191ce64590a28b3e038b09e85ea53178"
      }
    },
    {
      "type": "news",
      "id": "a355b8d628d8656eb93dc527fe1209f3",
      "attributes": {
        "title": "Fakenews",
        "content": "This is fakenews",
        "mkdate": "2018-05-08T11:35:56+02:00",
        "chdate": "2018-05-08T11:35:56+02:00",
        "publication-start": "2018-05-08T11:35:55+02:00",
        "publication-end": "2066-09-19T20:11:50+01:00",
        "comments-allowed": true
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
        "ranges": {
          "data": [
            {
              "type": "courses",
              "id": "1b7d3834e42c1569947e0eab7b63ed19"
            }
          ],
          "links": {
            "self": "/stud35/plugins.php/argonautsplugin/news/a355b8d628d8656eb93dc527fe1209f3/relationships/ranges"
          }
        }
      },
      "links": {
        "self": "/stud35/plugins.php/argonautsplugin/news/a355b8d628d8656eb93dc527fe1209f3"
      }
    }
  ]
}
```


## Alle Nutzer-News
   `GET /users/{id}/news`

   Parameter | Beschreibung
   ---------- | ------------
   id         | Die ID des Nutzers

   ```shell
   curl --request GET \
       --url https://example.com/user/<USER-ID>/news \
       --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`" \
   ```
### Autorisierung
   Der Nutzer muss mindestens eingeloggt sein oder über Root-Rechte verfügen.

   > Der Request liefert JSON ähnlich wie dieses:

```json
{
  "data": {
    "type": "news",
    "id": "0e8df7da383d7515c4dc081bfe889897",
    "attributes": {
    "title": "Neue News",
    "content": "Eine neue News sieht das Tageslicht.",
    "mkdate": "2018-06-19T16:08:51+02:00",
    "chdate": "2018-08-15T14:22:38+02:00",
    "publication-start": "2018-06-19T16:08:51+02:00",
    "publication-end": "2066-12-05T15:08:51+01:00",
    "comments-allowed": true
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
      "ranges": {
        "data": [
        {
          "type": "users",
          "id": "1b7d3834e42c1569947e0eab7b63ed19"
        }
        ],
          "links": {
          "self": "/stud35/plugins.php/argonautsplugin/news/0e8df7da383d7515c4dc081bfe889897/relationships/ranges"
          }
      }
    },
    "links": {
      "self": "/stud35/plugins.php/argonautsplugin/news/0e8df7da383d7515c4dc081bfe889897"
    }
  }
}
```

## Alle News-Kommentare
   GET /news/{id}/comments

   Parameter | Beschreibung
   ---------- | ------------
   id         | Die ID einer News

## Alle globalen News
   `GET /studip/news`

   ```shell
   curl --request GET \
       --url https://example.com/user/studip/news \
       --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`" \
   ```

### Autorisierung

    Der Nutzer muss mindestens eingeloggt sein oder über Root-Rechte verfügen.

> Der Request liefert JSON ähnlich wie dieses:

```json
{
  "data": {
    "type": "news",
    "id": "0e8df7da383d7515c4dc081bfe889897",
    "attributes": {
    "title": "Globale News",
    "content": "Eine neue News sieht das Tageslicht.",
    "mkdate": "2018-06-19T16:08:51+02:00",
    "chdate": "2018-08-15T14:22:38+02:00",
    "publication-start": "2018-06-19T16:08:51+02:00",
    "publication-end": "2066-12-05T15:08:51+01:00",
    "comments-allowed": true
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
      "ranges": {
        "data": [
        {
          "type": "global",
          "id": "studip"
        }
        ],
          "links": {
          "self": "/stud35/plugins.php/argonautsplugin/news/0e8df7da383d7515c4dc081bfe889897/relationships/ranges"
          }
      }
    },
    "links": {
      "self": "/stud35/plugins.php/argonautsplugin/news/0e8df7da383d7515c4dc081bfe889897"
    }
  }
}
```


## Alle News des aktuell eingeloggten Nutzers abrufen
    `GET /news`

  ```shell
  curl --request GET \
      --url https://example.com/news \
      --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`" \
  ```

### Autorisierung

    Der Nutzer muss mindestens eingeloggt sein oder über Root-Rechte verfügen.

> Der Request liefert JSON ähnlich wie dieses:

```json
{
  "data": {
    "type": "news",
    "id": "0e8df7da383d7515c4dc081bfe889897",
    "attributes": {
    "title": "Neue News",
    "content": "Eine neue News sieht das Tageslicht.",
    "mkdate": "2018-06-19T16:08:51+02:00",
    "chdate": "2018-08-15T14:22:38+02:00",
    "publication-start": "2018-06-19T16:08:51+02:00",
    "publication-end": "2066-12-05T15:08:51+01:00",
    "comments-allowed": true
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
      "ranges": {
        "data": [
        {
          "type": "users",
          "id": "1b7d3834e42c1569947e0eab7b63ed19"
        }
        ],
          "links": {
          "self": "/stud35/plugins.php/argonautsplugin/news/0e8df7da383d7515c4dc081bfe889897/relationships/ranges"
          }
      }
    },
    "links": {
      "self": "/stud35/plugins.php/argonautsplugin/news/0e8df7da383d7515c4dc081bfe889897"
    }
  }
}
```


## Eine News löschen
   `DELETE /news/{id}`

   Parameter  | Beschreibung
   ---------- | ------------
   id         | Die ID der News

### Authorisierung

Diese Route kann nur vom Nutzer der betreffenden Nachrichten genutzt werden.

   ```shell
   curl --request DELETE \
       --url https://example.com/news/<NEWS-ID> \
       --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`" \
   ```
 ### Autorisierung

       Der Nutzer muss mindestens eingeloggt sein oder über Root-Rechte verfügen.

## Einen Kommentar löschen
   `DELETE /comments/{id}`

   Parameter  | Beschreibung
   ---------- | ------------
   id         | Die ID eines Kommentars

   ```shell
   curl --request DELETE \
       --url https://example.com/comments/studip/news \
       --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`" \
   ```
## Alle News-Ranges
   `GET /news/{id}/relationships/ranges`

   see http://jsonapi.org/format/#fetching-relationships

## News-Ranges setzen
   `PATCH /news/{id}/relationships/ranges`

   see http://jsonapi.org/format/#crud-updating-to-many-relationships

## News-Ranges hinzufügen
   `POST /news/{id}/relationships/ranges`

   see http://jsonapi.org/format/#crud-updating-to-many-relationships

## News-Ranges löschen
   `DELETE /news/{id}/relationships/ranges`

   see http://jsonapi.org/format/#crud-updating-to-many-relationships
