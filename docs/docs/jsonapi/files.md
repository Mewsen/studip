---
title: Dateibereich
---

In Stud.IP hat jeder Nutzer, jede Einrichtung und jede Veranstaltung
einen eigenen Dateibereich. Dateibereiche sind (spezielle) Ordner.
Ordner können Dateien und Ordner enthalten, in denen sich wiederum
Dateien und Ordner befinden können.

Es gibt verschiedene Arten von Ordnern, die sich in der Regel darin
unterscheiden, wer sie sehen kann und wer Lese- und/oder
Schreibzugriff auf diese Ordner hat.

## Schemata


### Schema "file-refs"

Aus Nutzersicht sind Dateien in Stud.IP Ressourcen vom Typ
"file-refs". Technisch gesehen sind es allerdings Verweise auf die
Ressourcen vom Typ "files". Letztere sind die tatsächlich auf der
Festplatte gespeicherten Dateien, die mithilfe der "file-refs" nur
verlinkt werden.

Vereinfacht gesagt, hantiert man in der Regel immer mit "file-refs".

### Attribute

Attribut    | Beschreibung
--------    | ------------
name        | der Name der Datei
description | eine optionale Beschreibung der Datei
mkdate      | das Erstellungsdatum der Datei
chdate      | das Datum der letzten Änderung der Metadaten ('name', 'description', …) der Datei
downloads   | Wie häufig wurde diese Datei heruntergeladen?
filesize    | die Größe der Datei in Byte
storage     | TODO

### Relationen

 Relation    | Beschreibung
--------     | ------------
file         | die tatsächliche Datei auf der Festplatte
owner        | der Nutzer, dem diese Datei gehört
parent       | der Ordner, im dem diese Datei liegt
range        | die Veranstaltung, die Einrichtung oder der Nutzer, in dessen Dateibereich diese Datei liegt
terms-of-use | die Lizenz, unter der diese Datei verfügbar gemacht wird

### Meta

In den Metadaten von Dateien ist der "download-link" enthalten, um den Inhalt der Datei herunterzuladen.


### Schema "files"

Anders als Ressourcen vom Typ "file-refs" sind Ressourcen vom Typ "files" über die grafische Oberfläche nicht verfügbar. Technisch werden "files" verwendet, um die Dateien tatsächlich auf der Festplatte (oder einem entfernten Speicherort) abzulegen.

Erst durch die Verknüpfung durch "file-refs" werden Ressourcen vom Typ "files" sichtbar.

### Attribute

Attribut  | Beschreibung
--------- | ------------
name      | der Name der Datei
mime-type | der MIME-Typ der Datei
size      | die Größe der Datei in Bytes
storage   | TODO
mkdate    | das Erstellungsdatum der Datei
chdate    | das Datum der letzten Änderung der Datei

### Relationen

 Relation | Beschreibung
--------  | ------------
file-refs | alle Ressourcen vom Typ "file-refs", die auf diese Datei verweisen
owner     | der Nutzer, dem diese Datei gehört

### Type "folders"

Ressourcen vom Typ "folders" sind im herkömmlichen Sinne Ordner und können weitere "folders" oder Ressourcen vom Typ "file-refs" enthalten.

Es gibt verschiedene Arten von "folders". In Stud.IP werden aber vorrangig "StandardFolders" verwendet. Für diese sind alle Operationen möglich. Für andere Arten entscheiden die Implementierungen jeweils selbst, ob die Operation möglich ist.

### Attribute

Attribut             | Beschreibung
------------         | ------------
folder-type          | die Art des Ordners
name                 | der Name des Ordners
description          | die Beschreibung des Ordners
mkdate               | das Erstellungsdatum des Ordners
chdate               | das Datum der letzten Änderung des Ordners
is-visible           | Darf der eingeloggte Nutzer den Ordner sehen?
is-readable          | Darf der eingeloggte Nutzer den Ordner öffnen?
is-writable          | Darf der eingeloggte Nutzer im Ordner Dateien erstellen?
is-editable          | Darf der eingeloggte Nutzer den Ordner bearbeiten?
is-subfolder-allowed | Darf der eingeloggte Nutzer im Ordner weitere Ordner erstellen?


### Relationen

