---
title: Forum
---


Das Stud.IP-Forum bietet die Möglichkeit Beiträge zu erstellen, zu kommentieren
und zu Kategorisieren. Jedes Forum ist an genau eine Veranstaltung gebunden.
Die Schema's werden in Forum-Categories und Forum-Entries unterteilt.

## Schema "forum-categories"

Kategorien für Einträge haben einen Namen und geben die Hierarchie des Forums
anhand ihrer Position an.

### Attribute

Attribut          | Beschreibung
--------          | ------------
title             | Name einer Kategorie
position          | Position einer Kategorie

### Relationen

Relation | Beschreibung
--------  | ------------
course    | Der Kurs des Forums, indem die Kategorie angelegt ist
entries   | Alle Forum-Einträge einer Forum-Kategorie

## Schema "forum-entries"

Einträge des Forums liegen auf verschiedenen Ebenen. Sie können direkt in
Kategorien als Themen erstellt werden oder an vorhandene Einträge angebunden
werden.

### Attribute

Attribut          | Beschreibung
--------          | ------------
title             | Name eines Entries (sollte nur bei Themen angezeigt werden)
content           | Gibt den Inhalt eines Entries wieder
area              | Dieses Attribut wird mitgeführt (ist aber idr. '0')

### Relationen

Relation | Beschreibung
--------  | ------------
category  | Die Forum-Kategorie des Forumeintrags
entries   | Alle Untereinträge eines Forumeintrags

