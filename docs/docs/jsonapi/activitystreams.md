---
title: Activity Streams
---

Mit Stud.IP Version 3.5 wurde eine neue API zum Erzeugen, Darstellen
und Filtern von kontextrelevanten Aktivitäten eingeführt. Diese API
kann u.a. dafür genutzt werden um Nutzern einen schnellen Überblick
über die für ihn relevanten Information/Aktivitäten zu geben.

## Schema "activities"

Activity Streams enthalten Objekte des Typs "activities". Diese geben
enthalten eine textuelle Beschreibung der Aktivität, ein Datum, eine
(etwas) detailiertere Beschreibung und die Art der Aktivität (das
Verb). Aktivitäten beziehen sich auf einen Akteur (in der Regel ein
Nutzer), einen Kontext, in dem sie stattfinden, und ein Objekt, auf
das sie sich beziehen.

### Attribute

Attribut      | Beschreibung
--------      | ------------
title         | knappe Beschreibung der Aktivität: "Wer tut was mit wem/was wo?"
mkdate        | Datum der Aktivität
content       | etwas detailiertere Beschreibung der Aktivität
verb          | Art der Aktivität
activity-type | Typ der Aktivität

Die verwendeten Verben sind normiert. Der Wertebereich umfasst:

<code>answered, attempted, attended, completed, created, deleted,
edited, experienced, failed, imported, interacted, passed, shared,
sent, voided</code>

### Relationen

Die Relationen sind nicht änderbar und können nur ausgelesen werden.

 Relation | Beschreibung
--------  | ------------
actor     | Wenn der Akteur der Aktivität ein Nutzer ist, wird mit dieser Relation auf ihn verwiesen.
context   | der Kontext, in dem die Aktivität stattfindet; kann eine der folgenden sein: Veranstaltung, Einrichtung, Nutzer oder System.
object    | das Objekt, mit dem die Aktivität stattfindet; falls möglich wird hier auf eine Route in der JSON:API verwiesen

## Alle Aktivitäten auslesen

```shell
curl --request GET \
    --url https://example.com/jsonapi.php/v1/users/<USER-ID>/activitystream \
    --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`"
```

```javascript
fetch('https://example.com/jsonapi.php/v1/users/<USER-ID>/activitystream', {
    method: 'GET',
    mode: 'cors',
    headers: new Headers({
        'Authorization': `Basic ${btoa('test_autor:testing')}`
    })
}).then(response => console.log(response))
```


Mit dieser Route können die Aktivitäten ausgelesen werden, die für einen
Nutzer sichtbar sind. Der Activity Stream wird paginiert ausgegeben.
Standardmäßig werden nur Aktivitäten **der letzten 6 Monate**
ausgegeben. Diese Einschränkung kann mit Hilfe des URL-Parameters
'filter' verändert werden.

### HTTP Request

`GET /users/{id}/activitystream`

### URL-Parameter

Parameter |  Beschreibung
--------- | -------
filter    | Filtermöglichkeit der anzuzeigenden Aktivitäten (Zeit und Typ)
include   | ermöglicht das Inkludieren des Akteurs, des Kontexts und des Objekts in die JSON:API-Antwort
page      | Einstellmöglichkeiten [zur Paginierung](#paginierung)

#### URL-Parameter 'filter'

```shell
curl --request GET \
     --url 'https://example.com/jsonapi.php/v1/users/<USER-ID>/activitystream?filter\[start\]=1263078000&filter\[end\]=1409695200&filter[activity-type]=documents' \
     --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`"
```

Mit diesem URL-Parameter kann nach Typ und Datum der Aktivitäten
gefiltert werden. Möglich sind folgende Filter:

Filter                | Beschreibung
--------------------- | ------------
filter[start]         | zeitliche Beschränkung: Start des Abfrageintervalls
filter[end]           | zeitliche Beschränkung: Ende des Abfrageintervalls
filter[activity-type] | nur Aktivitäten dieses Typs/dieser Typen werden zurückgeliefert

Mit Hilfe der Parameter 'start' und 'end' kann das Abfrageintervall
verändert werden. Standardmäßig werden alle Aktivitäten der letzten 6
Monate bis zum aktuellen Zeitpunkt zurückgeliefert. Mit 'start' und
'end' können diese Intervallgrenzen beliebig gestaltet werden. Für diese
beiden Parameter können nur ganzzahlige Werte angegeben werden, die
die Anzahl der Sekunden seit dem 01.01.1970 bis zum gewünschten
Zeitpunkt angeben ('unix epoch time').

Der Parameter 'activity-type' schränkt die Aktivitäten nach Typ ein.
Mögliche Werte sind:

`activity`, `documents`, `forum`, `literature`, `message`, `news`, `participants`, `schedule`, `wiki`

Um nach mehreren Aktivitätstypen zu filtern, können mehrere dieser
Typen durch Komma getrennt verwendet werden.

#### URL-Parameter 'include'

```shell
curl --request GET \
     --url 'https://example.com/jsonapi.php/v1/users/<USER-ID>/activitystream?include=actor,context'\
     --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`"
```

Werte      | Beschreibung
---------- | ------------
actor      | inkludiert die Akteure der gelieferten Aktivitäten
context    | inkludiert die Kontexte der gelieferten Aktivitäten
object     | inkludiert die Objekte der gelieferten Aktivitäten

Der 'include'-Parameter wird der JSON:API-Spezifikation entsprechend
verwendet. Es können auch mehrere Werte durch Komma getrennt angegeben werden.

### Meta-Informationen

Damit klar ersichtlich ist, welche Filter für die Abfrage galten,
werden diese Informationen als Top-Level-'meta'-Objekt zurückgegeben.

### Authorisierung

Mit dieser Route kann nur der Nutzer selbst oder Root-Nutzer
diejenigen Aktivitäten sehen, die für einen Nutzers sichtbar wären.