Relation  | Beschreibung
--------- | ------------
owner     | der Nutzer, dem dieser Ordner gehört
parent    | der Ordner, in dem sich dieser Ordner befindet
range     | die Veranstaltung, die Einrichtung oder der Nutzer, in dessen Dateibereich dieser Ordner liegt
folders   | die Ordner, die sich in diesem Ordner befinden
file-refs | die Dateien, die sich in diesem Ordner befinden


### Type "terms-of-use"

Jede Datei unterliegt einer Lizenz, die die Nutzung, Weitergabe und Veränderung regelt.

### Attribute

Attribut     | Beschreibung
------------ | ------------
name         | der Name der Lizenz
description  | die Beschreibung der Lizenz
icon         | das für die Lizenz verwendete Icon
mkdate       | das Erstellungsdatum der Lizenz
chdate       | das Datum der letzten Änderung der Lizenz


### Relationen

Lizenzen ('terms-of-use') haben keine Relationen.

## Alle Lizenzen

```shell
curl --request GET \
    --url https://example.com/terms-of-use \
    --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`"
```

Diese Route erfragt alle im Stud.IP registrierten Lizenzen von Dateien.

### HTTP Request

`GET /terms-of-use`

### Autorisierung

Jeder Nutzer darf diese Route verwenden.

## Eine Lizenz auslesen

```shell
curl --request GET \
    --url https://example.com/terms-of-use/<ID> \
    --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`"
```

Mit dieser Route kann eine beliebige Lizenz ausgelesen werden.

### HTTP Request

`GET /terms-of-use/{id}`

Parameter | Beschreibung
--------- | ------------
id        | die ID der Lizenz

### URL-Parameter

keine URL-Parameter

### Autorisierung

Jeder Nutzer darf diese Route verwenden.


## Alle Dateien eines Dateibereichs

```shell
curl --request GET \
    --url https://example.com/<courses,institutes,users>/<ID>/file-refs \
    --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`"
```

Mit dieser Route können *alle*  Dateien eines Dateibereichs ausgelesen
werden. Das Ergebnis ist eine flache Liste aller Dateien, ungeachtet
der Zugehörigkeit zu Ordnern dieses Dateibereichs.

### HTTP Request

`GET /courses/{id}/file-refs`
`GET /institutes/{id}/file-refs`
`GET /users/{id}/file-refs`

Parameter |  Beschreibung
--------- | -------
id        | die ID der Veranstaltung, der Einrichtung oder des Nutzers

### URL-Parameter

Parameter    | Default | Beschreibung
---------    | ------- | ------------
page[offset] | 0       | der Offset (siehe Paginierung)
page[limit]  | 30      | das Limit (siehe Paginierung)

### Autorisierung

Die Dateien einer Einrichtung darf jeder Nutzer sehen. Die Dateien
einer Veranstaltung sehen alle Nutzer, die Zugriff
auf die Veranstaltung haben. Die Dateien eines Nutzers sehen alle, es
sei denn der Nutzer ist unsichtbar.

Im Übrigen gelten die Zugriffsregeln der Ordner, in denen die Dateien liegen.


## Alle Ordner eines Dateibereichs

```shell
curl --request GET \
    --url https://example.com/<courses,institutes,users>/<ID>/folders \
    --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`"
```

Mit dieser Route können *alle*  Ordner eines Dateibereichs ausgelesen
werden. Das Ergebnis ist eine flache Liste aller Ordner, ungeachtet
der Zugehörigkeit zu Ordnern dieses Dateibereichs.

### HTTP Request

`GET /courses/{id}/folders`
`GET /institutes/{id}/folders`
`GET /users/{id}/folders`

Parameter |  Beschreibung
--------- | -------
id        | die ID der Veranstaltung, der Einrichtung oder des Nutzers

### URL-Parameter

Parameter    | Default | Beschreibung
---------    | ------- | ------------
page[offset] | 0       | der Offset (siehe Paginierung)
page[limit]  | 30      | das Limit (siehe Paginierung)

### Autorisierung

Die Ordner einer Einrichtung darf jeder Nutzer sehen. Die Ordner einer
Veranstaltung sehen alle Nutzer, die Zugriff auf die Veranstaltung
haben. Die Ordner eines Nutzers sehen alle, es sei denn der Nutzer ist
unsichtbar.

Im Übrigen gelten die Zugriffsregeln der Ordner, in denen die Ordner liegen.


## Einen Ordner erstellen

