---
title: Nutzer*innen
---

Nutzer*innen (`users`) von Stud.IP-Installationen können mit den folgenden Routen
abgefragt werden.

## Schema

Alle Nutzer*innen werden in Stud.IP mit diesem Schema abgebildet. Die `id`
entspricht der in Stud.IP verwendeten `user_id`. Der Typ ist `users`.

### Attribute

Attribut          | Beschreibung
--------          | ------------
username          | der `username` wird beim Login-Vorgang verwendet
formatted-name    | der formatierte Echtname
family-name       | der Nachname
given-name        | der Vorname
name-prefix       | evtl. vorangestellte Titel
name-suffix       | evtl. nachgestellte Titel
permission        | die globale Berechtigungsstufe
email             | die E-Mail-Adresse
phone             | die Telefonnummer
homepage          | die URL der Homepage
address           | die private Adresse

Die Berechtigungsstufe kann eine der folgenden sein: `root`, `admin`,
`dozent`, `tutor`, `autor`

Die Sichtbarkeit der Attribute `phone`, `homepage`, `address` folgt
den Sichtbarkeitseinstellungen, die Nutzer*innen vorgenommen haben.

### Relationen

:::info
Nicht alle Relationen sind für alle Betrachtenden zugänglich.
:::

Relation              | Beschreibung
--------              | ------------
activitystream        | ein Link zum `activity stream`
blubber-postings      | die Blubber
contacts              | die Kontakte
courses               | die Veranstaltungen als `dozent`
course-memberships    | die Teilnahmen an Veranstaltungen
events                | der Terminkalender
institute-memberships | die Institute
schedule              | der Stundenplan


## Alle `users`

```shell
curl --request GET \
    --url https://example.com/jsonapi.php/v1/users \
    --header "Authorization: Basic `echo -ne "root@studip:testing" | base64`" \
```

  > Der Request liefert JSON ähnlich wie dieses:

```json
{
  "meta": {
    "page": {
      "offset": 0,
      "limit": 30,
      "total": 5
    }
  },
  "links": {
    "first": "/?page[offset]=0&page[limit]=30",
    "last": "/?page[offset]=0&page[limit]=30"
  },
  "data": [
    {
      "type": "users",
      "id": "76ed43ef286fb55cf9e41beadb484a9f",
      "attributes": {
        "username": "root@studip",
        "formatted-name": "Root Studip",
        "family-name": "Studip",
        "given-name": "Root",
        "name-prefix": "",
        "name-suffix": "",
        "permission": "root",
        "email": "root@localhost",
        "phone": null,
        "homepage": null,
        "address": null
      },
      "relationships": {
        "activitystream": {
          "links": {
            "related": "jsonapi.php/v1/users/76ed43ef286fb55cf9e41beadb484a9f/activitystream"
          }
        },
        "blubber-postings": {
          "links": {
            "related": "jsonapi.php/v1/blubber-postings?filter[user]=76ed43ef286fb55cf9e41beadb484a9f"
          }
        },
        "contacts": {
          "links": {
            "related": "jsonapi.php/v1/users/76ed43ef286fb55cf9e41beadb484a9f/contacts"
          }
        },
        "courses": {
          "links": {
            "related": "jsonapi.php/v1/users/76ed43ef286fb55cf9e41beadb484a9f/courses"
          }
        },
        "course-memberships": {
          "links": {
            "related": "jsonapi.php/v1/users/76ed43ef286fb55cf9e41beadb484a9f/course-memberships"
          }
        },
        "events": {
          "links": {
            "related": "jsonapi.php/v1/users/76ed43ef286fb55cf9e41beadb484a9f/events"
          }
        },
        "institute-memberships": {
          "links": {
            "related": "jsonapi.php/v1/users/76ed43ef286fb55cf9e41beadb484a9f/institute-memberships"
          }
        },
        "schedule": {
          "links": {
            "related": "jsonapi.php/v1/users/76ed43ef286fb55cf9e41beadb484a9f/schedule"
          }
        }
      },
      "links": {
        "self": "jsonapi.php/v1/users/76ed43ef286fb55cf9e41beadb484a9f"
      },
      "meta": [

      ]
    },
    "[...]"
  ]
}
```

Dieser Endpoint liefert alle Nutzer*innen im Stud.IP, die mit den
`credentials` des JSON:API-Nutzenden auch in Stud.IP selbst gesehen
werden dürfen. Die Ausgabe erfolgt paginiert und kann durch Angabe von
Offset und Limit weitergeblättert werden.

### HTTP Request

`GET /users`

### Query-Parameter

```shell
curl --request GET \
     --url 'https://example.com/jsonapi.php/v1/users?filter[search]=test_autor'\
     --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`"
