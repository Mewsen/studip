---
title: Nachrichten
---

:::info
Studip bietet die Möglichkeit Nachrichten ("messages")
innerhalb des Systems zwischen Nutzern und Nutzergruppen zu versenden.
Das Nachrichten-System ist aufgebaut wie ein interner Mail-Service.
:::

## Schema "messages"

Die Bestandteile einer Nachricht sind mit denen einer typischen Mail gleichzusetzen.

### Attribute

Attribut | Beschreibung
-------- | ------------
subject  | Der Betreff einer Nachricht
message  | Der Content einer Nachricht
mkdate   | Erstellungsdatum einer Nachricht
priority | Art der Relevanz
tags     | Themen der Nachricht

### Relationen

Relation   | Beschreibung
--------   | ------------
sender     | Absendender Nutzer
recipients | Emfpänger einer Nachricht

## Alle Inbox-Nachrichten

Gibt alle Nachrichten eines Nutzers zurück.

### HTTP Request

   `GET /users/{id}/inbox`

#### Parameter

Parameter |  Beschreibung
--------- | -------
id        | ID des Nutzers

#### URL-Parameter

Parameter         | Beschreibung
---------         | ------------
filter[unread]    | Sollen nur ungelesene Nachrichten ausgeliefert werden?

Wenn "filter[unread]" nicht gesetzt ist, werden alle Nachrichten ausgeliefert. Mit "filter[unread]=1" werden nur ungelesene Nachrichten zurück gegeben.

### Authorisierung

Diese Route kann nur vom Besitzer der betreffenden Nachrichten genutzt werden.

   ```shell
   curl --request GET \
       --url https://example.com/blubber-postings/<posting-id>/users/<user-id>/inbox \
       --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`"
   ```

## Alle Outbox-Nachrichten
Gibt alle Outbox-Nachrichten eines Nutzers zurück

### HTTP Request
   `GET /users/{id}/outbox`

### Parameter

Parameter |  Beschreibung
--------- | -------
id        | ID des Nutzers

### Authorisierung

Diese Route kann nur vom Besitzer der betreffenden Nachrichten genutzt werden.

   ```shell
   curl --request GET \
       --url https://example.com/blubber-postings/<posting-id>/users/<user-id>/outbox \
       --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`"
   ```

## Eine Nachricht senden

### HTTP Request

`POST /messages`

### Authorisierung

Diese Route kann von jedem Studip-Nutzer genutzt werden.

### Parameter

Diese Route benötigt keine Parameter

   ```shell
   curl --request POST \
       --url https://example.com/messages \
       --header "Content-Type: application/vnd.api+json" \
       --header "Authorization: Basic `echo -ne "root@studip:testing" | base64`" \
       --data
       '{"data": {"type": "messages","attributes": {"subject": "Eine neue E-Mail","message": "Das ist meine erste Mail - Dank der API super einfach.", "priority": "normal" }, "relationships": {"recipients": {"data": [{"type": "users","id": "6235c46eb9e962866ebdceece739ace5"}]}}}}'
   ```

## Eine Nachricht ansehen

### HTTP Request

   `GET /messages/{id}`

   Parameter |  Beschreibung
   --------- | -------
   id        | ID der Nachricht

### Authorisierung

Diese Route kann von Besitzern der jeweiligen Nachricht genutzt werden.

   ```shell
   curl --request GET \
       --url https://example.com/blubber-postings/messages/<message-id>/ \
       --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`"
   ```


## Eine Nachricht löschen
Löscht eine Nachrichten

### HTTP Request
   `DELETE /messages/{id}`

### Authorisierung

Diese Route kann von Besitzern der jeweiligen Nachricht genutzt werden.

  ```shell
    curl --request DELETE \
    --url https://example.com/messages/<messages-id> \
    --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`" \
  ```