Ein Ordner kann einfach über diese Route angelegt werden.

```shell
   curl --request POST \
       --url https://example.com/courses/<ID>/folders \
       --header "Content-Type: application/vnd.api+json" \
       --header "Authorization: Basic `echo -ne "test_dozent:testing" | base64`" \
       --data '{"data": {"type": "folders","attributes": {"name": "Name of the folder"}, "relationships": {"parent": {"data": {"type":"folders","id":"<any-folder-id>"}}}}}'
```

### HTTP Request

`POST /courses/{id}/folders`
`POST /institutes/{id}/folders`
`POST /users/{id}/folders`

Parameter |  Beschreibung
--------- | -------
id        | die ID der Veranstaltung, der Einrichtung oder des Nutzers

Der Request-Body enthält ein "JSONAPI resource object" vom Typ "folders". Name und übergeordneter, enthaltender Ordner sind erforderlich: Das Attribut "name" und die Relation "parent", die auf ein "folders"-Objekt verweist, sind verpflichtend.

### URL-Parameter

keine Parameter

### Autorisierung

Ob ein Ordner angelegt werden darf, wird von der jeweiligen Implementation des Zielordners entschieden.


## Eine Datei auslesen

```shell
curl --request GET \
    --url https://example.com/file-refs/<ID> \
    --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`"
```

Mit dieser Route kann eine Datei ausgelesen werden.

### HTTP Request

`GET /file-refs/{id}`

Parameter |  Beschreibung
--------- | -------
id        | die ID der Datei

### URL-Parameter

keine Parameter

### Autorisierung

Ob eine Datei ausgelesen werden darf, entscheidet der übergeordnete Ordner.


## Metadaten einer Datei ändern

```shell
curl --request PATCH \
    --url https://example.com/file-refs/<ID> \
    --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`" \
    --header "Content-Type: application/vnd.api+json" \
    --data '{"data": {"type": "file-refs","id":"<id-der-datei>", \
             "attributes":{"name":"neuer-name.jpg"}}}'
```
Mit dieser Route kann der Name, die Beschreibung und/oder die Lizenz einer Datei geändert werden. Dazu wird JSONAPI-typisch das angepasste "resource object" an diese Route geschickt.

### HTTP Request

`PATCH /file-refs/{id}`

Parameter |  Beschreibung
--------- | -------
id        | die ID der Datei

Der Request-Body enthält das veränderte "resource object".

### URL-Parameter

keine URL-Parameter

### Autorisierung

Ob eine Datei angepasst werden darf, entscheidet der übergeordnete Ordner.


## Eine Datei löschen

```shell
curl --request DELETE \
    --url https://example.com/file-refs/<ID> \
    --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`"
```

Mit dieser Route löscht man eine Datei.

### HTTP Request

`DELETE /file-refs/{id}`

Parameter |  Beschreibung
--------- | -------
id        | die ID der Datei

### URL-Parameter

keine URL-Parameter

### Autorisierung

ob eine Datei gelöscht werden kann, entscheidet der übergeordnete Ordner.


## Lizenz einer Datei auslesen
```shell
curl --request GET \
    --url https://example.com/file-refs/<ID>/relationships/terms-of-use \
    --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`"
```

Um die Relation einer Datei zu einer Lizenz auszulesen, verwendet man diese Route.

### HTTP Request

`GET /file-refs/<ID>/relationships/terms-of-use`

Parameter |  Beschreibung
--------- | -------
id        | die ID der Datei

### URL-Parameter

keine URL-Parameter

### Autorisierung

Der übergeordnete Ordner der Datei entscheidet.


## Lizenz einer Datei ändern
```shell
curl --request PATCH \
    --url https://example.com/file-refs/<ID>/relationships/terms-of-use \
    --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`" \
    --header "Content-Type: application/vnd.api+json" \
    --data '{"data": {"type": "terms-of-use","id": "<id-der-lizenz>"}}'
```

Um die Relation einer Datei zu einer Lizenz zu ändern, verwendet man diese Route. Das Löschen der Relation zur Datei ist ausgeschlossen.

### HTTP Request

`PATCH /file-refs/<ID>/relationships/terms-of-use`

Parameter |  Beschreibung
--------- | -------
id        | die ID der Datei

Der Request-Body muss einen "resource identifier" von Typ "terms-of-use" enthalten.

### URL-Parameter

keine URL-Parameter

### Autorisierung

Der übergeordnete Ordner der Datei entscheidet.


## Den ETag einer Datei auslesen

:::danger
Diese Route ist keine JSON-API-konforme Route.
:::

```shell
curl --request HEAD \
    --url https://example.com/file-refs/<ID>/content \
    --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`"
```