```

Parameter      | Default | Beschreibung
---------      | ------- | ------------
page[offset]   | 0       | der Offset
page[limit]    | 30      | das Limit
filter[search] | %%%     | der Suchbegriff, um Nutzer zu finden; mind. 3 Zeichen

### Autorisierung

Diese Route kann nur von Nutzern der Rechtestufe "root" verwendet werden.



## Sich selbst auslesen

```shell
curl --request GET \
    --url https://example.com/jsonapi.php/v1/users/me \
    --header "Authorization: Basic `echo -ne "test_dozent:testing" | base64`"
```

> Der Request liefert JSON ähnlich wie dieses:

```json
{
  "data": {
    "type": "users",
    "id": "205f3efb7997a0fc9755da2b535038da",
    "attributes": {
      "username": "test_dozent",
      "formatted-name": "Testaccount Dozent",
      "family-name": "Dozent",
      "given-name": "Testaccount",
      "name-prefix": "",
      "name-suffix": "",
      "permission": "dozent",
      "email": "dozent@studip.de",
      "phone": null,
      "homepage": null,
      "address": null
    },
    "relationships": {
      "activitystream": {
        "links": {
          "related": "jsonapi.php/v1/users/205f3efb7997a0fc9755da2b535038da/activitystream"
        }
      },
      "blubber-postings": {
        "links": {
          "related": "jsonapi.php/v1/blubber-postings?filter[user]=205f3efb7997a0fc9755da2b535038da"
        }
      },
      "contacts": {
        "links": {
          "related": "jsonapi.php/v1/users/205f3efb7997a0fc9755da2b535038da/contacts"
        }
      },
      "courses": {
        "links": {
          "related": "jsonapi.php/v1/users/205f3efb7997a0fc9755da2b535038da/courses"
        }
      },
      "course-memberships": {
        "links": {
          "related": "jsonapi.php/v1/users/205f3efb7997a0fc9755da2b535038da/course-memberships"
        }
      },
      "events": {
        "links": {
          "related": "jsonapi.php/v1/users/205f3efb7997a0fc9755da2b535038da/events"
        }
      },
      "institute-memberships": {
        "links": {
          "related": "jsonapi.php/v1/users/205f3efb7997a0fc9755da2b535038da/institute-memberships"
        }
      },
      "schedule": {
        "links": {
          "related": "jsonapi.php/v1/users/205f3efb7997a0fc9755da2b535038da/schedule"
        }
      }
    },
    "links": {
      "self": "jsonapi.php/v1/users/205f3efb7997a0fc9755da2b535038da"
    },
    "meta": [

    ]
  }
}
```

Mit diesem Endpoint bekommt man denjenigen Stud.IP Nutzer, der
autorisiert auf diesen Endpoint zugreift – also sich selbst.

### HTTP Request

`GET /users/me`

### Query-Parameter

Es werden keine Query-Parameter unterstützt.

### Autorisierung

Diese Route kann von jedem autorisierten Nutzer verwendet werden.


## Einzelne Nutzende auslesen

```shell
curl --request GET \
    --url https://example.com/jsonapi.php/v1/users/<ID> \
    --header "Authorization: Basic `echo -ne "test_dozent:testing" | base64`"
```

Diese Route liefert einzelne, beliebige Nutzende zurück. Unsichtbare
Nutzende können sich allerdings nur selbst sehen.

### HTTP Request

`GET /users/{id}`

### Query-Parameter

Es werden keine Query-Parameter unterstützt.

### Autorisierung

Man kann sich selbst sehen. `root` darf alle Nutzenden sehen. Gesperrte
und unsichtbare Nutzende sind ansonsten nicht sichtbar.


## Nutzende löschen

```shell
curl --request DELETE \
    --url https://example.com/jsonapi.php/v1/users/<ID> \
    --header "Authorization: Basic `echo -ne "test_dozent:testing" | base64`"
```

Diese Route löscht einen beliebigen Nutzenden.

### HTTP Request

`DELETE /users/{id}`

### Query-Parameter

Es werden keine Query-Parameter unterstützt.

### Autorisierung

Diese Route ist nur aktiviert, wenn die Stud.IP-Konfiguration
"JSONAPI_DANGEROUS_ROUTES_ALLOWED" gesetzt ist.

Ist das der Fall, dürfen Nutzende der Rechtestufe `root` andere
Nutzende löschen. Man kann sich selbst **nicht** löschen.


## Mitgliedschaften in Einrichtungen

```shell
curl --request GET \
    --url https://example.com/jsonapi.php/v1/users/<ID>/institute-memberships \
    --header "Authorization: Basic `echo -ne "test_dozent:testing" | base64`"
```

Mit dieser Route erhält man die Mitgliedschaften in Einrichtungen von Nutzenden.

### HTTP Request

`GET http://example.com/api/users/{id}/institute-memberships`

### Query-Parameter

Es werden keine Query-Parameter unterstützt.

### Autorisierung

Ein Nutzer kann nur die eigenen Mitgliedschaften in Einrichtungen einsehen.
