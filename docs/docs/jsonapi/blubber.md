---
title: Blubber
---

:::info
Blubber ermöglicht innerhalb von Veranstaltungen mit anderen Stud.IP-Teilnehmern zu chatten.
Wir unterscheiden in öffentliche-, private- und veranstaltungsbezogene Blubber.
:::

## Schema 'blubber-postings'
Der Inhalt wird als plain-text und html gespeichert. Meta-Daten geben Informationen über den Zeitpunkt und
das Thema einer Nachricht.

### Attribute

Attribut        | Beschreibung
--------------- | ------------
context-type    | die Art des Kontexts; Veranstaltung ("course"), Öffentlich ("global") oder Nutzer ("user")
content         | der Text des Blubber-Beitrags; kann Stud.IP-Markup enthalten
content-html    | der Text des Blubber-Beitrags; als HTML formatiert
mkdate          | Anlegedatum
chdate          | Datum der letzten Änderung
discussion-time | Datum der letzten Aktivität
tags            | eine Liste von Tags

### Relationen

 Relation | Beschreibung
--------- | ------------
author    | Verfasser der Nachricht
comments  | Untergeordnete Blubber
context   | Wem wird der Blubber angezeigt: users, courses, public
mentions  | Thema eines Blubber-Eintrags
parent    | Übergeordneter Blubber-Eintrag
resharers |


## Alle Beiträge

```shell
curl --request GET \
    --url https://example.com/blubber-postings \
    --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`"
```

```javascript
fetch('https://example.com/blubber-postings', {
    method: 'GET',
    mode: 'cors',
    headers: new Headers({
        'Authorization': `Basic ${btoa('test_autor:testing')}`
    })
}).then(response => console.log(response))
```

Es werden alle Blubber-Beiträge, die man im Stud.IP sehen könnte, angezeigt.

### HTTP Request

`GET /blubber-postings`

### URL-Parameter

Parameter |  Beschreibung
--------- | -------
filter    | Filtermöglichkeit der anzuzeigenden Blubber-Beiträge
include   | abhängige Ressourcen, die auch zurückgeliefert werden ([JSON:API-Spezifikation](http://jsonapi.org/format/#fetching-includes))
page      | Einstellmöglichkeiten [zur Paginierung](#paginierung)

#### URL-Parameter 'filter'

Mit diesem URL-Parameter kann nach Typ und Datum der Aktivitäten
gefiltert werden. Möglich sind folgende Filter:

Beispiel-Url: "https://example.com/blubber-postings?filter[user]=205f3efb7997a0fc9755da2b535038da"

Filter          | Beschreibung
--------------- | ------------
filter[course]  | Filtert Blubber-Einträge für eine Veranstaltung
filter[user]    | Filter Blubber-Einträge für einen Nutzer

#### URL-Parameter 'include'

Fügt folgende Attribute in die Ausgabe hinzu.

Wert      | Beschreibung
--------- | ------------
author    | Den Verfasser eines Blubbers
comments  | Angehangene Blubber
context   | Wem wird der post angezeigt (users, courses, public)
mentions  |
resharers |


## Beitrag auslesen
Einen gezielten Blubber-Eintrag auslesen.
### HTTP Request

`GET /blubber-postings/{id}`

### Parameter

Parameter |  Beschreibung
--------- | -------
id        | ID des Blubber-Posts

### Authorisierung

Diese Route kann von allen Nutzern verwendet werden.

   ```shell
   curl --request GET \
       --url https://example.com/blubber-postings/<posting-id> \
       --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`"
   ```

## Beitrag anlegen

   ```shell
   curl --request POST \
       --url https://example.com/blubber-postings \
       --header "Content-Type: application/vnd.api+json" \
       --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`" \
       --data \
       '{"data":{"type":"blubber-postings","attributes":{"context-type":"course","content":"Ein neuer blubberpost"},"relationships":{"context":{"data":{"type":"courses","id":"<CID>"}}}}}'
   ```

Mit dieser Route kann ein Blubber-Beitrag angelegt werden. Dies kann
ein öffentlicher oder privater Beitrag sein, aber auch Blubber in
Veranstaltungen können darüber angelegt werden.

### HTTP Request

   `POST /blubber-postings`

### HTTP Request Body

Im Request-Body muss der neue Beitrag als ``resource object``  vom Typ
"blubber-postings" sein.

Notwendig sind die Attribute "content" und "context-type".

Abhängig vom Wert des Attributs "context-type", muss außerdem eine
"context"-Relation angegeben werden.

Hat dieses Attribut den Wert "course", muss als "context"-Relation
eine Veranstaltung als ``resource identifiert`` angegeben werden.

### Parameter

   Bei diesem Request sind keine Parameter notwendig.

### Authorisierung

Diese Route kann von allen Nutzern verwendet werden.


## Beitrag editieren

  Aktualisiert einen Blubber-Beitrag.

### HTTP Request

   `PATCH /blubber-postings/{id}`

### Parameter

   Parameter |  Beschreibung
   --------- | -------
   id        | ID des Blubber-Posts

### Authorisierung

Der Sender des Requests muss Besitzer  des Blubber-Beitrags oder Root sein.

   ```shell
   curl --request PATCH \
       --url https://example.com/blubber-postings/<blubber-id> \
       --header "Content-Type: application/vnd.api+json" \
       --header "Authorization: Basic `echo -ne "root@studip:testing" | base64`" \
       --data
       '{"data":{"type":"blubber-postings","attributes":{"context-type":"course","content":"Ein veränderter blubberpost"}, "relationships":{"context":{"data":{"type":"courses","id":"a07535cf2f8a72df33c12ddfa4b53dde"}}}}}'
   ```

## Beitrag löschen

  Löscht einen Blubber-Eintrag.

### HTTP Request

   `DELETE /blubber-postings/{id}`

### Parameter

  Parameter |  Beschreibung
  --------- | -------
  id        | ID des Blubber-Posts

### Authorisierung

Der Sender des Requests muss Besitzer des Blubber-Beitrags oder Root sein.

   ```shell
   curl --request DELETE \
       --url https://example.com/blubber-postings/<blubber-id> \
       --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`" \
   ```