Um einen Fingerabdruck (ETag) des tatsächlichen Inhalts einer Datei zu bekommen, kann man diese nicht-JSON-API-Route aufrufen.

### HTTP Request

`HEAD /file-refs/{id}/content`

Parameter |  Beschreibung
--------- | -------
id        | die ID der Datei

### URL-Parameter

keine URL-Parameter

### Autorisierung

Der übergeordnete Ordner der Datei entscheidet.


## Eine Datei herunterladen

:::danger
Diese Route ist keine JSON-API-konforme Route.
:::

```shell
curl --request GET \
    --url https://example.com/file-refs/<ID>/content \
    --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`"
```

Mit dieser Route kann der Inhalt einer Datei heruntergeladen werden.

### HTTP Request

`GET /file-refs/{id}/content`

Parameter |  Beschreibung
--------- | -------
id        | die ID der Datei

Der Request kann einen ETag-Header mitbringen, um redundante
Datenübertragung zu vermeiden.

### URL-Parameter

keine URL-Parameter

### Autorisierung

Der übergeordnete Ordner der Datei entscheidet.


## Inhalt einer Datei aktualisieren

:::danger
Diese Route ist keine JSON-API-konforme Route.
:::

```shell
curl --request POST --url https://example.com/file-refs/<ID>/content \
    --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`" \
    -F 'myfile=@/path/to/local/file'
```

Mit dieser Route kann der Inhalt einer vorhandenen Datei überschrieben
werden. Dazu wird eine einzige Datei "multipart/form-data"-kodiert an
diese Route geschickt.

### HTTP Request

`POST /file-refs/{id}/content`

Im Request-Body muss dann eine Datei "multipart/form-data"-kodiert
enthalten sein.


### URL-Parameter

keine URL-Parameter

### Autorisierung

Der übergeordnete Ordner der Datei entscheidet.


## Einen Ordner auslesen

```shell
curl --request GET \
    --url https://example.com/folders/<ID> \
    --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`"
```

Mit dieser Route kann man die Daten eines Ordners auslesen.

### HTTP Request

`GET /folders/{id}`

Parameter |  Beschreibung
--------- | -------
id        | die ID des Ordners

### URL-Parameter

keine URL-Parameter

### Autorisierung

Die Art des Ordners entscheidet über die Autorisierung.


## Einen Ordner ändern
```shell
curl --request PATCH \
    --url https://example.com/folders/<ID> \
    --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`" \
    --header "Content-Type: application/vnd.api+json" \
    --data '{"data": {"type":"folders","id":"<id-der-lizenz>", \
             "attributes":{"name":"Neuer Name"}}}'
```

Mit dieser Route kann der Name und/oder die Beschreibung geändert
werden. Außerdem kann man den Ordner in einen anderen Ordner verschieben. Dazu
ändert man die "parent"-Relation.

### HTTP Request

`PATCH /folders/{id}`

Parameter |  Beschreibung
--------- | -------
id        | die ID des Ordners

### URL-Parameter

keine URL-Parameter

### Autorisierung

Die Art des Ordners entscheidet über die Autorisierung.


## Einen Ordner löschen
```shell
curl --request DELETE \
    --url https://example.com/folders/<ID> \
    --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`"
```

Mit dieser Route kann man einen Ordner löschen.

### HTTP Request

`DELETE /folders/{id}`

Parameter |  Beschreibung
--------- | -------
id        | die ID des Ordners

### URL-Parameter

keine URL-Parameter

### Autorisierung

Die Art des Ordners entscheidet über die Autorisierung.


## Alle Dateien eines Ordners
```shell
curl --request GET \
    --url https://example.com/folders/<ID>/file-refs \
    --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`"
```

Mit dieser Route erhält man eine Liste aller Dateien, die direkt in
einem Ordner liegen.

### HTTP Request

`GET /folders/{id}/file-refs`

Parameter |  Beschreibung
--------- | -------
id        | die ID des Ordners

### URL-Parameter