## Alle Forum-Kategorien eines Kurses auslesen
   GET /courses/{id}/forum-categories

   Parameter | Beschreibung
  ---------- | ------------
  id         | Die ID des Kurses

   ```shell
   curl --request GET \
       --url https://example.com/courses/<COURSE-ID>/forum-categories \
       --header "Content-Type: application/vnd.api+json" \
       --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`" \
       --data
   ```

### Autorisierung
         Der Nutzer sollte Mitglied des entsprechenden Kurses sein.
   > Der Request liefert JSON ähnlich wie dieses:

```json
{
  "data": [
    {
      "type": "forum-categories",
      "id": "d6b887a73f024cf31b4a01f41531b809",
      "attributes": {
        "title": "NewStuff",
        "position": 0
      },
      "relationships": {
        "course": {
          "data": {
            "type": "courses",
            "id": "1b7d3834e42c1569947e0eab7b63ed19"
          },
          "links": {
            "related": "/stud35/plugins.php/argonautsplugin/courses/1b7d3834e42c1569947e0eab7b63ed19"
          }
        }
      },
      "links": {
        "self": "/stud35/plugins.php/argonautsplugin/forum-categories/d6b887a73f024cf31b4a01f41531b809"
      }
    },
    {
      "type": "forum-categories",
      "id": "3710de2efd59869ab7ed7e410f70947f",
      "attributes": {
        "title": "CatCreateRoute",
        "position": 1
      },
      "relationships": {
        "course": {
          "data": {
            "type": "courses",
            "id": "1b7d3834e42c1569947e0eab7b63ed19"
          },
          "links": {
            "related": "/stud35/plugins.php/argonautsplugin/courses/1b7d3834e42c1569947e0eab7b63ed19"
          }
        }
      },
      "links": {
        "self": "/stud35/plugins.php/argonautsplugin/forum-categories/3710de2efd59869ab7ed7e410f70947f"
      }
    },
    {
      "type": "forum-categories",
      "id": "7684942d4a1d3f8ab0752165e22c31a6",
      "attributes": {
        "title": "TESTECASE ",
        "position": 2
      },
      "relationships": {
        "course": {
          "data": {
            "type": "courses",
            "id": "1b7d3834e42c1569947e0eab7b63ed19"
          },
          "links": {
            "related": "/stud35/plugins.php/argonautsplugin/courses/1b7d3834e42c1569947e0eab7b63ed19"
          }
        }
      },
      "links": {
        "self": "/stud35/plugins.php/argonautsplugin/forum-categories/7684942d4a1d3f8ab0752165e22c31a6"
      }
    },
    {
      "type": "forum-categories",
      "id": "4ca16225a42c94957c4129da5f0bef2d",
      "attributes": {
        "title": "CatCreateRoute",
        "position": 3
      },
      "relationships": {
        "course": {
          "data": {
            "type": "courses",
            "id": "1b7d3834e42c1569947e0eab7b63ed19"
          },
          "links": {
            "related": "/stud35/plugins.php/argonautsplugin/courses/1b7d3834e42c1569947e0eab7b63ed19"
          }
        }
      },
      "links": {
        "self": "/stud35/plugins.php/argonautsplugin/forum-categories/4ca16225a42c94957c4129da5f0bef2d"
      }
    },
    {
      "type": "forum-categories",
      "id": "1b7d3834e42c1569947e0eab7b63ed19",
      "attributes": {
        "title": "Allgemein",
        "position": 4
      },
      "relationships": {
        "course": {
          "data": {
            "type": "courses",
            "id": "1b7d3834e42c1569947e0eab7b63ed19"
          },
          "links": {
            "related": "/stud35/plugins.php/argonautsplugin/courses/1b7d3834e42c1569947e0eab7b63ed19"
          }
        }
      },
      "links": {
        "self": "/stud35/plugins.php/argonautsplugin/forum-categories/1b7d3834e42c1569947e0eab7b63ed19"
      }
    }
  ]
}
```

## Eine Forum-Kategorie auslesen
    GET /forum-categories/{id}

    Parameter | Beschreibung
   ---------- | ------------
   id         | Die ID der Kategorie

   ```shell
   curl --request GET \
       --url https://example.com/forum-categories/<FORUM-CATEGORY-ID> \
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
"type": "forum-categories",
"id": "1b7d3834e42c1569947e0eab7b63ed19",
"attributes": {
"title": "Allgemein",
"position": 4
},
"relationships": {
"course": {
"data": {
"type": "courses",
"id": "1b7d3834e42c1569947e0eab7b63ed19"
},
"links": {
"related": "/stud35/plugins.php/argonautsplugin/courses/1b7d3834e42c1569947e0eab7b63ed19"
}
}
},
"links": {
"self": "/stud35/plugins.php/argonautsplugin/forum-categories/1b7d3834e42c1569947e0eab7b63ed19"
}
}
}
```

## Einen Forum-Eintrag auslesen
       GET /forum-entries/{id}

       Parameter | Beschreibung
      ---------- | ------------
      id         | Die ID des Entries

  ```shell
  curl --request GET \
      --url https://example.com/forum-entries/<FORUM-ENTRY-ID> \
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
    "type": "forum-entries",
    "id": "1b7d3834e42c1569947e0eab7b63ed19",
    "attributes": {
      "title": "Übersicht",
      "area": 0,
      "content": ""
    },
    "relationships": {
      "category": {
        "data": {
          "type": "forum-categories",
          "id": null
        },
        "links": {
          "related": "/stud35/plugins.php/argonautsplugin/forum-categories/"
        }
      },
      "child-entries": {
        "data": [
          {
            "type": "forum-entries",
            "id": "2e6ff68d79c5e3f3ed24bd0274865e42"
          },
          {
            "type": "forum-entries",
            "id": "3e0ec8e69afe2502763730e17954d340"
          },
          {
            "type": "forum-entries",
            "id": "783e1b783a76f109eeb5fc19c43c2d08"
          },
          {
            "type": "forum-entries",
            "id": "9af47a2c1463b12a35e3a7c3a10bd53c"
          },
          {
            "type": "forum-entries",
            "id": "a5e119fc5b8cfc549ab9cc985e8609a1"
          },
          {
            "type": "forum-entries",
            "id": "b21ca75f9562d3a5751babaac49bbc9a"
          },
          {
            "type": "forum-entries",
            "id": "c2e21dfa7d071fb6f40ed29271f926aa"
          },
          {
            "type": "forum-entries",
            "id": "f5f8ea3da6fd945eb92dd0d1e1193132"
          }
        ],
        "links": {
          "related": "/stud35/plugins.php/argonautsplugin/forum-entries/1b7d3834e42c1569947e0eab7b63ed19/child-entries"
        }
      }
    },
    "links": {
      "self": "/stud35/plugins.php/argonautsplugin/forum-entries/1b7d3834e42c1569947e0eab7b63ed19"
    }
  }
}
```

## Alle Forum-Einträge einer Kategorie auslesen
       GET /forum-categories/{id}/entries

       Parameter | Beschreibung
      ---------- | ------------
      id         | Die ID der Kategorie

  ```shell
  curl --request GET \
      --url https://example.com/forum-categories/<CATEGORY-ID>/entries \
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
    "type": "forum-entries",
    "id": "1b7d3834e42c1569947e0eab7b63ed19",
    "attributes": {
      "title": "Übersicht",
      "area": 0,
      "content": ""
    },
    "relationships": {
      "category": {
        "data": {
          "type": "forum-categories",
          "id": null
        },
        "links": {
          "related": "/stud35/plugins.php/argonautsplugin/forum-categories/"
        }
      },
      "child-entries": {
        "data": [
          {
            "type": "forum-entries",
            "id": "2e6ff68d79c5e3f3ed24bd0274865e42"
          },
          {
            "type": "forum-entries",
            "id": "3e0ec8e69afe2502763730e17954d340"
          },
          {
            "type": "forum-entries",
            "id": "783e1b783a76f109eeb5fc19c43c2d08"
          },
          {
            "type": "forum-entries",
            "id": "9af47a2c1463b12a35e3a7c3a10bd53c"
          },
          {
            "type": "forum-entries",
            "id": "a5e119fc5b8cfc549ab9cc985e8609a1"
          },
          {
            "type": "forum-entries",
            "id": "b21ca75f9562d3a5751babaac49bbc9a"
          },
          {
            "type": "forum-entries",
            "id": "c2e21dfa7d071fb6f40ed29271f926aa"
          },
          {
            "type": "forum-entries",
            "id": "f5f8ea3da6fd945eb92dd0d1e1193132"
          }
        ],
        "links": {
          "related": "/stud35/plugins.php/argonautsplugin/forum-entries/1b7d3834e42c1569947e0eab7b63ed19/child-entries"
        }
      }
    },
    "links": {
      "self": "/stud35/plugins.php/argonautsplugin/forum-entries/1b7d3834e42c1569947e0eab7b63ed19"
    }
  }
}
```

## Alle Untereinträge eines Forumeintrags auslesen
       GET /forum-entries/{id}/entries

       Parameter | Beschreibung
      ---------- | ------------
      id         | Die ID des Eintrags

  ```shell
  curl --request GET \
      --url https://example.com/forum-entries/<FORUM-ENTRY-ID>/entries \
      --header "Content-Type: application/vnd.api+json" \
      --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`" \
      --data
  ```