## Relation 'author'

Gibt den Author eines Blubber-Posts zurück.

### HTTP Request

   `GET /blubber-postings/{id}/relationships/author`

### Parameter

  Parameter |  Beschreibung
  --------- | -------
  id        | ID des Blubber-Posts


### Authorisierung

Diese Route kann von allen Nutzern verwendet werden.

   ```shell
   curl --request GET \
       --url https://example.com/blubber-postings/<posting-id>/relationships/author \
       --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`"
   ```


## Kommentare eines Blubber-Beitrags

Gibt alle Kommentare eines Blubber-Beitrags zurück.

### HTTP Request

   `GET /blubber-postings/{id}/comments`

### Parameter

  Parameter |  Beschreibung
  --------- | -------
  id        | ID des Blubber-Posts

### Authorisierung

Diese Route kann von allen Nutzern verwendet werden.

   ```shell
   curl --request GET \
       --url https://example.com/blubber-postings/<posting-id>/comments \
       --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`"
   ```

## Beitrag kommentieren

Erstellt einen Kommentar zu einem Blubber-Beitrag.

### HTTP Request

   `POST /blubber-postings/{id}/comments`

### Parameter

 Parameter |  Beschreibung
 --------- | -------
 id        | ID des Blubber-Posts

### Authorisierung

Diese Route kann von allen Nutzern verwendet werden.

   ```shell
   curl --request POST \
       --url https://example.com/blubber-postings/<posting-id>/comments \
       --header "Content-Type: application/vnd.api+json" \
       --header "Authorization: Basic `echo -ne "root@studip:testing" | base64`" \
       --data
       '{"data": {"type": "blubber-postings","attributes": {"content": "Ein neuer blubberkommentar"}}}'
   ```

## Relation 'comments'

### HTTP Request
   `GET /blubber-postings/{id}/relationships/comments`

### Parameter

 Parameter |  Beschreibung
 --------- | -------
 id        | ID des Blubber-Posts

### Authorisierung

Diese Route kann von allen Nutzern verwendet werden.

   ```shell
   curl --request GET \
       --url https://example.com/blubber-postings/<posting-id>/relationships/comments \
       --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`"
   ```

## Relation 'context'

Gibt den Scope (Sichtbarkeit) eines Blubber-Beitrags zurück.

### HTTP Request

   `GET /blubber-postings/{id}/relationships/context`

### Parameter

    Parameter |  Beschreibung
    --------- | -------
    id        | ID des Blubber-Posts

### Authorisierung

Diese Route kann von allen Nutzern verwendet werden.

   ```shell
   curl --request GET \
       --url https://example.com/blubber-postings/<posting-id>/relationships/context \
       --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`"
   ```

## Erwähnungen eines Beitrags
Gibt an, ob und in welchen Beiträgen eine Referenz zu diesem Beitrag gibt.

### HTTP Request

   `GET /blubber-postings/{id}/mentions`

### Parameter

    Parameter |  Beschreibung
    --------- | -------
    id        | ID des Blubber-Posts

### Authorisierung

Diese Route kann von allen Nutzern verwendet werden.

   ```shell
   curl --request GET \
       --url https://example.com/blubber-postings/<posting-id>/mentions \
       --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`"
   ```

## Relation 'mentions'
Gibt die Referenz der Beiträge zurück, in denen dieser Beitrag erwähnt wird.

### HTTP Request

   `GET /blubber-postings/{id}/relationships/mentions`

### Parameter

    Parameter |  Beschreibung
    --------- | -------
    id        | ID des Blubber-Posts

### Authorisierung

Diese Route kann von allen Nutzern verwendet werden.

   ```shell
   curl --request GET \
       --url https://example.com/blubber-postings/<posting-id>/relationships/mentions \
       --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`"
   ```

## Relation 'resharers'

Gibt die Referenz von Usern zurück, die diesen Beitrag geteilt haben.

### HTTP Request

   `GET /blubber-postings/{id}/relationships/resharers`

### Parameter

    Parameter |  Beschreibung
    --------- | -------
    id        | ID des Blubber-Posts

### Authorisierung

Diese Route kann von allen Nutzern verwendet werden.

   ```shell
   curl --request GET \
       --url https://example.com/blubber-postings/<posting-id>/relationships/resharers \
       --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`"
   ```

## Blubber-Stream auslesen

Gibt eine Folge von Blubber-Einträgen zurück.

   `GET /blubber-streams/{id}`

### Parameter

  Parameter |  Beschreibung
  --------- | -------
  id        | ID des Blubber-Streams

### Authorisierung

Diese Route kann von allen Nutzern verwendet werden.

   ```shell
   curl --request GET \
       --url https://example.com/blubber-postings/<posting-id>/blubber-streams/<stream-id> \
       --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`"
   ```