Parameter    | Default | Beschreibung
---------    | ------- | ------------
page[offset] | 0       | der Offset (siehe Paginierung)
page[limit]  | 30      | das Limit (siehe Paginierung)

### Autorisierung

Ob man die Liste der Dateien eines Ordners sehen darf, entscheidet die
Implementierung des Ordners.


## Alle Ordner eines Ordners
```shell
curl --request GET \
    --url https://example.com/folders/<ID>/folders \
    --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`"
```

Mit dieser Route erhält man eine Liste aller Ordner, die direkt in
einem Ordner liegen.

### HTTP Request

`GET /folders/{id}/folders`

Parameter |  Beschreibung
--------- | -------
id        | die ID des Ordners

### URL-Parameter

Parameter    | Default | Beschreibung
---------    | ------- | ------------
page[offset] | 0       | der Offset (siehe Paginierung)
page[limit]  | 30      | das Limit (siehe Paginierung)

### Autorisierung

Ob man die Liste der Ordner eines Ordners sehen darf, entscheidet die
Implementierung des Ordners.


## Eine Datei erstellen

Eine Datei wird immer in einem Ordner erstellt. Da Dateien aus
Metadaten **und** Inhalt bestehen, muss das Erstellen einer Datei in
zwei Schritten passieren. Dazu kann entweder

* zuerst der Inhalt hochgeladen werden und dann die Metadaten (wie Beschreibung und Lizenz) angepasst werden oder
* erst die Datei mit den Metadaten erstellt werden und nachträglich der Inhalt hochgeladen werden.

### Variante a.

```shell
curl --request POST --url "https://example.com/folders/<ID>/file-refs" \
     -F 'file=@/pfad/zu/einer-neuen-datei.jpg' \
     --header "Authorization: Basic `echo -ne "test_dozent:testing" | base64`"
```

Zuerst sendet man einen ``POST``-Request mit ``Content-Type: multipart/form-data`` und der Datei im Request-Body an die angegebene URL.

Man erhält im Erfolgsfall einen Status-Code 201 und einen ``Location``-Header, der einen zum neu erstellten Dokument in die JSON:API bringt.

Der *Dateiname* wird standardmäßig aus dem Upload genommen und auch für den Namen der Datei verwendet.

Will man einen anderen Dateinamen verwenden, kann man einen HTTP-Header verwenden: ``Slug: neuer-dateiname.txt``.

Über die URL aus dem erhaltenen ``Location``-Header erhält man die JSON:API-Repräsentation der hochgeladenen Datei.

Nun können mit einem (JSON:API-typischen) ``PATCH``-Request an diese Route Modifikationen an den Metadaten (wie Beschreibung usw.) vorgenommen werden.

### Variante b.

```shell
curl --request POST --url https://example.com/folders/<ID>/file-refs \
    --header "Content-Type: application/vnd.api+json" \
    --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`" \
    --data '{"data": { "type": "file-refs", "attributes": { "name": "iason.txt", "description": "Iasons Lebenslauf"}, "relationships":{"terms-of-use": {"data": {"type": "terms-of-use", "id": "FREE_LICENSE"}}}}}'
```

Zunächst sendet man einen ``POST``-Request mit ``Content-Type: application/vnd.api+json`` an die URL.

Im Request-Body muss dann eine JSON:API-typische Repräsentation der neuen Datei enthalten sein. Im Erfolgsfall erhält man dann eine Repräsentation der neu angelegten Datei, die aber derzeit noch keinen Inhalt hat.

Daher muss der Inhalt in einem zweiten Request hochgeladen werden. Dazu wird – wie unter "Inhalt einer Datei aktualisieren" beschrieben – ein ``POST``-Request an die ``download-url`` geschickt.

### HTTP Request
`POST /folders/{id}/file-refs`

Im Request-Body befindet sich dann entweder eine
"multipart/form-data"-kodierte Datei oder ein JSON-API-spezifisches
"resource object".

Wenn man ein JSON-API-"resource object" verschickt, **muss** die
Relation `terms-of-use` (die Lizenz) enthalten sein. Ohne Lizenz
können keine Dateien angelegt werden.

### URL-Parameter

Parameter |  Beschreibung
--------- | -------
id        | die ID des Ordners

### Authorisierung

Ob man eine Datei erstellen darf, entscheidet die Implementierung des Ordners.

## Eine Datei kopieren