### Autorisierung
  Der Nutzer sollte Mitglied des entsprechenden Kurses sein.

__
## Eine Kategorie innerhalb eines Kurses anlegen

       POST /courses/{id}/forum-categories

       Parameter | Beschreibung
      ---------- | ------------
      id         | Die ID des Kurses

  ```shell
  curl --request POST \
      --url https://example.com/courses/<COURSE-ID>/categories \
      --header "Content-Type: application/vnd.api+json" \
      --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`" \
      --data
      '{"data": {"type": "forum-categories","attributes": {"title": "CreateCategoryTest","content": "works"}
  }
}'

  ```
### Autorisierung
  Der Nutzer sollte Mitglied des entsprechenden Kurses sein.

  > Der Request liefert JSON ähnlich wie dieses:

```json
{
"data": {
"type": "forum-categories",
"id": "1b7d3834e42c1569947e0eab7b63ed19",
"attributes": {
"title": "Allgemein",
"position": 4
},
"relationships": {
"course": {
"data": {
"type": "courses",
"id": "1b7d3834e42c1569947e0eab7b63ed19"
},
"links": {
"related": "/stud35/plugins.php/argonautsplugin/courses/1b7d3834e42c1569947e0eab7b63ed19"
}
}
},
"links": {
"self": "/stud35/plugins.php/argonautsplugin/forum-categories/1b7d3834e42c1569947e0eab7b63ed19"
}
}
}
```

## Einen Eintrag in eine Kategorie posten

       POST /forum-categories/{id}/entries

       Parameter | Beschreibung
      ---------- | ------------
      id         | Die ID der Kategorie

  ```shell
  curl --request POST \
      --url https://example.com/forum-entries/<FORUM-CATEGORY-ID>/entries \
      --header "Content-Type: application/vnd.api+json" \
      --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`" \
      --data
      '{"data": {"type": "forum-entries","attributes": {"title": "TestTheRoute","content": "works!"}}}'

  ```
