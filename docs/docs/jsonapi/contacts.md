---
title: Kontakte
---


Nutzer können in Stud.IP sich andere Nutzer als Kontakte merken. Dafür
ist kein neuer Ressourcentyp nötig.

## Alle Kontakte

```shell
curl --request GET \
    --url https://example.com/users/<ID>/contacts \
    --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`"
```

Mit dieser Route können alle Kontakte eines Nutzers ausgelesen werden.

### HTTP Request

`GET /users/{id}/contacts`

Parameter | Beschreibung
--------- | ------------
id        | die ID des Nutzers

### URL-Parameter

keine URL-Parameter

### Autorisierung

Jeder Nutzer kann seine eigenen Kontakte sehen.


## Alle Kontakt-IDs eines Nutzer

```shell
curl --request GET \
    --url https://example.com/users/<ID>/relationships/contacts \
    --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`"
```

Mit dieser Route können alle IDs der Kontakte eines Nutzers ausgelesen werden.

(siehe http://jsonapi.org/format/#fetching-relationships)

### HTTP Request

`GET /users/{id}/relationships/contacts`

Parameter | Beschreibung
--------- | ------------
id        | die ID des Nutzers

### URL-Parameter

keine URL-Parameter

### Autorisierung

Jeder Nutzer kann seine eigenen Kontakte sehen.


## Kontakte eines Nutzers setzen

```shell
curl --request PATCH \
    --url https://example.com/users/<ID>/relationships/contacts \
    --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`" \
    --header "Content-Type: application/vnd.api+json" \
    --data '{"data": [ \
        {"type": "users","id":"<id1>"}, \
        {"type": "users","id":"<id2>"}, \
        {"type": "users","id":"<id3>"} \
        ]}'
```

Mit dieser Route kann man die alle Kontakte eines Nutzers setzen.

(siehe http://jsonapi.org/format/#crud-updating-to-many-relationships)


### HTTP Request

`PATCH /users/{id}/relationships/contacts`

Parameter | Beschreibung
--------- | ------------
id        | die ID des Nutzers

### URL-Parameter

keine URL-Parameter

### Autorisierung

Jeder Nutzer kann seine eigenen Kontakte setzen.


## Kontakte eines Nutzers hinzufügen

```shell
curl --request POST \
    --url https://example.com/users/<ID>/relationships/contacts \
    --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`" \
    --header "Content-Type: application/vnd.api+json" \
    --data '{"data": [ \
        {"type": "users","id":"<id4>"} \
        ]}'
```

Mit dieser Route kann man Kontakte eines Nutzers hinzufügen.

(siehe http://jsonapi.org/format/#crud-updating-to-many-relationships)


### HTTP Request

`POST /users/{id}/relationships/contacts`

Parameter | Beschreibung
--------- | ------------
id        | die ID des Nutzers

### URL-Parameter

keine URL-Parameter

### Autorisierung

Jeder Nutzer kann seine eigenen Kontakte setzen.


## Kontakte eines Nutzers löschen

```shell
curl --request DELETE \
    --url https://example.com/users/<ID>/relationships/contacts \
    --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`" \
    --header "Content-Type: application/vnd.api+json" \
    --data '{"data": [ \
        {"type": "users","id":"<id1>"}, \
        {"type": "users","id":"<id4>"} \
        ]}'
```

Mit dieser Route kann man Kontakte eines Nutzers löschen.

(siehe http://jsonapi.org/format/#crud-updating-to-many-relationships)


### HTTP Request

`DELETE /users/{id}/relationships/contacts`

Parameter | Beschreibung
--------- | ------------
id        | die ID des Nutzers

### URL-Parameter

keine URL-Parameter

### Autorisierung

Jeder Nutzer kann seine eigenen Kontakte löschen.