Um eine Datei zu kopieren, verwendet man die ["Variante b."](#variante-b)  für das
Anlegen von Dateien.

Zuerst benötigt man den "resource identifier" der Relation `file` der
zu kopierenden Datei. Dann schickt man ein JSON-API-"resource object"
an die URL zum Erstellen einer Datei und setzt dort diesen "resource
identifier" als Relation `file` der neuen Datei.

Wenn man selbst der Besitzer der Quelldatei ist, bleibt der Verweis
auf das `file` bestehen. Ist man nicht der Besitzer der Quelldatei,
wird auch das `file` kopiert und man selbst dessen Besitzer.

## Einen Ordner kopieren

:::danger
Diese Route ist keine JSON-API-konforme Route.
:::

```shell
curl -F "destination=<destination-ID>" \
     --url "https://example.com/folders/<source-ID>/copy" \
     --header "Authorization: Basic `echo -ne "test_dozent:testing" | base64`"
```

Um einen Ordner zu kopieren, wird diese Route verwendet, die
allerdings nicht JSON-API-konform ist. Dazu wird ein POST-Request an
die Route des Ordners geschickt, in deren Request-Body der Zielordner
spezifiziert wird. Der Request-Body muss vom
"multipart/form-data"-kodiert sein.

### HTTP Request

`POST /folders/{id}/copy`

Der "Content-Type" des Requests muss "multipart/form-data" sein. Im
Request-Body muss unter dem Schlüssel "destination" die ID des
Zielordners enthalten.

Wenn der Request erfolgreich war, bekommt man einen Status-Code 201
und einen `Location`-Header, der auf den neuen, kopierten Ordner
zeigt.

### Authorisierung
Jeder Nutzer, der den Quellordner öffnen und im Zielordner schreiben
darf, kann diese Route aufrufen.


## Einen Ordner erstellen
```shell
curl --request POST \
    --url https://example.com/folders/<ID>/folders \
    --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`" \
    --data '{"data": { "type": "folders", "attributes": {"name":"Neuer Ordner"}}}'
```

Mit dieser Route kann man einen neuen Ordner anlegen.

### HTTP Request

`POST /folders/{id}/folders`

Parameter |  Beschreibung
--------- | -------
id        | die ID des übergeordneten Ordners

### URL-Parameter

keine URL-Parameter

### Autorisierung

Ob man einen Ordner erstellen darf, entscheidet die Implementierung des übergeordneten Ordners.


## Ein "File" auslesen
```shell
curl --request GET \
    --url https://example.com/files/<ID> \
    --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`"
```

Die Dateien, die in den obigen Routen genannt werden, sind technisch gesehen nur Verweise auf tatsächliche Dateien auf der Festplatte o.ä. Auch die tatsächlichen Dateien ("files") können ausgelesen werden. Dazu verwendet man diese Route.

### HTTP Request

`GET /files/{id}`

Parameter |  Beschreibung
--------- | -------
id        | die ID des "files"

### URL-Parameter

keine URL-Parameter

### Autorisierung

Ein "file" kann ein Nutzer dann sehen, wenn eine der darauf verweisenden Dateien vom Nutzer gesehen werden kann.


## Alle Dateien eines "Files"
```shell
curl --request GET \
    --url https://example.com/files/<ID>/file-refs \
    --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`"
```

Mit dieser Route können alle Dateien, die auf dieses "file" verweisen, ausgelesen werden.

### HTTP Request

`GET /files/{id}/file-refs`

Parameter |  Beschreibung
--------- | -------
id        | die ID des "files"

### URL-Parameter

keine URL-Parameter

### Autorisierung

Die Route kann sinnvoll aufgerufen werden, wenn man eine der darauf verweisenden Dateien sehen kann.


## Alle Datei-IDs eines "Files"
```shell
curl --request GET \
    --url https://example.com/files/<ID>/relationships/file-refs \
    --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`"
```

Diese Route wird verwendet, um alle IDs der Dateien zu erhalten, die auf dieses "file" verweisen.

### HTTP Request

`GET /files/{id}/relationships/file-refs`

Parameter |  Beschreibung
--------- | -------
id        | die ID des "files"

### URL-Parameter

keine URL-Parameter

### Autorisierung

Die Route kann sinnvoll aufgerufen werden, wenn man eine der auf das "file" verweisenden Dateien sehen kann.