### Autorisierung
  Der Nutzer sollte Mitglied des entsprechenden Kurses sein.


## Einen Eintrag unter einen Eintrag posten

       POST /forum-entries/{id}/entries

       Parameter | Beschreibung
      ---------- | ------------
      id         | Die ID des Eintrags

  ```shell
  curl --request POST \
      --url https://example.com/forum-entries/<FORUM-ENTRY-ID>/entries \
      --header "Content-Type: application/vnd.api+json" \
      --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`" \
      --data
      '{"data": {"type": "forum-entries","attributes": {"title": "TestTheRoute","content": "works!"}}}'

  ```
### Autorisierung
  Der Nutzer sollte Mitglied des entsprechenden Kurses sein.


## Einen Kategorie aktualisieren

       PATCH /forum-categories/{id}

       Parameter | Beschreibung
      ---------- | ------------
      id         | Die ID der Kategorie

  ```shell
  curl --request PATCH \
      --url https://example.com/forum-categories/<FORUM-CATEGORY-ID> \
      --header "Content-Type: application/vnd.api+json" \
      --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`" \
      --data
      '{"data": {"type": "forum-categories","attributes": {"title": "UpdateCategory","content": "time for a change"}

  ```
### Autorisierung
  Der Nutzer sollte Mitglied des entsprechenden Kurses sein.
  Der Nutzer sollte die entsprechenden Adminrechte verfügen oder Ersteller der
  Kategorie sein

  > Der Request liefert JSON ähnlich wie dieses:

```json
{
"data": {
"type": "forum-categories",
"id": "1b7d3834e42c1569947e0eab7b63ed19",
"attributes": {
"title": "Allgemein",
"position": 4
},
"relationships": {
"course": {
"data": {
"type": "courses",
"id": "1b7d3834e42c1569947e0eab7b63ed19"
},
"links": {
"related": "/stud35/plugins.php/argonautsplugin/courses/1b7d3834e42c1569947e0eab7b63ed19"
}
}
},
"links": {
"self": "/stud35/plugins.php/argonautsplugin/forum-categories/1b7d3834e42c1569947e0eab7b63ed19"
}
}
}
```

## Einen Forum-Eintrag aktualisieren

       PATCH /forum-entries/{id}

       Parameter | Beschreibung
      ---------- | ------------
      id         | Die ID des Eintrags

  ```shell
  curl --request PATCH \
      --url https://example.com/forum-entries/<FORUM-ENTRY-ID> \
      --header "Content-Type: application/vnd.api+json" \
      --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`" \
      --data
      '{"data": {"type": "forum-entries","attributes": {"title": "Update an entry","content": "time for a change"}}}'

  ```
  ### Autorisierung
  Der Nutzer sollte Mitglied des entsprechenden Kurses sein.
  Der Nutzer sollte die entsprechenden Adminrechte verfügen oder Ersteller des
  Eintrags sein

## Eine Forum-Kategorie entfernen

         DELETE /forum-categories/{id}

         Parameter | Beschreibung
        ---------- | ------------
        id         | Die ID der Kategorie

  ```shell
  curl --request DELETE \
    --url https://example.com/forum-categories/<FORUM-CATEGORY-ID> \
    --header "Content-Type: application/vnd.api+json" \
    --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`" \
    --data
  ```
### Autorisierung
    Der Nutzer sollte Mitglied des entsprechenden Kurses sein.
    Der Nutzer sollte die entsprechenden Adminrechte verfügen oder Ersteller der
    Kategorie sein.

## Einen Forum-Eintrag entfernen

         DELETE /forum-categories/{id}

         Parameter | Beschreibung
        ---------- | ------------
        id         | Die ID des Eintrags

```shell
curl --request DELETE \
    --url https://example.com/forum-entries/<FORUM-ENTRY-ID> \
    --header "Content-Type: application/vnd.api+json" \
    --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`" \
    --data
```
### Autorisierung
    Der Nutzer sollte Mitglied des entsprechenden Kurses sein.
    Der Nutzer sollte die entsprechenden Adminrechte verfügen oder Ersteller des
    Eintrags sein.
