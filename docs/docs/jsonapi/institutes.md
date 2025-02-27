---
title: Einrichtungen
---


Die Einrichtungen der Stud.IP-Installation können mit den folgenden Routen
abgefragt werden.

## Schema 'institutes'

Alle Einrichtungen werden in Stud.IP mit diesem Schema abgebildet. Die `id`
entspricht der in Stud.IP verwendeten `Institut_id`. Der Typ ist `institutes`.

### Attribute

Attribut      | Beschreibung
--------      | ------------
name          | der Einrichtungsname
city          | die Stadt in der die Einrichtung liegt
street        | die Anschrift (Straße) der Einrichtung
phone         | die Telefonnummer der Einrichtung
fax           | die Faxnummer der Einrichtung
url           | die URL der Webseite der Einrichtung
mkdate        | das Erstellungsdatum der Einrichtung in Stud.IP
chdate        | das letztes Änderungsdatum der Einrichtungsdaten in Stud.IP

### Relationen

keine Relationen


## Schema 'institute-memberships'

Die Mitgliedschaft in einer Einrichtung wird in Stud.IP mit diesem
Schema abgebildet.

### Attribute

Attribut        | Beschreibung
--------        | ------------
permission      | die Rolle des Nutzers in der Einrichtung
office-hours    | die Sprechzeiten des Nutzers bzgl. der Einrichtung
location        | der Raum/Ort des Nutzers bzgl. der Einrichtung
phone           | die Telefonnummer des Nutzers bzgl. der Einrichtung
fax             | die Faxnummer des Nutzers bzgl. der Einrichtung

### Relationen

Relation  | Beschreibung
--------- | ------------
institute | die Einrichtung dieser Mitgliedschaft
user      | der Nutzer dieser Mitgliedschaft

## Alle Einrichtungen

Dieser Endpoint liefert alle Einrichtungen im Stud.IP, die der
JSON:API-Nutzer mit seinen ``credentials`` auch im Stud.IP selbst
sehen darf. Die Ausgabe erfolgt paginiert und kann durch Angabe von
Offset und Limit weitergeblättert werden.

### HTTP Request

   `GET /institutes`

   ```shell
   curl --request GET \
       --url https://example.com/institutes \
       --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`" \
   ```

### URL-Parameter

Parameter    | Default | Beschreibung
---------    | ------- | ------------
page[offset] | 0       | der Offset
page[limit]  | 30      | das Limit

### Authorisierung

Jeder Nutzer darf diese Route verwenden.


## Eine Einrichtung

   ```shell
   curl --request GET \
       --url https://example.com/institutes/<INSTITUTE-ID> \
       --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`" \
   ```

Eine bestimmte Einrichtung kann einfach über diese Route ausgelesen werden.

### HTTP Request

   `GET /institutes/{id}`


       Parameter | Beschreibung
      ---------- | ------------
      id         | Die ID des Instituts

### URL-Parameter

keine URL-Parameter

### Authorisierung

Jeder Nutzer darf diese Route verwenden.


## Mitgliedschaften in einer Einrichtung

   ```shell
   curl --request GET \
       --url https://example.com/institutes/<institute-id>/memberships \
       --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`"
   ```

Gibt alle Mitgliedschaften mit den jeweiligen Daten der Nutzer zurück.

### HTTP Request
   `GET /institutes/{id}/memberships`

### Parameter

Parameter | Beschreibung
--------- | -------
id        | ID der Einrichtung

### URL-Parameter

Parameter          | Default | Beschreibung
---------          | ------- | ------------
filter[permission] | -       | Rolle des Nutzers in der Einrichtung

### Autorisierung

Jeder Nutzer darf diese Route verwenden.


## Eine Mitgliedschaft

   ```shell
   curl --request GET \
       --url https://example.com/institute-memberships/<ID> \
       --header "Authorization: Basic `echo -ne "test_autor:testing" | base64`" \
   ```

Mit dieser Route kann man eine Mitgliedschaft in einer Einrichtung auslesen.

### HTTP Request

`GET /institute-memberships/{id}`

       Parameter | Beschreibung
      ---------- | ------------
      id         | Die ID der Mitgliedschaft

### URL-Parameter

keine URL-Parameter

### Authorisierung

Jeder Nutzer darf diese Route verwenden.
