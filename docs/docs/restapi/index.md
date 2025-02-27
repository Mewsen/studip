---
title: REST-API
sidebar_label: Übersicht
---

:::danger Veraltetes Feature

Die REST-API wurde zu Stud.IP v5.0 als deprecated gekennzeichnet und wird zur Version 6.0
ausgebaut werden. Bitte verwenden Sie die [JSONAPI](../jsonapi).

:::

### Einführung

Seit Version 3.0 steht mit der REST-API eine umfangreiche HTTP-basierte Schnittstelle für das Stud.IP-System zur Verfügung. Mit dieser können grundlegende Daten von Nutzern, und Veranstaltungen abgefragt werden. Zudem ermöglicht die API, Blubber-Nachrichten, Foren-Beiträge und Wiki-Seiten zu erstellen und abzurufen.

### Administration der REST-API

Die REST-API ist in Stud.IP standardmäßig nicht aktiviert. Um sie zu aktivieren muss ein Root-Benutzer auf der Administrationsseite eine Konfigurationsvariable umschalten und REST-Routen aktivieren.

#### Anschalten der API

Nachdem man sich als Root-Benutzer angemeldet hat, wählt man über die Stud.IP-Navigation nacheinander die Punkte Admin -> System -> Konfiguration an und klickt auf der Seite "Verwaltung von Systemkonfigurationen" den Bereich "global" an. Es erscheint eine Liste mit Konfigurationsvariablen. In dieser Liste bearbeitet man die Variable API_ENABLED und setzt diese im Dialog, der sich nach dem Klick auf das Bearbeiten-Icon öffnet, auf aktiviert (Setzen des Häkchens). Nach dem Übernehmen der Änderungen ist die API eingeschaltet. Dies ist dadurch zu erkennen, dass in der linken Seitenleiste ein neuer Punkt namens "API" auftaucht.

#### Freischalten der REST-Routen

Nachdem die API eingeschaltet wurde, sind die meisten Routen immer noch nicht freigeschaltet. Um dies zu tun, wählt man als Root-Benutzer nacheinander die Navigationspunkte Admin -> System -> API aus und wählt die Ansicht "Globale Zugriffseinstellungen" aus. Hier kann für jede Route und jede HTTP-Methode, über die die Route erreichbar ist, der Zugriff gesteuert werden. Um den Zugriff zu erlauben, muss das Häkchen in der Spalte "Zugriff" gesetzt sein. Um den Zugriff auf alle Routen (und alle HTTP-Methoden für die jeweilige Route) zu erlauben, wählt man am unteren Ende der Routen-Tabelle das unterste Häkchen aus (oberhalb des Wortes "Alle") und klickt auf Speichern. Nun sind alle REST-Routen freigeschaltet.

Die REST-Routen können auch programmatisch beispielsweise in einer Migration freigeschaltet werden. Dazu gibt es seit Stud.IP 4.3 an dem `ConsumerPermissions`-Objekt die Methoden `activateRouteMap()` bzw. `deactivateRouteMap()`, welche eine `RouteMap` annimmt und alle darin enthaltenen Routen aktiviert bzw. deaktiviert.

#### Einrichtung einer Anwendung für OAuth-Authentifizierung

Um eine Anwendung für die API mit OAuth nutzen zu können, muss die Anwendung erst für OAuth freigegeben werden. Hiermit können Stud.IP Root-Benutzer festlegen, welche Anwendungen im Stud.IP System erlaubt sind.

Zum Freischalten (oder zum Widerrufen ebendieser) navigiert man im Stud.IP System als Root-Benutzer auf den Navigationspunkt "Admin", dann "System", dann "API". Man sieht eine Liste mit registrierten Konsumenten. Konsument bezeichnet hier eine Anwendungen, welche für die API freigeschaltet ist (registriert ist).

Um eine neue Anwendung für die API freizuschalten klickt man auf der Seitenleiste links auf "Neue Applikation registrieren" und füllt das sich öffnende Formular aus. Das Häkchen "Aktiviert" ganz oben im Dialog muss gesetzt sein. Der Titel der Anwendung sollte aussagekräftig sein und dem Namen der Anwendung entsprechen, um unnötige Verwirrung auf Nutzerseite zu vermeiden. Nach dem Klick auf Speichern ist die neue Anwendung freigeschaltet.

In der Liste der registrierten Konsumenten taucht nun ein Eintrag für die neue Anwendung auf. Einzelne REST-Routen können für die Anwendung mit dem Klick auf das Zahnrad-Symbol des Eintrags freigeschaltet oder abgeschaltet werden.

Die Anwendung verwendet dann folgende Konfigurationsdaten:

* `consumer_key` - wird beim Einrichten des Konsumenten erzeugt
* `consumer_secret`- wird beim Einrichten des Konsumenten erzeugt
* `request_token_url` - `https://<meine-stud.ip-url>/dispatch.php/api/oauth/request_token`
* `access_token_url` - `https://<meine-stud.ip-url>/dispatch.php/api/oauth/access_token`
* `authorize_url` - `https://<meine-stud.ip-url>/dispatch.php/api/oauth/authorize`
* `RESTAPI base URI` - `https://<meine-stud.ip-url>/api.php/`

### Verwendung der REST-API

Die REST-API kann über die HTTP-Methoden `GET`, `POST`, `PUT` und `DELETE` benutzt werden. 
Zum Lesen wird `GET` verwendet. `POST` und `PUT` sind für schreibende Zugriffe gedacht und `DELETE` zum Löschen von Daten im Stud.IP System über die API.

Sollte die Notwendigkeit bestehen, einen Request mit einer anderen als der für den API-Aufruf genutzen Methode durchzuführen, 
so kann die HTTP-Methode über den Header `X-HTTP-Method-Override` ab Stud.IP 4.3 explizit gesetzt werden. 
Dies ist zum Beispiel notwendig, wenn man über einen `GET`-Request mehr Daten übermitteln will als die Länge des Requests zulässt. 
In diesem Fall kann man einen `POST`-Request absetzen und die Methode mittels des HTTP-Headers `X-HTTP-Method-Override` explizit auf `GET` setzen.

### Anmeldung

Die meisten REST-Routen sind nur im Zusammenhang mit einem angemeldeten Stud.IP-Nutzern sinnvoll zu benutzen.

#### Anmeldung via OAuth

Bei der Anmeldung via OAuth wird ein Programm vom Nutzer autorisiert, mit seinem Nutzerkonto über die API auf Daten im Stud.IP-System zuzugreifen.

Damit eine Anwendung via OAuth genutzt werden kann, muss sie zuerst dafür freigeschaltet werden und über einen OAuth `consumer key` 
und OAuth `consumer secret` verfügen (siehe Abschnitt ["Einrichtung einer Anwendung für OAuth-Authentifizierung"](#einrichtung-einer-anwendung-für-oauth-authentifizierung)).

Nach der Autorisierung durch den Nutzer erhält die Anwendung eigene Zugangsdaten, die dauerhaft gespeichert werden und zur Anmeldung genutzt werden können.


#### Anmeldung mit Nutzername und Passwort

Wird eine Anmeldung via Nutzername und Passwort durchgeführt, müssen für jede API-Anfrage die Zugangsdaten mitgesendet werden. 
Nutzername und Passwort eines Stud.IP Nutzers werden hierbei via HTTP Basic Authentication an die API gesendet. 
Da bei der HTTP Basic Authentication weder eine Verschlüsselung der Daten noch ein Hashing des Passwortes stattfindet, 
sollte die API über HTTPS aufgerufen werden, um einen Zugriff auf die Nutzerdaten durch Dritte zu erschweren.

Die Anmeldung via Nutzername und Passwort ist zwar auf Clientseite einfacher zu realisieren, sollte aber nicht verwendet werden, 
da der Nutzername und das Passwort in die Hände von anderen Programmen gelegt werden, was ein Sicherheitsrisiko darstellen kann.


### Abfragen

REST-Routen sind unterhalb des Pfades /api.php/ der Stud.IP-Installation zu erreichen. Liegt ein Stud.IP System unter der Adresse https://studip.example.org, so wäre dessen API unter https://studip.example.org/api.php/ erreichbar. In Stud.IP-Systemen, deren URL-Umleitungen nicht definiert sind, wäre die API zum Beispiel unter https://studip.example.org/public/api.php/ erreichbar.

Eine Abfrage geschieht durch das Aufrufen einer Route, zum Beispiel /user. Der komplette Pfad zur Route könnte beispielsweise so lauten: https://studip.example.org/api.php/user. Diese Anfrage kann via `GET` ausgeführt werden.

#### verwendbare Parameter bei Abfragen von Listen

Die folgenden Parameter sind sinnvoll, wenn Routen abgefragt werden, welche Listen von Objekten zurückliefern.

| Parameter | Beschreibung |
| ---- | ---- |
| `offset` | gibt die Startposition innerhalb der Liste an |
| `limit` | gibt die maximale Anzahl an Elementen an, die zurückgeliefert werden soll |

### Antwortformate

#### Einzelnes Objekt

Die Antwortformate der API sind unterschiedlich. Wird nur ein Objekt abgefragt, so wird dieses direkt zurückgeliefert. Die Anfrage der REST-Route /user liefert beispielsweise folgende Daten zurück:

```json
    {
        "phone" : "",
        "datafields" : [],
        "privadr" : "",
        "username" : "root@studip",
        "name" : {
            "username" : "root@studip",
            "formatted" : "Root Studip",
            "suffix" : "",
            "family" : "Studip",
            "prefix" : "",
            "given" : "Root"
        },
        "perms" : "root",
        "avatar_original" : "https://studip.example.org/pictures/user/nobody_original.png?d=0",
        "skype_show" : null,
        "avatar_small" : "https://studip.example.org/pictures/user/nobody_small.png?d=1476444962",
        "homepage" : "",
        "email" : "root@localhost",
        "user_id" : "76ed43ef286fb55cf9e41beadb484a9f",
        "avatar_normal" : "https://studip.example.org/pictures/user/nobody_normal.png?d=1476444962",
        "avatar_medium" : "https://studip.example.org/pictures/user/nobody_medium.png?d=1476444962",
        "skype" : ""
    }
```

Es handelt sich um ein User-Objekt. Auf gleiche Art und Weise können auch zum Beispiel einzelne Veranstaltungs- oder Blubber-Objekte über die jeweiligen Routen abgefragt werden.

#### Liste von Objekten

Es gibt REST-Routen, die eine Liste von Objekten zurückliefern, beispielsweise alle Blubber-Objekte einer Veranstaltung. In diesem Fall sieht das Antwortformat anders aus. Es wird ein Listen-Objekt zurückgegeben, welches die folgende Struktur besitzt:

```json
{
        "pagination" : {
            "total" : 0,
            "limit" : 20,
            "offset" : 0
        },
        "collection" : {
        }
    }
```

Das zurückgelieferte Objekt besitzt zwei Unterobjekte, wovon das erste Informationen zur Paginierung liefert und das zweite die eigentlichen Objekte beinhaltet. Anhand der Paginierung kann die Anzahl der zu ladenden Objekte begrenzt und gesteuert werden.

Das Unterobjekt "collection" besitzt für jedes Listen-Objekt ein eigenes Attribut. Das Attribut verweist auf die API-Route, mit der das jeweilige Objekt direkt abgefragt werden könnte.

##### Beispiel zum Aufruf der API

Zuerst wird die Route `/discovery` aufgerufen, um zu erfahren, welche Routen freigeschaltet sind. 
Diese Route liefert eine List mit den aktivierten Routen zurück, welche von der Anwendung genutzt werden können.

Im Beispiel sollen alle Blubber-Streams des aktuell angemeldeten Nutzers ermittelt werden. Dazu wird zuerst die Route `/user` aufgerufen, 
um Informationen des aktuellen Nutzers zu finden. 
In der Antwort ist die user-ID enthalten, welche für die nächste Abfrage relevant ist: Dem Abruf aller Blubber-Streams eines Nutzers. 
Dazu wird die Route `/user/:user_id/blubber` verwendet, welche alle Blubber-Objekte eines Nutzers zurückliefert.


#### Paginierung

Mit den Request-Parametern `offset` und `limit kann die Paginierung der Daten gesteuert werden. Mittels "offset" kann die 
Startposition innerhalb einer Datensammlung festgelegt werden. 
Der Parameter `limit` gibt die maximale Anzahl an Einträgen an, die zurückgegeben werden soll.

#### Statuscodes

Die API liefert HTTP Statuscodes zurück, anhand derer ermittelt werden kann, ob die Anfrage erfolgreich ausgeführt wurde oder nicht.

#### Fehlerverhalten

Im Fehlerfall wird kein JSON zurückgeliefert. Stattdessen werden einfache, kurze Zeichenketten zurückgeliefert, welche den Fehlerfall beschreiben.

Im Falle, dass die REST-Route einen `PHP Fatal Error` verursacht, wird dieser angezeigt.


### Beispiel: Erstellung einer Anwendung für die Stud.IP API (inklusive OAuth)

Auf die Stud.IP API kann je nach Programmiersprache mit relativ wenig Code zugegriffen werden. 
Hier wird die Erstellung einer kleinen Anwendung für Stud.IP beschrieben, welche sich via OAuth authentifiziert und danach Daten 
des angemeldeten Nutzers abfragt.


#### Benötigte Module

Für diese Anwendung werden die Python-Module json, requests und rauth benötigt. Python3 sollte verwendet werden.


#### Import und Initialisierung

Zuerst werden due benötigten Module geladen und ein Objekt für die OAuth1-Authentifizierung gebaut:

```python
import json
import requests
from rauth import OAuth1Service

studip = OAuth1Service(
    name='Stud.IP',
    consumer_key='CONSUMER_KEY_DER_ANWENDUNG',
    consumer_secret='CONSUMER_SECRET_DER_ANWENDUNG',
    request_token_url='http://studip.example.org/dispatch.php/api/oauth/request_token',
    access_token_url='http://studip.example.org/dispatch.php/api/oauth/access_token',
    authorize_url='http://studip.example.org/dispatch.php/api/oauth/authorize',
    base_url='http://studip.example.org/api.php/'
)
```


#### Anfordern von Request-Token und Autorisierung

Im Anschluss kann erst ein Request-Token angefordert werden und die URL zur Autorisierung abgerufen werden. Diese muss im Browser geöffnet werden. Sofern man nicht im Stud.IP System angemeldet ist, müssen auf der Seite der Stud.IP Installation Nutzername und Passwort eingegeben werden, um die Anwendung zu autorisieren.

```python
request_token, request_token_secret = studip.get_request_token()

authorize_url = studip.get_authorize_url(request_token)

print('Bitte die folgende URL aufrufen: ' + authorize_url)

input('Wenn die URL aufgerufen wurde und diese Anwendung zugelassen wurde, bitte zum Fortfahren die Eingabetaste drücken!')
```


#### Erstellen einer authentifizierten Sitzung

Nach der Autorisierung kann eine authentifizierte Sitzung gestartet werden, mit welcher Abfragen an die Stud.IP API gemacht werden können:

```python
session = studip.get_auth_session(
    request_token,
    request_token_secret,
    method='`POST`',
    data={'oauth_verifier': *}
)
```

Da Stud.IP auch ohne oauth_verifier Abfragen der API erlaubt, kann dieser Parameter weggelassen werden.


#### Abfragen der API

Mit der Sitzung, die in obigem Abschnitt erzeugt wurde, können nun die Routen der Stud.IP API abgefragt werden. Beispielsweise kann die Route /user aufgerufen werden, um Daten zum angemeldeten Nutzer zu erhalten:

```python
user_data = session.`GET`('user')
```



### REST-API Routen

Im Folgenden werden die verfügbaren REST-API Routen mitsamt den verwendbaren HTTP-Methoden dargestellt.

#### Systemrouten

##### `GET` /discovery

Liefert eine List mit verfügbaren Routen zurück.

###### Antwortformat

```json
{
   "/studip/settings" : {
      "`GET`" : "Grundlegende Systemeinstellungen"
   },
   "/user" : {
      "`GET`" : "getUser - retrieves data of a user"
   },
   "/discovery" : {
      "`GET`" : "Schnittstellenbeschreibung"
   },
   "/messages" : {
      "`POST`" : "Schreibt eine neue Nachricht."
   },
   "/studip/news" : {
      "`GET`" : "Globale News auslesen",
      "`POST`" : "News anlegen"
   }
}
```


##### `GET` /studip/colors

Liefert die Farbeinstellungen des Stud.IP Systems zurück. Es handelt sich um drei fest vorgegebene Farbwerte für den Hintergrund, sowie für dunkle und helle Bereiche des Stud.IP Systems.

###### Antwortformat

```json
{
   "background" : "#e1e4e9",
   "dark" : "#34578c",
   "light" : "#899ab9"
}
```


##### `GET` /studip/news

Liefert eine Liste mit globalen Ankündigungen (Ankündigungen für das gesamte Stud.IP System) zurück.

###### Antwortformat

```json
{
   "pagination" : {
      "limit" : 20,
      "total" : 1,
      "offset" : 0
   },
   "collection" : {
      "/api.php/news/29f2932ce32be989022c6f43b866e744" : {
         "comments" : "/api.php/news/29f2932ce32be989022c6f43b866e744/comments",
         "news_id" : "29f2932ce32be989022c6f43b866e744",
         "expire" : "14562502",
         "date" : "1468409976",
         "chdate_uid" : "",
         "body_html" : "<div class=\"formatted-content\">Das Stud.IP-Team heisst sie herzlich willkommen. <br>Bitte schauen Sie sich ruhig um!<br><br>Wenn Sie das System selbst installiert haben und diese News sehen, haben Sie die Demonstrationsdaten in die Datenbank eingefügt. Wenn Sie produktiv mit dem System arbeiten wollen, sollten Sie diese Daten später wieder löschen, da die Passwörter der Accounts (vor allem des root-Accounts) öffentlich bekannt sind.</div>",
         "mkdate" : "1468409976",
         "topic" : "Herzlich Willkommen!",
         "body" : "Das Stud.IP-Team heisst sie herzlich willkommen. \r\nBitte schauen Sie sich ruhig um!\r\n\r\nWenn Sie das System selbst installiert haben und diese News sehen, haben Sie die Demonstrationsdaten in die Datenbank eingefügt. Wenn Sie produktiv mit dem System arbeiten wollen, sollten Sie diese Daten später wieder löschen, da die Passwörter der Accounts (vor allem des root-Accounts) öffentlich bekannt sind.",
         "ranges" : [
            "/api.php/user/76ed43ef286fb55cf9e41beadb484a9f/news",
            "/api.php/studip/news"
         ],
         "comments_count" : 0,
         "allow_comments" : "1",
         "chdate" : "1468409976",
         "user_id" : "76ed43ef286fb55cf9e41beadb484a9f"
      }
   }
}
```


##### `POST` /studip/news

Legt eine neue globale Ankündigung an.

Der Titel der Ankündigung wird über den Parameter `topic` gesetzt, der Inhalt über `body`. 
Es gibt zwei optionale Parameter, `expire` und `allow_comments`. "expire" gibt die Zeitspanne in Sekunden ab dem aktuellen Datum an, 
an dem die Ankündigung ablaufen soll. Ist der Parameter `allow_comments` auf 1 gesetzt, so sind Kommentare erlaubt. 
Standardmäßig ist er auf 0 gesetzt.

###### Parameter

|**`POST`-Parameter** |**Format** |**Beschreibung**
| ---- | ---- | ---- |
| `topic` | `String` | Der Titel der Ankündigung
| `body` | `String` | Der Inhalt der Ankündigung
| `expire` | `Integer` | Ablaufdatum der Nachricht (in Sekunden vom aktuellen Datum gerechnet)
| `allow_comments` | `Integer` | Gibt an, ob Kommentare erlaubt sind: 1 = erlaubt, 0 = nicht erlaubt



##### `GET` /studip/settings

Liefert die Werte bestimmter Konfigurationsvariablen zurück.

###### Antwortformat
```json
{
   "TERMIN_TYP" : {
      "2" : {
         "name" : "Vorbesprechung",
         "color" : "#b02e7c",
         "sitzung" : 0
      },
      "4" : {
         "name" : "Exkursion",
         "color" : "#f26e00",
         "sitzung" : 0
      },
      "6" : {
         "name" : "Sondersitzung",
         "color" : "#a85d45",
         "sitzung" : 0
      },
      "7" : {
         "name" : "Vorlesung",
         "color" : "#ca9eaf",
         "sitzung" : 1
      },
      "3" : {
         "color" : "#129c94",
         "name" : "Klausur",
         "sitzung" : 0
      },
      "1" : {
         "sitzung" : 1,
         "color" : "#682c8b",
         "name" : "Sitzung"
      },
      "5" : {
         "sitzung" : 0,
         "name" : "anderer Termin",
         "color" : "#008512"
      }
   },
   "PERS_TERMIN_KAT" : {
      "10" : {
         "color" : "#66b570",
         "name" : "Verabredung"
      },
      "5" : {
         "color" : "#f26e00",
         "name" : "Exkursion"
      },
      "12" : {
         "color" : "#d082b0",
         "name" : "Familie"
      },
      "2" : {
         "color" : "#682c8b",
         "name" : "Sitzung"
      },
      "4" : {
         "color" : "#129c94",
         "name" : "Klausur"
      },
      "1" : {
         "name" : "Sonstiges",
         "color" : "#008512"
      },
      "14" : {
         "name" : "Reise",
         "color" : "#f7a866"
      },
      "15" : {
         "name" : "Vorlesung",
         "color" : "#ca9eaf"
      },
      "8" : {
         "color" : "#d60000",
         "name" : "Telefonat"
      },
      "9" : {
         "color" : "#ffbd33",
         "name" : "Besprechung"
      },
      "11" : {
         "color" : "#a480b9",
         "name" : "Geburtstag"
      },
      "13" : {
         "name" : "Urlaub",
         "color" : "#70c3bf"
      },
      "3" : {
         "color" : "#b02e7c",
         "name" : "Vorbesprechung"
      },
      "7" : {
         "color" : "#6ead10",
         "name" : "Prüfung"
      },
      "6" : {
         "color" : "#a85d45",
         "name" : "Sondersitzung"
      }
   },
   "SEM_TYPE" : {
      "9" : {
         "class" : "2",
         "name" : "Projektgruppe"
      },
      "11" : {
         "class" : "3",
         "name" : "Kulturforum"
      },
      "13" : {
         "name" : "sonstige",
         "class" : "3"
      },
      "7" : {
         "name" : "sonstige",
         "class" : "1"
      },
      "3" : {
         "class" : "1",
         "name" : "Übung"
      },
      "6" : {
         "class" : "1",
         "name" : "Forschungsgruppe"
      },
      "10" : {
         "class" : "2",
         "name" : "sonstige"
      },
      "5" : {
         "class" : "1",
         "name" : "Colloquium"
      },
      "12" : {
         "class" : "3",
         "name" : "Veranstaltungsboard"
      },
      "4" : {
         "class" : "1",
         "name" : "Praktikum"
      },
      "2" : {
         "name" : "Seminar",
         "class" : "1"
      },
      "1" : {
         "name" : "Vorlesung",
         "class" : "1"
      },
      "8" : {
         "class" : "2",
         "name" : "Gremium"
      },
      "99" : {
         "name" : "Studiengruppe",
         "class" : "99"
      }
   },
   "SUPPORT_EMAIL" : "<please insert your general contact mail-adress here>",
   "ALLOW_CHANGE_NAME" : true,
   "ALLOW_CHANGE_USERNAME" : true,
   "ALLOW_CHANGE_EMAIL" : true,
   "UNI_NAME_CLEAN" : "Stud.IP trunk",
   "TITLES" : {
      "accepted" : [
         "Vorläufig akzeptierte Person",
         "Vorläufig akzeptierte Personen"
      ],
      "deputy" : [
         "Vertretung",
         "Vertretungen"
      ],
      "autor" : [
         "Studierende",
         "Studierende"
      ],
      "dozent" : [
         "Lehrende",
         "Lehrende"
      ],
      "user" : [
         "Leser/-in",
         "Leser/-innen"
      ],
      "tutor" : [
         "Tutor/-in",
         "Tutor/-innen"
      ]
   },
   "SEM_CLASS" : {
      "2" : {
         "bereiche" : "0",
         "create_description" : "",
         "description" : "Hier finden Sie virtuelle Veranstaltungen zu verschiedenen Gremien an der Universit&auml;t",
         "name" : "Organisation",
         "scm" : null,
         "title_autor_plural" : null,
         "wiki" : "CoreWiki",
         "id" : "2",
         "write_access_nobody" : "0",
         "default_write_level" : "2",
         "default_read_level" : "2",
         "schedule" : "CoreSchedule",
         "studygroup_mode" : "0",
         "title_tutor_plural" : "Mitglieder",
         "modules" : {
            "CoreResources" : {
               "sticky" : "0",
               "activated" : "1"
            },
            "CoreAdmin" : {
               "activated" : "1",
               "sticky" : "1"
            },
            "CoreSchedule" : {
               "sticky" : "0",
               "activated" : "1"
            },
            "CoreForum" : {
               "activated" : "1",
               "sticky" : "0"
            },
            "CoreParticipants" : {
               "sticky" : "0",
               "activated" : "1"
            },
            "CoreScm" : {
               "activated" : "0",
               "sticky" : "1"
            },
            "CoreElearningInterface" : {
               "sticky" : "1",
               "activated" : "0"
            },
            "CoreStudygroupParticipants" : {
               "activated" : "0",
               "sticky" : "1"
            },
            "CoreLiterature" : {
               "activated" : "0",
               "sticky" : "1"
            },
            "CoreDocuments" : {
               "sticky" : "0",
               "activated" : "1"
            },
            "CoreCalendar" : {
               "activated" : "0",
               "sticky" : "1"
            },
            "CoreWiki" : {
               "activated" : "1",
               "sticky" : "0"
            },
            "CoreStudygroupAdmin" : {
               "activated" : "0",
               "sticky" : "1"
            },
            "CoreOverview" : {
               "sticky" : "1",
               "activated" : "1"
            }
         },
         "title_dozent" : "LeiterIn",
         "module" : "0",
         "forum" : "CoreForum",
         "documents" : "CoreDocuments",
         "elearning_interface" : null,
         "overview" : "CoreOverview",
         "topic_create_autor" : "0",
         "admission_type_default" : "0",
         "title_dozent_plural" : "LeiterInnen",
         "course_creation_forbidden" : "0",
         "admin" : "CoreAdmin",
         "title_tutor" : "Mitglied",
         "chdate" : "1366882198",
         "compact_mode" : "1",
         "mkdate" : "1366882120",
         "literature" : null,
         "visible" : "1",
         "resources" : "CoreResources",
         "admission_prelim_default" : "0",
         "calendar" : null,
         "turnus_default" : "-1",
         "workgroup_mode" : "1",
         "show_browse" : "1",
         "participants" : "CoreParticipants",
         "show_raumzeit" : "1",
         "title_autor" : null,
         "only_inst_user" : "0"
      },
      "3" : {
         "write_access_nobody" : "1",
         "default_write_level" : "1",
         "id" : "3",
         "wiki" : "CoreWiki",
         "title_autor_plural" : null,
         "scm" : null,
         "name" : "Community",
         "description" : "Hier finden Sie virtuelle Veranstaltungen zu unterschiedlichen Themen",
         "bereiche" : "0",
         "create_description" : "",
         "overview" : "CoreOverview",
         "documents" : "CoreDocuments",
         "elearning_interface" : null,
         "module" : "0",
         "title_dozent" : null,
         "forum" : "CoreForum",
         "title_tutor_plural" : null,
         "modules" : {
            "CoreAdmin" : {
               "activated" : 1,
               "sticky" : 1
            },
            "CoreOverview" : {
               "sticky" : 1,
               "activated" : 1
            }
         },
         "studygroup_mode" : "0",
         "schedule" : "CoreSchedule",
         "default_read_level" : "1",
         "admission_prelim_default" : "0",
         "calendar" : null,
         "literature" : "CoreLiterature",
         "visible" : "1",
         "resources" : "CoreResources",
         "compact_mode" : "1",
         "mkdate" : "1366882120",
         "admin" : "CoreAdmin",
         "chdate" : "1366882120",
         "title_tutor" : null,
         "title_dozent_plural" : null,
         "course_creation_forbidden" : "0",
         "topic_create_autor" : "0",
         "admission_type_default" : "0",
         "only_inst_user" : "0",
         "title_autor" : null,
         "participants" : "CoreParticipants",
         "show_raumzeit" : "1",
         "workgroup_mode" : "0",
         "show_browse" : "1",
         "turnus_default" : "-1"
      },
      "99" : {
         "show_raumzeit" : "0",
         "participants" : "CoreStudygroupParticipants",
         "title_autor" : "Mitglied",
         "only_inst_user" : "0",
         "show_browse" : "0",
         "workgroup_mode" : "0",
         "turnus_default" : "0",
         "visible" : "0",
         "resources" : null,
         "literature" : null,
         "calendar" : null,
         "admission_prelim_default" : "0",
         "title_dozent_plural" : "GruppengründerInnen",
         "course_creation_forbidden" : "1",
         "admission_type_default" : "0",
         "topic_create_autor" : "1",
         "mkdate" : "1366882120",
         "compact_mode" : "0",
         "chdate" : "1462287763",
         "title_tutor" : "ModeratorIn",
         "admin" : "CoreStudygroupAdmin",
         "elearning_interface" : null,
         "documents" : "CoreDocuments",
         "overview" : "CoreOverview",
         "studygroup_mode" : "1",
         "schedule" : "CoreSchedule",
         "default_read_level" : "0",
         "forum" : "CoreForum",
         "module" : "0",
         "title_dozent" : "GruppengründerIn",
         "modules" : {
            "CoreScm" : {
               "sticky" : "0",
               "activated" : "1"
            },
            "CoreElearningInterface" : {
               "sticky" : "1",
               "activated" : "0"
            },
            "CoreAdmin" : {
               "sticky" : "1",
               "activated" : "0"
            },
            "CoreSchedule" : {
               "sticky" : "0",
               "activated" : "1"
            },
            "CoreResources" : {
               "sticky" : "1",
               "activated" : "0"
            },
            "CoreForum" : {
               "sticky" : "0",
               "activated" : "1"
            },
            "CoreParticipants" : {
               "sticky" : "1",
               "activated" : "0"
            },
            "CoreWiki" : {
               "activated" : "1",
               "sticky" : "0"
            },
            "CoreStudygroupAdmin" : {
               "activated" : "1",
               "sticky" : "1"
            },
            "CoreOverview" : {
               "activated" : "1",
               "sticky" : "1"
            },
            "CoreLiterature" : {
               "sticky" : "1",
               "activated" : "0"
            },
            "CoreStudygroupParticipants" : {
               "sticky" : "1",
               "activated" : "1"
            },
            "CoreCalendar" : {
               "activated" : "0",
               "sticky" : "1"
            },
            "CoreDocuments" : {
               "activated" : "1",
               "sticky" : "0"
            }
         },
         "title_tutor_plural" : "ModeratorInnen",
         "wiki" : "CoreWiki",
         "title_autor_plural" : "Mitglieder",
         "default_write_level" : "0",
         "write_access_nobody" : "0",
         "id" : "99",
         "description" : "",
         "name" : "Studiengruppen",
         "create_description" : "",
         "bereiche" : "0",
         "scm" : "CoreScm"
      },
      "1" : {
         "overview" : "CoreOverview",
         "elearning_interface" : "CoreElearningInterface",
         "documents" : "CoreDocuments",
         "modules" : {
            "CoreScm" : {
               "activated" : "1",
               "sticky" : "0"
            },
            "CoreElearningInterface" : {
               "activated" : "1",
               "sticky" : "0"
            },
            "CoreResources" : {
               "activated" : "1",
               "sticky" : "0"
            },
            "CoreSchedule" : {
               "activated" : "1",
               "sticky" : "0"
            },
            "CoreAdmin" : {
               "activated" : "1",
               "sticky" : "1"
            },
            "CoreParticipants" : {
               "activated" : "1",
               "sticky" : "0"
            },
            "CoreForum" : {
               "activated" : "1",
               "sticky" : "0"
            },
            "CoreWiki" : {
               "activated" : "1",
               "sticky" : "0"
            },
            "CoreOverview" : {
               "activated" : "1",
               "sticky" : "1"
            },
            "CoreStudygroupAdmin" : {
               "sticky" : "1",
               "activated" : "0"
            },
            "CoreStudygroupParticipants" : {
               "sticky" : "1",
               "activated" : "0"
            },
            "CoreLiterature" : {
               "activated" : "1",
               "sticky" : "0"
            },
            "CoreDocuments" : {
               "activated" : "1",
               "sticky" : "0"
            },
            "CoreCalendar" : {
               "activated" : "1",
               "sticky" : "0"
            }
         },
         "title_tutor_plural" : null,
         "forum" : "CoreForum",
         "module" : "0",
         "title_dozent" : null,
         "default_read_level" : "1",
         "schedule" : "CoreSchedule",
         "studygroup_mode" : "0",
         "id" : "1",
         "default_write_level" : "1",
         "write_access_nobody" : "0",
         "title_autor_plural" : null,
         "wiki" : "CoreWiki",
         "scm" : "CoreScm",
         "create_description" : "",
         "bereiche" : "1",
         "description" : "Hier finden Sie alle in Stud.IP registrierten Lehrveranstaltungen",
         "name" : "Lehre",
         "title_autor" : null,
         "only_inst_user" : "1",
         "show_raumzeit" : "1",
         "participants" : "CoreParticipants",
         "turnus_default" : "0",
         "workgroup_mode" : "0",
         "show_browse" : "1",
         "calendar" : "CoreCalendar",
         "admission_prelim_default" : "0",
         "visible" : "1",
         "resources" : "CoreResources",
         "literature" : "CoreLiterature",
         "title_tutor" : null,
         "chdate" : "1366882169",
         "admin" : "CoreAdmin",
         "mkdate" : "1366882120",
         "compact_mode" : "0",
         "admission_type_default" : "0",
         "topic_create_autor" : "0",
         "title_dozent_plural" : null,
         "course_creation_forbidden" : "0"
      }
   },
   "ALLOW_CHANGE_TITLE" : true,
   "INST_TYPE" : {
      "1" : {
         "name" : "Einrichtung"
      },
      "8" : {
         "name" : "Arbeitsgruppe"
      },
      "2" : {
         "name" : "Zentrum"
      },
      "4" : {
         "name" : "Abteilung"
      },
      "5" : {
         "name" : "Fachbereich"
      },
      "6" : {
         "name" : "Seminar"
      },
      "7" : {
         "name" : "Fakultät"
      },
      "3" : {
         "name" : "Lehrstuhl"
      }
   }
}
```




#### User - Daten zu einem Nutzer

##### `GET` /user

Liefert Daten des aktuellen Benutzers zurück.

Diese REST-Route ist vergleichbar mit dem Aufruf `User::findCurrent()` im Stud.IP System. 
Sie liefert ein Objekt mit Daten des Nutzers zurück, welcher sich über die API angemeldet hat.


###### Antwortformat
```json
{
   "name" : {
      "family" : "Studip",
      "given" : "Root",
      "formatted" : "Root Studip",
      "prefix" : "",
      "username" : "root@studip",
      "suffix" : ""
   },
   "username" : "root@studip",
   "avatar_normal" : "https://studip.example.org/pictures/user/nobody_normal.png?d=1476444962",
   "avatar_medium" : "https://studip.example.org/pictures/user/nobody_medium.png?d=1476444962",
   "email" : "root@localhost",
   "homepage" : "",
   "privadr" : "",
   "phone" : "",
   "avatar_original" : "https://studip.example.org/pictures/user/nobody_original.png?d=0",
   "user_id" : "76ed43ef286fb55cf9e41beadb484a9f",
   "skype" : "",
   "datafields" : [],
   "skype_show" : null,
   "avatar_small" : "https://studip.example.org/pictures/user/nobody_small.png?d=1476444962",
   "perms" : "root"
}
```

##### `GET` /user/:user_id

Liefert Daten eines Nutzers zurück, welcher anhand seiner Nutzer-ID referenziert wird, vergleichbar mit User::find() im Stud.IP System.

###### Parameter

| **Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| `user_id` |string, 32 Zeichen | Die Nutzer-ID des Nutzers

###### Antwortformat

```json
{
   "name" : {
      "family" : "Studip",
      "given" : "Root",
      "formatted" : "Root Studip",
      "prefix" : "",
      "username" : "root@studip",
      "suffix" : ""
   },
   "username" : "root@studip",
   "avatar_normal" : "https://studip.example.org/pictures/user/nobody_normal.png?d=1476444962",
   "avatar_medium" : "https://studip.example.org/pictures/user/nobody_medium.png?d=1476444962",
   "email" : "root@localhost",
   "homepage" : "",
   "privadr" : "",
   "phone" : "",
   "avatar_original" : "https://studip.example.org/pictures/user/nobody_original.png?d=0",
   "user_id" : "76ed43ef286fb55cf9e41beadb484a9f",
   "skype" : "",
   "datafields" : [],
   "skype_show" : null,
   "avatar_small" : "https://studip.example.org/pictures/user/nobody_small.png?d=1476444962",
   "perms" : "root"
}
```


##### `DELETE` /user/:user_id

Sofern der API-Nutzer Root-Berechtigungen hat und nicht vorhat, sich selbst zu löschen, kann er durch Aufruf dieser API-Route einen Nutzer löschen. Versucht der Nutzer, sich selbst zu löschen, wird der Fehlercode 400 zurückgeliefert, hat der Nutzer keine Root-Berechtigungen, wird Fehlercode 401 zurückgeliefert.

###### Parameter

| **Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| user_id |string, 32 Zeichen | Die Nutzer-ID des Nutzers


##### `GET` /user/:user_id/blubber

Diese Route liefert alle Blubber-Objekte zurück, welche im Blubber-Stream eines Nutzers erstellt wurden.

###### Parameter


| **Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| user_id |string, 32 Zeichen | Die Nutzer-ID des Nutzers


###### Antwortformat

```json
{
    "pagination" : {
        "total" : 2,
        "limit" : 20,
        "offset" : 0
    },
    "collection" : {
        "/api.php/blubber/posting/ecab929ef0dfaeca159802c018826a25" : {
            "chdate" : "1477299897",
            "blubber_id" : "ecab929ef0dfaeca159802c018826a25",
            "mkdate" : "1477299897",
            "content" : "Noch ein Test-Blubber",
            "comments" : "/api.php/blubber/posting/ecab929ef0dfaeca159802c018826a25/comments",
            "content_html" : "<div class=\"formatted-content\">Noch ein Test-Blubber</div>",
            "tags" : [],
            "root_id" : "ecab929ef0dfaeca159802c018826a25",
            "comments_count" : "0",
            "context_type" : "public",
            "author" : {
                "name" : {
                "prefix" : "",
                "username" : "root@studip",
                "family" : "Studip",
                "formatted" : "Root Studip",
                "suffix" : "",
                "given" : "Root"
                },
                "id" : "76ed43ef286fb55cf9e41beadb484a9f",
                "avatar_normal" : "https://studip.example.org/pictures/user/nobody_normal.png?d=1476444962",
                "href" : "/api.php/user/76ed43ef286fb55cf9e41beadb484a9f",
                "avatar_medium" : "https://studip.example.org/pictures/user/nobody_medium.png?d=1476444962",
                "avatar_small" : "https://studip.example.org/pictures/user/nobody_small.png?d=1476444962",
                "avatar_original" : "https://studip.example.org/pictures/user/nobody_original.png?d=0"
            },
            "reshares" : []
        },
        "/api.php/blubber/posting/5f5f5d4f49f6122a0f2761ecf887d912" : {
            "comments" : "/api.php/blubber/posting/5f5f5d4f49f6122a0f2761ecf887d912/comments",
            "content_html" : "<div class=\"formatted-content\">Dies ist ein Test-Blubber</div>",
            "content" : "Dies ist ein Test-Blubber",
            "mkdate" : "1477299878",
            "blubber_id" : "5f5f5d4f49f6122a0f2761ecf887d912",
            "chdate" : "1477299878",
            "reshares" : [],
            "author" : {
                "avatar_normal" : "https://studip.example.org/pictures/user/nobody_normal.png?d=1476444962",
                "href" : "/api.php/user/76ed43ef286fb55cf9e41beadb484a9f",
                "avatar_medium" : "https://studip.example.org/pictures/user/nobody_medium.png?d=1476444962",
                "id" : "76ed43ef286fb55cf9e41beadb484a9f",
                "name" : {
                "prefix" : "",
                "family" : "Studip",
                "username" : "root@studip",
                "given" : "Root",
                "suffix" : "",
                "formatted" : "Root Studip"
                },
                "avatar_small" : "https://studip.example.org/pictures/user/nobody_small.png?d=1476444962",
                "avatar_original" : "https://studip.example.org/pictures/user/nobody_original.png?d=0"
            },
            "context_type" : "public",
            "tags" : [],
            "root_id" : "5f5f5d4f49f6122a0f2761ecf887d912",
            "comments_count" : "0"
        }
    }
}
```


##### `POST` /user/:user_id/blubber

Erstellt ein neues Blubber-Objekt im Blubber-Stream eines Nutzers.

Der Inhalt des Blubbers wird einfach als Parameter namens "content" der HTTP `POST` Anfrage gesendet. Eine spezielle Formatierung ist nicht notwendig.

###### Parameter


| **Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| user_id |String, 32 Zeichen | Die Nutzer-ID des Nutzers


| **`POST`-Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| content | String | Der Inhalt des Blubbers


##### `GET` /user/:user_id/contacts

Es wird eine Liste mit Kontakten eines Nutzers zurückgeliefert, welche den Namen und die URLs zum Profilbild in mehreren Größen enthält. Darüber hinaus werden Nutzer-ID und API-URL zum Aufruf weiterer Daten des Nutzers mitgeteilt.

###### Parameter


| **Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| user_id |String, 32 Zeichen | Die Nutzer-ID des Nutzers

###### Antwortformat

```json
{
   "collection" : [
      {
         "name" : {
            "prefix" : "",
            "username" : "test_admin",
            "family" : "Admin",
            "formatted" : "Testaccount Admin",
            "given" : "Testaccount",
            "suffix" : ""
         },
         "avatar_original" : "https://studip.example.org/pictures/user/nobody_original.png?d=0",
         "href" : "/api.php/user/6235c46eb9e962866ebdceece739ace5",
         "avatar_normal" : "https://studip.example.org/pictures/user/nobody_normal.png?d=1476444962",
         "avatar_small" : "https://studip.example.org/pictures/user/nobody_small.png?d=1476444962",
         "id" : "6235c46eb9e962866ebdceece739ace5",
         "avatar_medium" : "https://studip.example.org/pictures/user/nobody_medium.png?d=1476444962"
      },
      {
         "avatar_medium" : "https://studip.example.org/pictures/user/nobody_medium.png?d=1476444962",
         "id" : "e7a0a84b161f3e8c09b4a0a2e8a58147",
         "avatar_normal" : "https://studip.example.org/pictures/user/nobody_normal.png?d=1476444962",
         "avatar_small" : "https://studip.example.org/pictures/user/nobody_small.png?d=1476444962",
         "href" : "/api.php/user/e7a0a84b161f3e8c09b4a0a2e8a58147",
         "avatar_original" : "https://studip.example.org/pictures/user/nobody_original.png?d=0",
         "name" : {
            "formatted" : "Testaccount Autor",
            "given" : "Testaccount",
            "suffix" : "",
            "prefix" : "",
            "username" : "test_autor",
            "family" : "Autor"
         }
      },
      {
         "avatar_small" : "https://studip.example.org/pictures/user/nobody_small.png?d=1476444962",
         "avatar_normal" : "https://studip.example.org/pictures/user/nobody_normal.png?d=1476444962",
         "id" : "205f3efb7997a0fc9755da2b535038da",
         "avatar_medium" : "https://studip.example.org/pictures/user/nobody_medium.png?d=1476444962",
         "name" : {
            "given" : "Testaccount",
            "suffix" : "",
            "formatted" : "Testaccount Dozent",
            "username" : "test_dozent",
            "prefix" : "",
            "family" : "Dozent"
         },
         "href" : "/api.php/user/205f3efb7997a0fc9755da2b535038da",
         "avatar_original" : "https://studip.example.org/pictures/user/nobody_original.png?d=0"
      },
      {
         "id" : "7e81ec247c151c02ffd479511e24cc03",
         "avatar_medium" : "https://studip.example.org/pictures/user/nobody_medium.png?d=1476444962",
         "avatar_normal" : "https://studip.example.org/pictures/user/nobody_normal.png?d=1476444962",
         "avatar_small" : "https://studip.example.org/pictures/user/nobody_small.png?d=1476444962",
         "avatar_original" : "https://studip.example.org/pictures/user/nobody_original.png?d=0",
         "href" : "/api.php/user/7e81ec247c151c02ffd479511e24cc03",
         "name" : {
            "formatted" : "Testaccount Tutor",
            "given" : "Testaccount",
            "suffix" : "",
            "prefix" : "",
            "username" : "test_tutor",
            "family" : "Tutor"
         }
      }
   ],
   "pagination" : {
      "offset" : 0,
      "total" : 4,
      "limit" : 20
   }
}

```


##### `GET` /user/:user_id/courses

Liefert eine Liste mit Veranstaltungen eines Nutzers. Durch den Parameter "semester" können die Veranstaltungen anhand des Semesters gefiltert werden. Dieser Parameter muss die Semester-ID enthalten. Eine Liste aller Semester samt zugehöriger IDs können über die Route /semesters abgefragt werden.

###### Parameter


| **Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| user_id | String, 32 Zeichen | Die Nutzer-ID des Nutzers
| semester | String, 32 Zeichen | Die ID eines Semesters

###### Antwortformat

```json

{
   "pagination" : {
      "total" : 4,
      "limit" : 20,
      "offset" : 0
   },
   "collection" : {
      "/api.php/course/984e34196f2e6ea6e1b2cc58f432fb8d" : {
         "course_id" : "984e34196f2e6ea6e1b2cc58f432fb8d",
         "type" : "1",
         "end_semester" : "/api.php/semester/eb828ebb81bb946fac4108521a3b4697",
         "lecturers" : {
            "/api.php/user/205f3efb7997a0fc9755da2b535038da" : {
               "name" : {
                  "given" : "Testaccount",
                  "formatted" : "Testaccount Dozent",
                  "suffix" : "",
                  "family" : "Dozent",
                  "username" : "test_dozent",
                  "prefix" : ""
               },
               "avatar_small" : "https://studip.example.org/pictures/user/nobody_small.png?d=1476444962",
               "avatar_medium" : "https://studip.example.org/pictures/user/nobody_medium.png?d=1476444962",
               "avatar_normal" : "https://studip.example.org/pictures/user/nobody_normal.png?d=1476444962",
               "href" : "/api.php/user/205f3efb7997a0fc9755da2b535038da",
               "avatar_original" : "https://studip.example.org/pictures/user/nobody_original.png?d=0",
               "id" : "205f3efb7997a0fc9755da2b535038da"
            }
         },
         "title" : "Am Lehrstuhl s&#65533;gen 1",
         "group" : 6,
         "subtitle" : "",
         "members" : {
            "user" : "/api.php/course/984e34196f2e6ea6e1b2cc58f432fb8d/members?status=user",
            "tutor" : "/api.php/course/984e34196f2e6ea6e1b2cc58f432fb8d/members?status=tutor",
            "dozent_count" : 1,
            "autor" : "/api.php/course/984e34196f2e6ea6e1b2cc58f432fb8d/members?status=autor",
            "tutor_count" : 0,
            "autor_count" : 0,
            "dozent" : "/api.php/course/984e34196f2e6ea6e1b2cc58f432fb8d/members?status=dozent",
            "user_count" : 0
         },
         "location" : "",
         "modules" : {
            "documents" : "/api.php/course/984e34196f2e6ea6e1b2cc58f432fb8d/files",
            "wiki" : "/api.php/course/984e34196f2e6ea6e1b2cc58f432fb8d/wiki",
            "forum" : "/api.php/course/984e34196f2e6ea6e1b2cc58f432fb8d/forum_categories"
         },
         "number" : "ALS1",
         "start_semester" : "/api.php/semester/eb828ebb81bb946fac4108521a3b4697",
         "description" : ""
      },
      "/api.php/course/a07535cf2f8a72df33c12ddfa4b53dde" : {
         "start_semester" : "/api.php/semester/f2b4fdf5ac59a9cb57dd73c4d3bbb651",
         "description" : "",
         "number" : "12345",
         "modules" : {
            "documents" : "/api.php/course/a07535cf2f8a72df33c12ddfa4b53dde/files",
            "wiki" : "/api.php/course/a07535cf2f8a72df33c12ddfa4b53dde/wiki",
            "forum" : "/api.php/course/a07535cf2f8a72df33c12ddfa4b53dde/forum_categories"
         },
         "location" : "",
         "members" : {
            "user_count" : 0,
            "dozent" : "/api.php/course/a07535cf2f8a72df33c12ddfa4b53dde/members?status=dozent",
            "tutor_count" : 1,
            "autor" : "/api.php/course/a07535cf2f8a72df33c12ddfa4b53dde/members?status=autor",
            "autor_count" : 1,
            "tutor" : "/api.php/course/a07535cf2f8a72df33c12ddfa4b53dde/members?status=tutor",
            "dozent_count" : 1,
            "user" : "/api.php/course/a07535cf2f8a72df33c12ddfa4b53dde/members?status=user"
         },
         "subtitle" : "eine normale Lehrveranstaltung",
         "group" : 5,
         "title" : "Test Lehrveranstaltung",
         "lecturers" : {
            "/api.php/user/205f3efb7997a0fc9755da2b535038da" : {
               "avatar_normal" : "https://studip.example.org/pictures/user/nobody_normal.png?d=1476444962",
               "id" : "205f3efb7997a0fc9755da2b535038da",
               "avatar_original" : "https://studip.example.org/pictures/user/nobody_original.png?d=0",
               "href" : "/api.php/user/205f3efb7997a0fc9755da2b535038da",
               "name" : {
                  "suffix" : "",
                  "username" : "test_dozent",
                  "family" : "Dozent",
                  "prefix" : "",
                  "given" : "Testaccount",
                  "formatted" : "Testaccount Dozent"
               },
               "avatar_small" : "https://studip.example.org/pictures/user/nobody_small.png?d=1476444962",
               "avatar_medium" : "https://studip.example.org/pictures/user/nobody_medium.png?d=1476444962"
            }
         },
         "end_semester" : "/api.php/semester/f2b4fdf5ac59a9cb57dd73c4d3bbb651",
         "type" : "1",
         "course_id" : "a07535cf2f8a72df33c12ddfa4b53dde"
      },
      "/api.php/course/29d755d51d2bf920ef2017db3359bdb2" : {
         "type" : "1",
         "course_id" : "29d755d51d2bf920ef2017db3359bdb2",
         "title" : "Fakultäten faktorisieren, fakultative Fakultätsvorlesung Fünf",
         "end_semester" : "/api.php/semester/eb828ebb81bb946fac4108521a3b4697",
         "lecturers" : {
            "/api.php/user/205f3efb7997a0fc9755da2b535038da" : {
               "avatar_small" : "https://studip.example.org/pictures/user/nobody_small.png?d=1476444962",
               "avatar_medium" : "https://studip.example.org/pictures/user/nobody_medium.png?d=1476444962",
               "name" : {
                  "given" : "Testaccount",
                  "formatted" : "Testaccount Dozent",
                  "suffix" : "",
                  "family" : "Dozent",
                  "username" : "test_dozent",
                  "prefix" : ""
               },
               "avatar_normal" : "https://studip.example.org/pictures/user/nobody_normal.png?d=1476444962",
               "avatar_original" : "https://studip.example.org/pictures/user/nobody_original.png?d=0",
               "id" : "205f3efb7997a0fc9755da2b535038da",
               "href" : "/api.php/user/205f3efb7997a0fc9755da2b535038da"
            }
         },
         "location" : "",
         "members" : {
            "user" : "/api.php/course/29d755d51d2bf920ef2017db3359bdb2/members?status=user",
            "dozent_count" : 1,
            "tutor" : "/api.php/course/29d755d51d2bf920ef2017db3359bdb2/members?status=tutor",
            "autor_count" : 0,
            "tutor_count" : 0,
            "autor" : "/api.php/course/29d755d51d2bf920ef2017db3359bdb2/members?status=autor",
            "dozent" : "/api.php/course/29d755d51d2bf920ef2017db3359bdb2/members?status=dozent",
            "user_count" : 0
         },
         "subtitle" : "",
         "group" : 6,
         "start_semester" : "/api.php/semester/eb828ebb81bb946fac4108521a3b4697",
         "description" : "",
         "number" : "4F5",
         "modules" : {
            "wiki" : "/api.php/course/29d755d51d2bf920ef2017db3359bdb2/wiki",
            "forum" : "/api.php/course/29d755d51d2bf920ef2017db3359bdb2/forum_categories",
            "documents" : "/api.php/course/29d755d51d2bf920ef2017db3359bdb2/files"
         }
      },
      "/api.php/course/9bbd57993e9cf6e1ed82ab5273af09ad" : {
         "lecturers" : {
            "/api.php/user/205f3efb7997a0fc9755da2b535038da" : {
               "avatar_original" : "https://studip.example.org/pictures/user/nobody_original.png?d=0",
               "id" : "205f3efb7997a0fc9755da2b535038da",
               "href" : "/api.php/user/205f3efb7997a0fc9755da2b535038da",
               "avatar_normal" : "https://studip.example.org/pictures/user/nobody_normal.png?d=1476444962",
               "avatar_small" : "https://studip.example.org/pictures/user/nobody_small.png?d=1476444962",
               "avatar_medium" : "https://studip.example.org/pictures/user/nobody_medium.png?d=1476444962",
               "name" : {
                  "suffix" : "",
                  "family" : "Dozent",
                  "username" : "test_dozent",
                  "prefix" : "",
                  "formatted" : "Testaccount Dozent",
                  "given" : "Testaccount"
               }
            }
         },
         "end_semester" : "/api.php/semester/eb828ebb81bb946fac4108521a3b4697",
         "title" : "Externe Einrichtungen extern einrichten, Einf&#65533;hrungsveranstaltung Eins",
         "course_id" : "9bbd57993e9cf6e1ed82ab5273af09ad",
         "type" : "1",
         "modules" : {
            "documents" : "/api.php/course/9bbd57993e9cf6e1ed82ab5273af09ad/files",
            "wiki" : "/api.php/course/9bbd57993e9cf6e1ed82ab5273af09ad/wiki",
            "forum" : "/api.php/course/9bbd57993e9cf6e1ed82ab5273af09ad/forum_categories"
         },
         "number" : "5E-1",
         "start_semester" : "/api.php/semester/eb828ebb81bb946fac4108521a3b4697",
         "description" : "",
         "group" : 6,
         "subtitle" : "",
         "members" : {
            "dozent" : "/api.php/course/9bbd57993e9cf6e1ed82ab5273af09ad/members?status=dozent",
            "user_count" : 0,
            "autor_count" : 0,
            "tutor_count" : 0,
            "autor" : "/api.php/course/9bbd57993e9cf6e1ed82ab5273af09ad/members?status=autor",
            "dozent_count" : 1,
            "tutor" : "/api.php/course/9bbd57993e9cf6e1ed82ab5273af09ad/members?status=tutor",
            "user" : "/api.php/course/9bbd57993e9cf6e1ed82ab5273af09ad/members?status=user"
         },
         "location" : ""
      }
   }
}

```


##### `GET` /user/:user_id/events

Liest die Liste der Kalendereinträge eines Nutzers der nächsten 2 Wochen aus.
Wird statt dieser Route die Route /user/:user_id/events.ics aufgerufen, erhält man die Kalendereinträge im iCal-Format.

###### Parameter


| **Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| user_id |String, 32 Zeichen | Die Nutzer-ID des Nutzers



##### `GET` /user/:user_id/institutes

Liefert eine Liste der Institute eines Nutzers zurück. Das zurückgelieferte "collection"-Objekt enthält hier nicht die Routen zu den einzelnen Objekten, sondern zwei Attribute mit Namen "work" und "study", welche wiederrum ein Array mit Institut-Objekten enthalten. Somit werden Institute sortiert nach ihrer Funktion für den gewählten Nutzer zurückgeliefert.

###### Parameter


| **Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| user_id |String, 32 Zeichen | Die Nutzer-ID des Nutzers

###### Antwortformat

```json

{
   "collection" : {
      "study" : [],
      "work" : [
         {
            "fax" : "",
            "street" : "Geismar Landstr. 17b",
            "room" : "",
            "faculty_street" : "Geismar Landstr. 17b",
            "faculty_city" : "37083 Göttingen",
            "city" : "37083 Göttingen",
            "consultation" : "",
            "faculty_name" : "Test Fakultät",
            "perms" : "dozent",
            "institute_id" : "1535795b0d6ddecac6813f5f6ac47ef2",
            "name" : "Test Fakultät",
            "phone" : ""
         },
         {
            "room" : "",
            "faculty_street" : "Geismar Landstr. 17b",
            "faculty_city" : "37083 Göttingen",
            "fax" : "",
            "street" : "",
            "phone" : "",
            "consultation" : "",
            "city" : "",
            "institute_id" : "2560f7c7674942a7dce8eeb238e15d93",
            "faculty_name" : "Test Fakultät",
            "perms" : "dozent",
            "name" : "Test Einrichtung"
         },
         {
            "name" : "Test Lehrstuhl",
            "faculty_name" : "Test Fakultät",
            "institute_id" : "536249daa596905f433e1f73578019db",
            "perms" : "dozent",
            "city" : "",
            "consultation" : "",
            "phone" : "",
            "street" : "",
            "fax" : "",
            "faculty_city" : "37083 Göttingen",
            "faculty_street" : "Geismar Landstr. 17b",
            "room" : ""
         },
         {
            "faculty_street" : "",
            "faculty_city" : "",
            "room" : "",
            "fax" : "",
            "street" : "",
            "phone" : "",
            "faculty_name" : "externe Bildungseinrichtungen",
            "institute_id" : "7a4f19a0a2c321ab2b8f7b798881af7c",
            "perms" : "dozent",
            "name" : "externe Einrichtung A",
            "consultation" : "",
            "city" : ""
         }
      ]
   },
   "pagination" : {
      "limit" : 20,
      "total" : 2,
      "offset" : 0
   }
}

```


##### `GET` /user/:user_id/news

Ankündigungen eines Nutzers auslesen.

###### Parameter


| **Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| user_id |String, 32 Zeichen | Die Nutzer-ID des Nutzers


###### Antwortformat

```json

{
   "collection" : {
      "/api.php/news/29f2932ce32be989022c6f43b866e744" : {
         "chdate_uid" : "",
         "news_id" : "29f2932ce32be989022c6f43b866e744",
         "chdate" : "1476445862",
         "mkdate" : "1476445862",
         "date" : "1476445862",
         "expire" : "14562502",
         "comments_count" : 1,
         "body_html" : "<div class=\"formatted-content\">Das Stud.IP-Team heisst sie herzlich willkommen. <br>Bitte schauen Sie sich ruhig um!<br><br>Wenn Sie das System selbst installiert haben und diese News sehen, haben Sie die Demonstrationsdaten in die Datenbank eingefügt. Wenn Sie produktiv mit dem System arbeiten wollen, sollten Sie diese Daten später wieder löschen, da die Passwörter der Accounts (vor allem des root-Accounts) öffentlich bekannt sind.</div>",
         "allow_comments" : "1",
         "topic" : "Herzlich Willkommen!",
         "user_id" : "76ed43ef286fb55cf9e41beadb484a9f",
         "comments" : "/api.php/news/29f2932ce32be989022c6f43b866e744/comments",
         "ranges" : [
            "/api.php/user/76ed43ef286fb55cf9e41beadb484a9f/news",
            "/api.php/studip/news"
         ],
         "body" : "Das Stud.IP-Team heisst sie herzlich willkommen. \r\nBitte schauen Sie sich ruhig um!\r\n\r\nWenn Sie das System selbst installiert haben und diese News sehen, haben Sie die Demonstrationsdaten in die Datenbank eingefügt. Wenn Sie produktiv mit dem System arbeiten wollen, sollten Sie diese Daten später wieder löschen, da die Passwörter der Accounts (vor allem des root-Accounts) öffentlich bekannt sind."
      }
   },
   "pagination" : {
      "offset" : 0,
      "total" : 1,
      "limit" : 20
   }
}

```


##### `POST` /user/:user_id/news

Erstellt eine neue Ankündigung des Nutzers. Diese Route verhält sich wie die `POST`-Route /studip/news.

###### Parameter


| **Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| user_id | String, 32 Zeichen | Die Nutzer-ID des Nutzers


| **`POST`-Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| topic | String | Der Titel der Ankündigung
| body | String | Der Inhalt der Ankündigung
| expire | Integer | Ablaufdatum der Nachricht (in Sekunden vom aktuellen Datum gerechnet)
| allow_comments | Integer | Gibt an, ob Kommentare erlaubt sind: 1 = erlaubt, 0 = nicht erlaubt


##### `GET` /user/:user_id/schedule und `GET` /user/:user_id/schedule/:semester_id

Liest den wöchentlichen Stundenplan des Nutzers vom aktuellen Semesters aus. Soll hingegen der Zeitplan eines bestimmten Semesters ausgelesen werden, so wird die Route /user/:user_id/schedule/:semester_id verwendet.

Diese Route liefert keine "collection"-Liste zurück, sondern ein JSON-Objekt, welches die Attribute 1-7 besitzt. Diese verweisen jeweils auf ein Array, das die Stundenplan-Einträge beinhaltet.

###### Parameter


| **Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| user_id | String, 32 Zeichen | Die Nutzer-ID des Nutzers


#### Course - Daten zu einer Veranstaltung

##### `GET` /course/:course_id

Es wird ein Veranstaltungs-Objekt zurückgeliefert, welches alle Grunddaten einer Veranstaltung beinhaltet. Vergleichbar mit dem Aufruf von Course::find() im Stud.IP System.

###### Parameter


| **Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| course_id | String, 32 Zeichen | Die ID der Veranstaltung

###### Antwortformat

```json

{
   "start_semester" : "/api.php/semester/f2b4fdf5ac59a9cb57dd73c4d3bbb651",
   "number" : "12345",
   "lecturers" : {
      "/api.php/user/205f3efb7997a0fc9755da2b535038da" : {
         "avatar_medium" : "https://studip.example.org/pictures/user/nobody_medium.png?d=1476444962",
         "avatar_small" : "https://studip.example.org/pictures/user/nobody_small.png?d=1476444962",
         "name" : {
            "formatted" : "Testaccount Dozent",
            "username" : "test_dozent",
            "given" : "Testaccount",
            "family" : "Dozent",
            "prefix" : "",
            "suffix" : ""
         },
         "avatar_original" : "https://studip.example.org/pictures/user/nobody_original.png?d=0",
         "id" : "205f3efb7997a0fc9755da2b535038da",
         "avatar_normal" : "https://studip.example.org/pictures/user/nobody_normal.png?d=1476444962",
         "href" : "/api.php/user/205f3efb7997a0fc9755da2b535038da"
      }
   },
   "members" : {
      "tutor" : "/api.php/course/a07535cf2f8a72df33c12ddfa4b53dde/members?status=tutor",
      "dozent_count" : 1,
      "tutor_count" : 1,
      "dozent" : "/api.php/course/a07535cf2f8a72df33c12ddfa4b53dde/members?status=dozent",
      "user" : "/api.php/course/a07535cf2f8a72df33c12ddfa4b53dde/members?status=user",
      "autor_count" : 1,
      "user_count" : 0,
      "autor" : "/api.php/course/a07535cf2f8a72df33c12ddfa4b53dde/members?status=autor"
   },
   "modules" : {
      "forum" : "/api.php/course/a07535cf2f8a72df33c12ddfa4b53dde/forum_categories",
      "documents" : "/api.php/course/a07535cf2f8a72df33c12ddfa4b53dde/files",
      "wiki" : "/api.php/course/a07535cf2f8a72df33c12ddfa4b53dde/wiki"
   },
   "type" : "1",
   "location" : "",
   "title" : "Test Lehrveranstaltung",
   "end_semester" : "/api.php/semester/f2b4fdf5ac59a9cb57dd73c4d3bbb651",
   "description" : "",
   "subtitle" : "eine normale Lehrveranstaltung",
   "course_id" : "a07535cf2f8a72df33c12ddfa4b53dde"
}

```


##### `GET` /course/:course_id/blubber

Es wird eine Liste mit Blubber-Objekten zurückgeliefert.


| **Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| course_id | String, 32 Zeichen | Die ID der Veranstaltung

###### Antwortformat

```json
{
   "pagination" : {
      "limit" : 20,
      "offset" : 0,
      "total" : 1
   },
   "collection" : {
      "/api.php/blubber/posting/6b7ff409eeaa73cb8927ef27ac623f6d" : {
         "comments_count" : "1",
         "reshares" : [],
         "author" : {
            "avatar_normal" : "https://studip.example.org/pictures/user/nobody_normal.png?d=1476444962",
            "id" : "205f3efb7997a0fc9755da2b535038da",
            "href" : "/api.php/user/205f3efb7997a0fc9755da2b535038da",
            "name" : {
               "family" : "Dozent",
               "given" : "Testaccount",
               "formatted" : "Testaccount Dozent",
               "username" : "test_dozent",
               "prefix" : "",
               "suffix" : ""
            },
            "avatar_original" : "https://studip.example.org/pictures/user/nobody_original.png?d=0",
            "avatar_small" : "https://studip.example.org/pictures/user/nobody_small.png?d=1476444962",
            "avatar_medium" : "https://studip.example.org/pictures/user/nobody_medium.png?d=1476444962"
         },
         "blubber_id" : "6b7ff409eeaa73cb8927ef27ac623f6d",
         "tags" : [],
         "chdate" : "1478853090",
         "root_id" : "6b7ff409eeaa73cb8927ef27ac623f6d",
         "mkdate" : "1478853090",
         "comments" : "/api.php/blubber/posting/6b7ff409eeaa73cb8927ef27ac623f6d/comments",
         "content_html" : "<div class=\"formatted-content\">In dieser Veranstaltung gibt es einen Test-Blubber</div>",
         "context_type" : "course",
         "course_id" : "a07535cf2f8a72df33c12ddfa4b53dde",
         "content" : "In dieser Veranstaltung gibt es einen Test-Blubber"
      }
   }
}

```


##### `POST` /course/:course_id/blubber

Der Veranstaltung wird ein neuer Blubber hinzugefügt. Diese Route verhält sich genauso wie die Route /user/:user_id/blubber.


| **Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| course_id | String, 32 Zeichen | Die ID der Veranstaltung


| **`POST`-Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| content | String | Der Inhalt des Blubbers (`POST`-Parameter)


##### `GET` /course/:course_id/events

Liefert eine Liste mit Kalendereinträge einer Veranstaltung zurück.


| **Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| course_id | String, 32 Zeichen | Die ID der Veranstaltung

###### Antwortformat

```json

{
   "collection" : [
      {
         "event_id" : "b99e85888891b41e1e814b7934a6791a",
         "start" : "1460358000",
         "title" : "Mo , 11.04.2016 09:00 - 12:00",
         "description" : "",
         "categories" : "Sitzung",
         "end" : "1460368800",
         "room" : "Raum: Hörsaal 1",
         "canceled" : false,
         "deleted" : null
      },
      {
         "deleted" : null,
         "canceled" : false,
         "categories" : "Sitzung",
         "end" : "1461236400",
         "room" : "Raum: Seminarraum 1",
         "description" : "",
         "title" : "Do , 21.04.2016 09:00 - 13:00",
         "event_id" : "86abb6bfe25cc2ac254b91474c95549f",
         "start" : "1461222000"
      },
      {
         "deleted" : null,
         "canceled" : false,
         "room" : "Raum: Hörsaal 1",
         "end" : "1461578400",
         "categories" : "Sitzung",
         "description" : "",
         "title" : "Mo , 25.04.2016 09:00 - 12:00",
         "start" : "1461567600",
         "event_id" : "61eb9961107feca471b5db61bce1a76c"
      },
      {
         "description" : "",
         "title" : "Do , 05.05.2016 09:00 - 13:00",
         "event_id" : "d08073764f751b3c54db4337a7113e77",
         "start" : "1462431600",
         "deleted" : true,
         "canceled" : "Christi Himmelfahrt",
         "categories" : "Sitzung",
         "room" : "(Christi Himmelfahrt)",
         "end" : "1462446000"
      },
      {
         "description" : "",
         "title" : "Mo , 09.05.2016 09:00 - 12:00",
         "event_id" : "6061c46538054821deb1031d70997ee9",
         "start" : "1462777200",
         "deleted" : null,
         "canceled" : false,
         "categories" : "Sitzung",
         "end" : "1462788000",
         "room" : "Raum: Hörsaal 1"
      },
      {
         "title" : "Do , 19.05.2016 09:00 - 13:00",
         "event_id" : "b6b3767d913faad2bbb44135a6321295",
         "start" : "1463641200",
         "description" : "",
         "canceled" : false,
         "categories" : "Sitzung",
         "room" : "Raum: Seminarraum 1",
         "end" : "1463655600",
         "deleted" : null
      },
      {
         "event_id" : "d0d018a4aed955ecf85e01e07d81147a",
         "start" : "1463986800",
         "title" : "Mo , 23.05.2016 09:00 - 12:00",
         "description" : "",
         "categories" : "Sitzung",
         "room" : "Raum: Hörsaal 1",
         "end" : "1463997600",
         "canceled" : false,
         "deleted" : null
      },
      {
         "deleted" : null,
         "canceled" : false,
         "categories" : "Sitzung",
         "room" : "Raum: Seminarraum 1",
         "end" : "1464865200",
         "description" : "",
         "title" : "Do , 02.06.2016 09:00 - 13:00",
         "event_id" : "12a11ff3f258ab706e77f85d7f1f28f9",
         "start" : "1464850800"
      },
      {
         "deleted" : null,
         "canceled" : false,
         "categories" : "Sitzung",
         "end" : "1465207200",
         "room" : "Raum: Hörsaal 1",
         "description" : "",
         "title" : "Mo , 06.06.2016 09:00 - 12:00",
         "event_id" : "124609f704bff3379705bb8a8fce3ae1",
         "start" : "1465196400"
      },
      {
         "categories" : "Sitzung",
         "room" : "Raum: Seminarraum 1",
         "end" : "1466074800",
         "canceled" : false,
         "deleted" : null,
         "event_id" : "4be63f71cf1d08b87b7d731c3a28a572",
         "start" : "1466060400",
         "title" : "Do , 16.06.2016 09:00 - 13:00",
         "description" : ""
      },
      {
         "description" : "",
         "event_id" : "98a296243626ea32243fb3507af38bce",
         "start" : "1466406000",
         "title" : "Mo , 20.06.2016 09:00 - 12:00",
         "deleted" : null,
         "categories" : "Sitzung",
         "room" : "Raum: Hörsaal 1",
         "end" : "1466416800",
         "canceled" : false
      },
      {
         "title" : "Do , 30.06.2016 09:00 - 13:00",
         "start" : "1467270000",
         "event_id" : "cedcf924d509a3c1acc8fcde47ab198b",
         "description" : "",
         "canceled" : false,
         "end" : "1467284400",
         "room" : "Raum: Seminarraum 1",
         "categories" : "Sitzung",
         "deleted" : null
      },
      {
         "deleted" : null,
         "canceled" : false,
         "categories" : "Sitzung",
         "room" : "Raum: Hörsaal 1",
         "end" : "1467626400",
         "description" : "",
         "title" : "Mo , 04.07.2016 09:00 - 12:00",
         "event_id" : "341e8520fe69a90fb437cb9e0ba3368c",
         "start" : "1467615600"
      },
      {
         "title" : "Do , 14.07.2016 09:00 - 13:00",
         "event_id" : "bab7532584170e5aaf44eb217b1d19e0",
         "start" : "1468479600",
         "description" : "",
         "canceled" : false,
         "categories" : "Sitzung",
         "room" : "keine Raumangabe",
         "end" : "1468494000",
         "deleted" : null
      }
   ],
   "pagination" : {
      "limit" : 20,
      "offset" : 0,
      "total" : 14
   }
}

```


##### `GET` /course/:course_id/files

Liefert eine Liste mit Dateien und Ordnern einer Veranstaltung zurück.


| **Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| course_id | String, 32 Zeichen | Die ID der Veranstaltung

###### Antwortformat

```json

{
   "collection" : {
      "/api.php/file/ad8dc6a6162fb0fe022af4a62a15e309" : {
         "course" : "/api.php/course/a07535cf2f8a72df33c12ddfa4b53dde",
         "permissions" : [
            "visible",
            "writable",
            "readable"
         ],
         "chdate" : 1343924877,
         "mkdate" : 1343924873,
         "author" : "/api.php/user/76ed43ef286fb55cf9e41beadb484a9f",
         "name" : "Hausaufgaben",
         "folder_id" : "ad8dc6a6162fb0fe022af4a62a15e309",
         "range_id" : "373a72966cf45c484b4b0b07dba69a64",
         "documents" : [],
         "description" : ""
      },
      "/api.php/file/ca002fbae136b07e4df29e0136e3bd32" : {
         "chdate" : 1343924894,
         "mkdate" : 1343924407,
         "course" : "/api.php/course/a07535cf2f8a72df33c12ddfa4b53dde",
         "permissions" : [
            "visible",
            "readable",
            "extendable"
         ],
         "name" : "Allgemeiner Dateiordner",
         "author" : "/api.php/user/76ed43ef286fb55cf9e41beadb484a9f",
         "folder_id" : "ca002fbae136b07e4df29e0136e3bd32",
         "description" : "Ablage für allgemeine Ordner und Dokumente der Veranstaltung",
         "documents" : {
            "/api.php/file/6b606bd3d6d6cda829200385fa79fcbf" : {
               "filesize" : 314146,
               "course" : "/api.php/course/a07535cf2f8a72df33c12ddfa4b53dde",
               "filename" : "mappe_studip-el.pdf",
               "chdate" : 1343924841,
               "mkdate" : 1343924827,
               "file_id" : "6b606bd3d6d6cda829200385fa79fcbf",
               "author" : "/api.php/user/76ed43ef286fb55cf9e41beadb484a9f",
               "name" : "Stud.IP-Produktbrosch?re im PDF-Format",
               "downloads" : 0,
               "protected" : false,
               "range_id" : "ca002fbae136b07e4df29e0136e3bd32",
               "content" : "/api.php/file/6b606bd3d6d6cda829200385fa79fcbf/content",
               "description" : ""
            }
         },
         "range_id" : "a07535cf2f8a72df33c12ddfa4b53dde"
      },
      "/api.php/file/df122112a21812ff4ffcf1965cb48fc3" : {
         "range_id" : "2f597139a049a768dbf8345a0a0af3de",
         "documents" : [],
         "description" : "Ablage für Ordner und Dokumente dieser Gruppe",
         "folder_id" : "df122112a21812ff4ffcf1965cb48fc3",
         "name" : "Dateiordner der Gruppe: Studierende",
         "author" : "/api.php/user/76ed43ef286fb55cf9e41beadb484a9f",
         "chdate" : 1343924860,
         "mkdate" : 1343924860,
         "permissions" : [
            "visible",
            "writable",
            "readable",
            "extendable"
         ],
         "course" : "/api.php/course/a07535cf2f8a72df33c12ddfa4b53dde"
      }
   },
   "pagination" : {
      "limit" : 20,
      "total" : 3,
      "offset" : 0
   }
}

```


##### `GET` /course/:course_id/forum_categories

Liefert die Liste mit Forenbereichen einer Veranstaltung zurück. Um an die einzelnen Forenbeiträge zu gelangen, muss die Route /forum_category/:category_id/areas aufgerufen werden.


| **Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| course_id | String, 32 Zeichen | Die ID der Veranstaltung

###### Antwortformat

```json

{
   "pagination" : {
      "offset" : 0,
      "limit" : 20,
      "total" : 1
   },
   "collection" : {
      "/api.php/forum_category/a07535cf2f8a72df33c12ddfa4b53dde" : {
         "entry_name" : "Allgemein",
         "pos" : "0",
         "areas_count" : 1,
         "seminar_id" : "a07535cf2f8a72df33c12ddfa4b53dde",
         "areas" : "/api.php/forum_category/a07535cf2f8a72df33c12ddfa4b53dde/areas",
         "category_id" : "a07535cf2f8a72df33c12ddfa4b53dde",
         "course" : "/api.php/course/a07535cf2f8a72df33c12ddfa4b53dde"
      }
   }
}

```


##### `POST` /course/:course_id/forum_categories

Es wird ein neuer Forenbereich erstellt. Die `POST`-Anfrage benötigt den Parameter "name", welcher den Namen der neuen Kategorie angibt.


| **Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| course_id | String, 32 Zeichen | Die ID der Veranstaltung


| **`POST`-Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| name | String | Der Name der neuen Foren-Kategorie (`POST`-Parameter)


##### `GET` /course/:course_id/members

Liefert die Teilnehmer einer Veranstaltung zurück. Über den Parameter "status" können die Teilnehmer anhand ihres Status gefiltert werden. Der Parameter "status" darf nur aus einem der folgenden Wörter bestehen: "user", "autor", "tutor", "dozent".


| **Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| course_id | String, 32 Zeichen | Die ID der Veranstaltung

###### Antwortformat

```json

{
   "pagination" : {
      "offset" : 0,
      "limit" : 20,
      "total" : 3
   },
   "collection" : {
      "/api.php/user/205f3efb7997a0fc9755da2b535038da" : {
         "member" : {
            "href" : "/api.php/user/205f3efb7997a0fc9755da2b535038da",
            "avatar_normal" : "https://studip.example.org/pictures/user/nobody_normal.png?d=1476444962",
            "avatar_medium" : "https://studip.example.org/pictures/user/nobody_medium.png?d=1476444962",
            "avatar_small" : "https://studip.example.org/pictures/user/nobody_small.png?d=1476444962",
            "id" : "205f3efb7997a0fc9755da2b535038da",
            "name" : {
               "given" : "Testaccount",
               "family" : "Dozent",
               "suffix" : "",
               "username" : "test_dozent",
               "prefix" : "",
               "formatted" : "Testaccount Dozent"
            },
            "avatar_original" : "https://studip.example.org/pictures/user/nobody_original.png?d=0"
         },
         "status" : "dozent"
      },
      "/api.php/user/e7a0a84b161f3e8c09b4a0a2e8a58147" : {
         "status" : "autor",
         "member" : {
            "href" : "/api.php/user/e7a0a84b161f3e8c09b4a0a2e8a58147",
            "avatar_normal" : "https://studip.example.org/pictures/user/nobody_normal.png?d=1476444962",
            "avatar_medium" : "https://studip.example.org/pictures/user/nobody_medium.png?d=1476444962",
            "id" : "e7a0a84b161f3e8c09b4a0a2e8a58147",
            "avatar_small" : "https://studip.example.org/pictures/user/nobody_small.png?d=1476444962",
            "name" : {
               "given" : "Testaccount",
               "suffix" : "",
               "family" : "Autor",
               "username" : "test_autor",
               "prefix" : "",
               "formatted" : "Testaccount Autor"
            },
            "avatar_original" : "https://studip.example.org/pictures/user/nobody_original.png?d=0"
         }
      },
      "/api.php/user/7e81ec247c151c02ffd479511e24cc03" : {
         "member" : {
            "name" : {
               "given" : "Testaccount",
               "username" : "test_tutor",
               "suffix" : "",
               "family" : "Tutor",
               "prefix" : "",
               "formatted" : "Testaccount Tutor"
            },
            "avatar_original" : "https://studip.example.org/pictures/user/nobody_original.png?d=0",
            "id" : "7e81ec247c151c02ffd479511e24cc03",
            "avatar_small" : "https://studip.example.org/pictures/user/nobody_small.png?d=1476444962",
            "avatar_normal" : "https://studip.example.org/pictures/user/nobody_normal.png?d=1476444962",
            "href" : "/api.php/user/7e81ec247c151c02ffd479511e24cc03",
            "avatar_medium" : "https://studip.example.org/pictures/user/nobody_medium.png?d=1476444962"
         },
         "status" : "tutor"
      }
   }
}

```


##### `GET` /course/:course_id/news

Liefert die Ankündigungen einer Veranstaltung zurück.


| **Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| course_id | String, 32 Zeichen | Die ID der Veranstaltung

###### Antwortformat

```json

{
   "collection" : {
      "/api.php/news/d8d92fb84dbb1150f09199f923c54aa8" : {
         "expire" : "691140",
         "chdate_uid" : "",
         "topic" : "Ausfall der Veranstaltung",
         "ranges" : [
            "/api.php/course/a07535cf2f8a72df33c12ddfa4b53dde/news"
         ],
         "user_id" : "205f3efb7997a0fc9755da2b535038da",
         "date" : "1478818800",
         "chdate" : "1478853886",
         "mkdate" : "1478853886",
         "news_id" : "d8d92fb84dbb1150f09199f923c54aa8",
         "body" : "Die Veranstaltung f&#65533;llt am 11.11. wegen Beginn der \"5. Jahreszeit\" aus!",
         "body_html" : "<div class=\"formatted-content\">Die Veranstaltung f&#65533;llt am 11.11. wegen Beginn der &quot;5. Jahreszeit&quot; aus!</div>",
         "allow_comments" : "0"
      }
   },
   "pagination" : {
      "limit" : 20,
      "offset" : 0,
      "total" : 1
   }
}

```


##### `POST` /course/:course_id/news

Legt eine neue Ankündigung in der Veranstaltung an. Diese Route verhält sich wie die `POST`-Route /studip/news.


| **Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| course_id | String, 32 Zeichen | Die ID der Veranstaltung


| **`POST`-Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| topic | String | Der Titel der Ankündigung
| body | String | Der Inhalt der Ankündigung
| expire | Integer | Ablaufdatum der Nachricht (in Sekunden vom aktuellen Datum gerechnet)
| allow_comments | Integer | Gibt an, ob Kommentare erlaubt sind: 1 = erlaubt, 0 = nicht erlaubt


##### `GET` /course/:course_id/wiki

Liefert die Liste mit allen Seiten des Wikis einer Veranstaltung zurück, wobei von jeder Seite die aktuellste Version zurückgeliefert wird. Sind keine Seiten vorhanden, besteht die Liste mit Seiten nur aus der Hauptseite.


| **Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| course_id | String, 32 Zeichen | Die ID der Veranstaltung

###### Antwortformat

```json

{
   "pagination" : {
      "limit" : 20,
      "total" : 3,
      "offset" : 0
   },
   "collection" : {
      "/api.php/course/a07535cf2f8a72df33c12ddfa4b53dde/wiki/Weitere Seite" : {
         "version" : "1",
         "keyword" : "Weitere Seite",
         "range_id" : "a07535cf2f8a72df33c12ddfa4b53dde",
         "chdate" : "1478854048",
         "user" : {
            "avatar_normal" : "https://studip.example.org/pictures/user/nobody_normal.png?d=1476444962",
            "avatar_medium" : "https://studip.example.org/pictures/user/nobody_medium.png?d=1476444962",
            "avatar_original" : "https://studip.example.org/pictures/user/nobody_original.png?d=0",
            "avatar_small" : "https://studip.example.org/pictures/user/nobody_small.png?d=1476444962",
            "name" : {
               "family" : "Dozent",
               "prefix" : "",
               "formatted" : "Testaccount Dozent",
               "username" : "test_dozent",
               "suffix" : "",
               "given" : "Testaccount"
            },
            "id" : "205f3efb7997a0fc9755da2b535038da",
            "href" : "/api.php/user/205f3efb7997a0fc9755da2b535038da"
         }
      },
      "/api.php/course/a07535cf2f8a72df33c12ddfa4b53dde/wiki/WikiWikiWeb" : {
         "keyword" : "WikiWikiWeb",
         "version" : "1",
         "user" : {
            "avatar_original" : "https://studip.example.org/pictures/user/nobody_original.png?d=0",
            "avatar_small" : "https://studip.example.org/pictures/user/nobody_small.png?d=1476444962",
            "avatar_medium" : "https://studip.example.org/pictures/user/nobody_medium.png?d=1476444962",
            "avatar_normal" : "https://studip.example.org/pictures/user/nobody_normal.png?d=1476444962",
            "id" : "205f3efb7997a0fc9755da2b535038da",
            "href" : "/api.php/user/205f3efb7997a0fc9755da2b535038da",
            "name" : {
               "family" : "Dozent",
               "username" : "test_dozent",
               "formatted" : "Testaccount Dozent",
               "prefix" : "",
               "suffix" : "",
               "given" : "Testaccount"
            }
         },
         "chdate" : "1478854031",
         "range_id" : "a07535cf2f8a72df33c12ddfa4b53dde"
      },
      "/api.php/course/a07535cf2f8a72df33c12ddfa4b53dde/wiki/WikiWikWeb" : {
         "keyword" : "WikiWikWeb",
         "version" : 0,
         "chdate" : null,
         "range_id" : "a07535cf2f8a72df33c12ddfa4b53dde"
      }
   }
}

```


##### `GET` /course/:course_id/wiki/:seitenname

Liefert eine Wikiseite mit dem angegebenen Seitennamen zurück.


| **Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| course_id | String, 32 Zeichen | Die ID der Veranstaltung
| seitenname | String | Der Name der Wiki-Seite

###### Antwortformat

```json

{
   "content_html" : "<div class=\"formatted-content\">Dies ist eine weitere Wiki-Seite.</div>",
   "chdate" : "1478854048",
   "version" : "1",
   "keyword" : "Weitere Seite",
   "user" : {
      "avatar_small" : "https://studip.example.org/pictures/user/nobody_small.png?d=1476444962",
      "avatar_original" : "https://studip.example.org/pictures/user/nobody_original.png?d=0",
      "avatar_normal" : "https://studip.example.org/pictures/user/nobody_normal.png?d=1476444962",
      "avatar_medium" : "https://studip.example.org/pictures/user/nobody_medium.png?d=1476444962",
      "id" : "205f3efb7997a0fc9755da2b535038da",
      "name" : {
         "family" : "Dozent",
         "given" : "Testaccount",
         "prefix" : "",
         "suffix" : "",
         "formatted" : "Testaccount Dozent",
         "username" : "test_dozent"
      },
      "href" : "/api.php/user/205f3efb7997a0fc9755da2b535038da"
   },
   "content" : "Dies ist eine weitere Wiki-Seite.",
   "range_id" : "a07535cf2f8a72df33c12ddfa4b53dde"
}

```



##### `PUT` /course/:course_id/wiki/:seitenname

Aktualisiert eine bestehende Wikiseite oder legt diese neu an. Der Parameter "content" muss gesetzt sein und den Inhalt der Wiki-Seite beinhalten.


| **Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| course_id | String, 32 Zeichen | Die ID der Veranstaltung
| seitenname | String | Der Name der Wiki-Seite

| **`PUT`-Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| content | String | Der neue Inhalt der Wiki-Seite


##### `GET` /course/:course_id/wiki/:seitenname/:version

Liefert eine bestimmte Version einer Wikiseite zurück.


| **Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| course_id | String, 32 Zeichen | Die ID der Veranstaltung
| seitenname | String | Der Name der Wiki-Seite

###### Antwortformat

```json

{
   "range_id" : "a07535cf2f8a72df33c12ddfa4b53dde",
   "content" : "Dies ist eine weitere Wiki-Seite, die mehrmals ge&#65533;ndert wurde!",
   "version" : "1",
   "content_html" : "<div class=\"formatted-content\">Dies ist eine weitere Wiki-Seite, die mehrmals ge&#65533;ndert wurde!</div>",
   "chdate" : "1478854411",
   "keyword" : "Weitere Seite",
   "user" : {
      "avatar_medium" : "https://studip.example.org/pictures/user/nobody_medium.png?d=1476444962",
      "avatar_original" : "https://studip.example.org/pictures/user/nobody_original.png?d=0",
      "name" : {
         "prefix" : "",
         "suffix" : "",
         "family" : "Dozent",
         "username" : "test_dozent",
         "given" : "Testaccount",
         "formatted" : "Testaccount Dozent"
      },
      "id" : "205f3efb7997a0fc9755da2b535038da",
      "href" : "/api.php/user/205f3efb7997a0fc9755da2b535038da",
      "avatar_normal" : "https://studip.example.org/pictures/user/nobody_normal.png?d=1476444962",
      "avatar_small" : "https://studip.example.org/pictures/user/nobody_small.png?d=1476444962"
   }
}

```


#### Blubber

Neben den Methoden zum Holen aller Blubber eines Nutzers bzw. einer Veranstaltung gibt es die folgenden REST-Routen. Da Kommentare eines Blubbers ebenfalls wiederrum Blubber-Objekte sind, können alle Routen, die für Blubber-Objekte gedacht sind, auch für Blubber-Kommentare genutzt werden.


##### `POST` /blubber/postings

Erstellt einen neuen Blubber. Es müssen Parameter angegeben werden, damit ein neuer Blubber erstellt werden kann. Über den Parameter "content" wird der Inhalt des Blubbers gesetzt. Mittels "context_type" wird angegeben, ob der Blubber öffentlich (public), privat (private) oder zu einer Veranstaltung (course) gehört. 

Im Falle, dass der Blubber zu einer Veranstaltung gehört, muss der Parameter course_id gesetzt sein und dieser muss eine Veranstaltungs-ID enthalten.

Falls der Blubber privat ist, muss über den Parameter "private_adresses" eine Liste mit Nutzer-IDs der Nutzer übergeben werden, welche den Blubber sehen dürfen. Hierbei ist zu beachten, das Nutzer, welche im Text des Blubbers mit einem @-Verweis referenziert wurden, den Blubber automatisch sehen können.


| **`POST`-Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| content | String | Der Inhalt des Blubbers
| context_type | String | Der Kontext, in dem ein Blubber erstellt wird: "course", "private", "public"
| course_id | String, 32 Zeichen | Die Veranstaltungs-ID (nur erforderlich, wenn context_type = course gesetzt ist)
| private_adresses | Array | Eine Liste mit Nutzer-IDs der Nutzer, die diesen Blubber sehen dürfen (nur erforderlich, wenn context_type = private gesetzt ist)


##### `GET` /blubber/stream/:stream_id

Liefert eine Liste der Blubber in einem bestimmten Blubber-Stream.


| **Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| stream_id | String, 32 Zeichen | Die ID des Blubber-Streams


##### `GET` /blubber/posting/:blubber_id/comments

Liefert eine Liste der Kommentare ("Antwort-Blubber") zu einem Blubber.


| **Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| blubber_id | String, 32 Zeichen | Die ID des Blubbers

###### Antwortformat

```json

{
   "collection" : {
      "/api.php/blubber/comment/8340654c6fce2441e967ae3a9bf350eb" : {
         "root_id" : "ecab929ef0dfaeca159802c018826a25",
         "mkdate" : "1478854991",
         "content" : "Test2",
         "content_html" : "<div class=\"formatted-content\">Test2</div>",
         "chdate" : "1478854991",
         "author" : {
            "avatar_original" : "https://studip.example.org/pictures/user/nobody_original.png?d=0",
            "avatar_normal" : "https://studip.example.org/pictures/user/nobody_normal.png?d=1476444962",
            "id" : "76ed43ef286fb55cf9e41beadb484a9f",
            "name" : {
               "given" : "Root",
               "suffix" : "",
               "prefix" : "",
               "formatted" : "Root Studip",
               "username" : "root@studip",
               "family" : "Studip"
            },
            "avatar_medium" : "https://studip.example.org/pictures/user/nobody_medium.png?d=1476444962",
            "href" : "/api.php/user/76ed43ef286fb55cf9e41beadb484a9f",
            "avatar_small" : "https://studip.example.org/pictures/user/nobody_small.png?d=1476444962"
         },
         "blubber_id" : "8340654c6fce2441e967ae3a9bf350eb",
         "context_type" : "public"
      }
   },
   "pagination" : {
      "total" : 1,
      "limit" : 20,
      "offset" : 0
   }
}

```


##### `POST` /blubber/posting/:blubber_id/comments

Erstellt einen neuen Kommentar zu einem Blubber. Mittels des Parameters "content" wird der Inhalt des Kommentars gesetzt.


| **Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| blubber_id | String, 32 Zeichen | Die ID des Blubbers


| **`POST`-Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| content | String | Der Inhalt des Blubbers


##### `GET` /blubber/posting/:blubber_id und `GET` /blubber/comment/:blubber_id

Liefert Daten zu einem Blubber oder einem Kommentar zurück. Das zurückgegebene JSON-Objekt ist in beiden Fällen gleich aufgebaut.


| **Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| blubber_id | String, 32 Zeichen | Die ID des Blubbers

###### Antwortformat

```json

{
   "mkdate" : "1478854991",
   "chdate" : "1478854991",
   "blubber_id" : "8340654c6fce2441e967ae3a9bf350eb",
   "content_html" : "<div class=\"formatted-content\">Test2</div>",
   "content" : "Test2",
   "author" : {
      "href" : "/api.php/user/76ed43ef286fb55cf9e41beadb484a9f",
      "avatar_original" : "https://studip.example.org/pictures/user/nobody_original.png?d=0",
      "avatar_normal" : "https://studip.example.org/pictures/user/nobody_normal.png?d=1476444962",
      "id" : "76ed43ef286fb55cf9e41beadb484a9f",
      "avatar_small" : "https://studip.example.org/pictures/user/nobody_small.png?d=1476444962",
      "name" : {
         "username" : "root@studip",
         "suffix" : "",
         "formatted" : "Root Studip",
         "family" : "Studip",
         "given" : "Root",
         "prefix" : ""
      },
      "avatar_medium" : "https://studip.example.org/pictures/user/nobody_medium.png?d=1476444962"
   },
   "context_type" : "public",
   "root_id" : "ecab929ef0dfaeca159802c018826a25"
}

```


##### `PUT` /blubber/posting/:blubber_id und `PUT` /blubber/comment/:blubber_id

Editiert einen Blubber. Über den mitgegebenen Parameter "content" kann der Inhalt des Blubbers geändert werden.


| **Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| blubber_id | String, 32 Zeichen | Die ID des Blubbers


| **`PUT`-Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| content | String | Der neue Inhalt des Blubbers oder des Blubber-Kommentars


##### `DELETE` /blubber/posting/:blubber_id und `DELETE` /blubber/comment/:blubber_id

Löscht einen Blubber.


| **Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| blubber_id | String, 32 Zeichen | Die ID des Blubbers


#### File - Dateien und Ordner

##### `GET` /file/:file_id

Liefert die Metadaten einer Datei bzw. eines Ordners zurück.


| **Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| file_id | String, 32 Zeichen | Die ID der Datei bzw. des Ordners


###### Antwortformat

```json

{
   "file_id" : "6b606bd3d6d6cda829200385fa79fcbf",
   "chdate" : 1343924841,
   "description" : "",
   "range_id" : "ca002fbae136b07e4df29e0136e3bd32",
   "protected" : false,
   "content" : "/api.php/file/6b606bd3d6d6cda829200385fa79fcbf/content",
   "mkdate" : 1343924827,
   "downloads" : 0,
   "course" : "/api.php/course/a07535cf2f8a72df33c12ddfa4b53dde",
   "author" : "/api.php/user/76ed43ef286fb55cf9e41beadb484a9f",
   "filesize" : 314146,
   "filename" : "mappe_studip-el.pdf",
   "name" : "Stud.IP-Produktbroschüre im PDF-Format"
}

```


##### `GET` /file/:file_id/content

Liefert den Inhalt einer Datei als Binärdaten zurück.


| **Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| file_id | String, 32 Zeichen | Die ID der Datei

###### Antwortformat

Die angeforderte Datei.


##### `DELETE` /file/:file_id

Löscht die Datei bzw. den Ordner.


| **Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| file_id | String, 32 Zeichen | Die ID der Datei bzw. des Ordners


##### `PUT` /file/:file_id

Aktualisiert die Datei bzw. den Ordner. Es können hierbei entweder nur die Metadaten über die Parameter "name" oder "description" (siehe unten) geändert werden oder aber bei Dateien eine neue Version der Datei angehängt werden. Hierbei ist zu beachten, dass die Anfrage als Multipart-Request ausgeführt werden muss.


| **Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| file_id | String, 32 Zeichen | Die ID der Datei bzw. des Ordners


##### `POST` /file/:folder_id

Erstellt eine Datei bzw. einen Ordner.

Der Parameter "name" ist für einen neuen Ordner verpflichtend und gibt dessen Namen an. Bei Dateien kann dieser Parameter verwendet werden, um den Dateinamen zu überschreiben. Eine optionale Beschreibung kann über den Parameter "description" mitgeliefert werden.

Sofern eine Datei erstellt werden soll, so muss diese als Multipart-Request angehängt werden, da ansonsten ein Ordner erstellt wird.


| **Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| folder_id | String, 32 Zeichen | Die ID der des Ordners, in dem eine neue Datei bzw. ein neuer Ordner erstellt werden soll


| **`POST`-Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| name | String | Name der Datei bzw. des Ordners
| (Datei) | binär | Daten der Datei als Multipart-Request


#### Forum

Ein Forum einer Veranstaltung bzw. einer Einrichtung ist aufgeteilt in mehrere Kategorien, welche Forenbeiträge besitzen. Forenbeiträge sind in einer Baumstruktur organisiert. Auf der obersten Ebene sind die Bereiche, z.B. "Allgemeine Diskussion". Unterhalb dieser sind die eigentlichen Themen als Kindelemente des "Bereichs-Beitrags" zu finden.

##### `GET` /forum_category/:category_id

Eine Kategorie eines Forums auslesen.


| **Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| category_id | String, 32 Zeichen | Die ID der Foren-Kategorie

###### Antwortformat

```json

{
   "areas_count" : 1,
   "entry_name" : "Allgemein",
   "seminar_id" : "a07535cf2f8a72df33c12ddfa4b53dde",
   "areas" : "/api.php/forum_category/a07535cf2f8a72df33c12ddfa4b53dde/areas",
   "category_id" : "a07535cf2f8a72df33c12ddfa4b53dde",
   "course" : "/api.php/course/a07535cf2f8a72df33c12ddfa4b53dde",
   "name" : "Allgemein",
   "pos" : "0"
}

```


##### `PUT` /forum_category/:category_id

Eine Kategorie eines Forums aktualisieren. Über den Parameter "name" kann der Name der Kategorie geändert werden.


| **Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| category_id | String, 32 Zeichen | Die ID der Foren-Kategorie


| **`PUT`-Parameter | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| name | String | Der neue Name der Kategorie


##### `DELETE` /forum_category/:category_id

Löscht eine Kategorie eines Forums.


| **Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| category_id | String, 32 Zeichen | Die ID der Foren-Kategorie


##### `GET` /forum_category/:category_id/areas

Liefert eine Liste mit Forenbeiträgen einer Forum-Kategorie zurück. Dabei werden nur die notwendigsten Daten des Forenbeitrags zurückgeliefert. Um mehr Daten zu einem Forenbeitrag zu erhalten, sollte die REST-Route /forum_entry/:entry_id aufgerufen werden (siehe unten).


| **Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| category_id | String, 32 Zeichen | Die ID der Foren-Kategorie


###### Antwortformat

```json

{
   "collection" : {
      "/api.php/forum_entry/fa431efbfa909ed48fbae10fef316222" : {
         "course" : "/api.php/course/a07535cf2f8a72df33c12ddfa4b53dde",
         "depth" : "1",
         "content" : "Hier ist Raum für allgemeine Diskussionen",
         "topic_id" : "fa431efbfa909ed48fbae10fef316222",
         "subject" : "Allgemeine Diskussion",
         "content_html" : "<div class=\"formatted-content\">Hier ist Raum für allgemeine Diskussionen</div>",
         "user" : "/api.php/user/",
         "anonymous" : "0",
         "mkdate" : "1477315889",
         "chdate" : "1477315889"
      }
   },
   "pagination" : {
      "total" : 1,
      "offset" : 0,
      "limit" : 20
   }
}

```


##### `POST` /forum_category/:category_id/areas

Erstellt einen neuen Forenbeitrag. Der Parameter "subject" gibt das Thema des Beitrags an. Der Inhalt des Beitrags wird über den Parameter "content" gesetzt. Über den optionalen Parameter "anonymous" kann eingestellt werden, ob der Beitrag anonym erstellt werden soll. Dazu muss "anonymous" auf 1 gesetzt sein.


| **Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| category_id | string, 32 Zeichen | Die ID der Foren-Kategorie


| **`POST`-Parameter | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| subject | String | Das Thema des Forenbeitrags
| content | String | der Inhalt des Forenbeitrags
| anonymous | Integer | Gibt an, ob der Eintrag anonym gemacht werden soll oder nicht: 1 = anonym, 0 = nicht anonym


##### `GET` /forum_entry/:entry_id

Liefert Daten zu einem Forenbeitrag zurück, inklusive dessen Inhalt.


| **Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| entry_id | String, 32 Zeichen | Die ID des Foren-Beitrags

###### Antwortformat

```json

{
   "topic_id" : "fa431efbfa909ed48fbae10fef316222",
   "user" : "/api.php/user/",
   "chdate" : "1477315889",
   "subject" : "Allgemeine Diskussion",
   "content_html" : "<div class=\"formatted-content\">Hier ist Raum f&#65533;r allgemeine Diskussionen</div>",
   "children" : [
      {
         "content_html" : "<div class=\"formatted-content\"><div>Test-Inhalt des Test-Themas.</div></div>",
         "subject" : "<div class=\"formatted-content\">Test-Thema</div>",
         "chdate" : "1477316401",
         "user" : "/api.php/user/76ed43ef286fb55cf9e41beadb484a9f",
         "topic_id" : "d4dc64e82387ad5df7d388377e70a54b",
         "anonymous" : "0",
         "depth" : "2",
         "mkdate" : "1477316401",
         "content" : "<div class=\"formatted-content\">Test-Inhalt des Test-Themas.</div>",
         "course" : "/api.php/course/a07535cf2f8a72df33c12ddfa4b53dde"
      }
   ],
   "content" : "Hier ist Raum f&#65533;r allgemeine Diskussionen",
   "course" : "/api.php/course/a07535cf2f8a72df33c12ddfa4b53dde",
   "mkdate" : "1477315889",
   "depth" : "1",
   "anonymous" : "0"
}

```


##### `PUT` /forum_entry/:entry_id

Aktualisiert einen Forenbeitrag. Der Parameter "subject" setzt den Titel, der Parameter "content" setzt den Inhalt.


| **Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| entry_id | String, 32 Zeichen | Die ID des Foren-Beitrags


| **`PUT`-Parameter | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| subject | String | Das Thema des Forenbeitrags
| content | String | Der Inhalt des Forenbeitrags


##### `DELETE` /forum_entry/:entry_id

Löscht einen Forenbeitrag.


| **Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| entry_id | String, 32 Zeichen | Die ID des Foren-Beitrags


##### `POST` /forum_entry/:entry_id

Fügt einem Forenbeitrag einen neuen Forenbeitrag (eine neue Antwort) hinzu.


| **Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| entry_id | string, 32 Zeichen | Die ID des Foren-Beitrags


| **`POST`-Parameter | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| subject | String | Das Thema des Forenbeitrags
| content | String | der Inhalt des Forenbeitrags
| anonymous | Integer | Gibt an, ob der Beitrag anonym angezeigt werden soll oder nicht: 1 = anonym, 0 = nicht anonym


#### Nachrichten

##### `POST` /messages

Schreibt eine neue Nachricht. Der Parameter "subject" setzt den Titel, während der Parameter "message" den Inhalt der Nachricht setzt. Zudem müssen die Empfänger der Nachricht ebenfalls angegeben werden. Dazu dient der Parameter "recipients", dem die Nutzer-IDs der Empfänger übergeben werden.


| **`POST`-Parameter | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| subject | String | Das Thema der Nachricht
| message | String | Der Inhalt der Nachricht
| recipients | Array | Die Nutzer-IDs der Empfänger der Nachricht


##### `GET` /message/:message_id

Liefert eine Nachricht zurück.


| **Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| message_id | String, 32 Zeichen | Die ID der Nachricht


##### `PUT` /message/:message_id

Mit dieser Route kann eine Nachricht als ungelesen oder gelesen markiert werden oder sie kann in einen anderen Nachrichtenordner verschoben werden.

Zum Markieren der Nachricht als ungelesen muss der Parameter "unread" auf 1 gesetzt sein. Für den umgekehrten Fall wird der Parameter auf 0 gesetzt.

Zum Verschieben der Nachricht in einen anderen Nachrichtenordner muss die Nutzer-ID, die ID des Ordners und der Nachrichtenbereich angegeben werden, sodass ein Pfad folgender Struktur angegeben werden muss:


| **Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| message_id | String, 32 Zeichen | Die ID der Nachricht


| **`PUT`-Parameter | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| unread | Integer | Gibt an, ob die Nachricht als ungelesen markiert werden soll (1) oder als gelesen markiert werden soll (0)

Die Parameter können dabei sowohl über ein JSON-Objekt im Body mit gesetztem `Content-Type: application/json` oder wie bei `POST`-Requests abgesetzt werden. Explizit kann der Parameter nicht über `GET`-Parameter gesetzt werden.

##### `DELETE` /message/:message_id

Löscht eine Nachricht.


| **Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| message_id | String, 32 Zeichen | Die ID der Nachricht


#### Semester

##### `GET` /semesters

Liefert eine Liste aller Semester zurück.

###### Antwortformat

```json

{
   "pagination" : {
      "offset" : 0,
      "limit" : 20,
      "total" : 2
   },
   "collection" : {
      "/api.php/semester/eb828ebb81bb946fac4108521a3b4697" : {
         "begin" : 1475272800,
         "end" : 1490997599,
         "seminars_end" : 1486162799,
         "title" : "WS 2016/17",
         "description" : "",
         "id" : "eb828ebb81bb946fac4108521a3b4697",
         "seminars_begin" : 1476655200
      },
      "/api.php/semester/f2b4fdf5ac59a9cb57dd73c4d3bbb651" : {
         "description" : "",
         "title" : "SS 2016",
         "seminars_begin" : 1460325600,
         "id" : "f2b4fdf5ac59a9cb57dd73c4d3bbb651",
         "seminars_end" : 1468619999,
         "end" : 1475272799,
         "begin" : 1459461600
      }
   }
}

```


##### `GET` /semester/:semester_id

Liefert ein einzelnes Semester zurück. Diese REST-Route liefert die gleiche Menge an Daten zu einem Semester, wie der Aufruf der Route /semesters.


| **Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| semester_id | String, 32 Zeichen | Die ID des Semesters

###### Antwortformat

```json

{
   "begin" : 1459461600,
   "title" : "SS 2016",
   "seminars_begin" : 1460325600,
   "seminars_end" : 1468619999,
   "end" : 1475272799,
   "id" : "f2b4fdf5ac59a9cb57dd73c4d3bbb651",
   "description" : ""
}

```


#### Ankündigungen

Die Routen zu Ankündigungen eines Nutzers, einer Veranstaltung oder des Stud.IP Systems sind in den jeweiligen Abschnitten oberhalb dieses Abschnittes beschrieben.

##### `GET` /news/:news_id

Liefert eine Ankündigung zurück.


| **Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| news_id | String, 32 Zeichen | Die ID der Ankündigung

###### Antwortformat

```json

{
   "news_id" : "29f2932ce32be989022c6f43b866e744",
   "chdate" : "1476445862",
   "comments" : "/api.php/news/29f2932ce32be989022c6f43b866e744/comments",
   "expire" : "14562502",
   "mkdate" : "1476445862",
   "topic" : "Herzlich Willkommen!",
   "chdate_uid" : "",
   "allow_comments" : "1",
   "comments_count" : 1,
   "user_id" : "76ed43ef286fb55cf9e41beadb484a9f",
   "ranges" : [
      "/api.php/user/76ed43ef286fb55cf9e41beadb484a9f/news",
      "/api.php/studip/news"
   ],
   "body" : "Das Stud.IP-Team heisst sie herzlich willkommen. \r\nBitte schauen Sie sich ruhig um!\r\n\r\nWenn Sie das System selbst installiert haben und diese News sehen, haben Sie die Demonstrationsdaten in die Datenbank eingefügt. Wenn Sie produktiv mit dem System arbeiten wollen, sollten Sie diese Daten später wieder löschen, da die Passwörter der Accounts (vor allem des root-Accounts) öffentlich bekannt sind.",
   "body_html" : "<div class=\"formatted-content\">Das Stud.IP-Team heisst sie herzlich willkommen. <br>Bitte schauen Sie sich ruhig um!<br><br>Wenn Sie das System selbst installiert haben und diese News sehen, haben Sie die Demonstrationsdaten in die Datenbank eingefügt. Wenn Sie produktiv mit dem System arbeiten wollen, sollten Sie diese Daten später wieder löschen, da die Passwörter der Accounts (vor allem des root-Accounts) öffentlich bekannt sind.</div>",
   "date" : "1476445862"
}

```


##### `PUT` /news/:news_id

Aktualisiert eine Ankündigung. Über den Parameter "topic" kann der Titel der Ankündigung gesetzt werden, während über den Parameter "body" der Inhalt der Ankündigung verändert werden kann.


| **Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| news_id | String, 32 Zeichen | Die ID der Ankündigung


| **`PUT`-Parameter | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| topic | String | Der Titel der Ankündigung
| body | String | Der Inhalt der Ankündigung
| expire | Integer | Ablaufdatum der Nachricht (in Sekunden vom aktuellen Datum gerechnet)
| allow_comments | Integer | Gibt an, ob Kommentare erlaubt sind: 1 = erlaubt, 0 = nicht erlaubt


##### `DELETE` /news/:news_id

Löscht eine Ankündigung.


| **Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| news_id | String, 32 Zeichen | Die ID der Ankündigung


##### `GET` /news/:news_id/comments

Liefert eine Liste der Kommentare zu einer Ankündigung zurück.


| **Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| news_id | String, 32 Zeichen | Die ID der Ankündigung

###### Antwortformat

```json

{
   "collection" : {
      "/api.php/comment/a97b1b623d92f11293d4a8de52724087" : {
         "chdate" : "1477320607",
         "comment_id" : "a97b1b623d92f11293d4a8de52724087",
         "object_id" : "29f2932ce32be989022c6f43b866e744",
         "content" : "Test-Kommentar",
         "content_html" : "Test-Kommentar",
         "mkdate" : "1477320607",
         "user_id" : "76ed43ef286fb55cf9e41beadb484a9f"
      }
   },
   "pagination" : {
      "total" : 1,
      "limit" : 20,
      "offset" : 0
   }
}

```


##### `POST` /news/:news_id/comments

Erstellt einen neuen Kommentar zu einer Ankündigung. Der Inhalt des Kommentars wird über den Parameter "content" gesetzt.


| **Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| news_id | String, 32 Zeichen | Die ID der Ankündigung


| **`POST`-Parameter | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| content | String | Der Inhalt des Kommentars


##### `GET` /comment/:comment_id

Liest einen Kommentar zu einer Ankündigung aus.


| **Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| news_id | String, 32 Zeichen | Die ID des Kommentars

###### Antwortformat

```json

{
   "content" : "Test-Kommentar",
   "comment_id" : "a97b1b623d92f11293d4a8de52724087",
   "content_html" : "<div class=\"formatted-content\">Test-Kommentar</div>",
   "chdate" : "1477320607",
   "author" : "/api.php/user/76ed43ef286fb55cf9e41beadb484a9f",
   "mkdate" : "1477320607",
   "news" : "/api.php/news/29f2932ce32be989022c6f43b866e744"
}

```


##### `DELETE` /comment/:comment_id

Löscht einen Kommentar zu einer Ankündigung.


| **Parameter** | **Format** | **Beschreibung**
| ---- | ---- | ---- |
| news_id | String, 32 Zeichen | Die ID des Kommentars


### Erweiterung der REST-API im Kern

Zur Implementierung neuer REST-Routen im Kern muss eine Klasse geschrieben werden, welche den Namespace \RESTAPI\Routes nutzt und die Klasse \RESTAPI\RouteMap erweitert. In der Klasse RouteMap (lib/classes/restapi/RouteMap.php) ist in den Kommentaren eine umfangreiche Beschreibung vorhanden, welche erklärt, wie REST-Routen erzeugt werden können.


Die Datei, welche die neue Klasse beinhaltet muss unter app/routes/ liegen, den gleichen Namen wie die enthaltene Klasse tragen und ihr Name muss in der Klasse Router (lib/classes/restapi/Router.php) in der Methode setupRoutes() eingetragen werden. Innerhalb dieser Methode befindet sich folgende Codezeile:

```php
$routes = words('Activity Blubber Contacts ...');
```

Innerhalb der Zeichenkette, welche words übergeben wird, muss der Name der Datei mit der neuen REST-API-Klasse eingetragen werden.


### Erweiterung der REST-API durch Plugins

Damit die REST-API auch durch Plugins erweitert werden kann, wurde die neuen Pluginklasse `RESTAPIPlugin` erschaffen. Plugins dieser Art müssen eine einzige Methode names `getRouteMaps()` implementieren, welche alle REST-API Routen (RouteMaps genannt) zurückliefert, die das Plugin implementiert.

#### Struktur REST-API Plugin

Zum Bereitstellen von Routen muss das Plugin eine Klasse enthalten, die die Klasse RouteMap erweitert. In dieser muss mindestens die Methode `before` implementiert sein. Für einfache Routen kann diese Methode leer gelassen werden.

Im Anschluss müssen Methoden für die zu implementierenden Routen geschrieben werden. Eine Methode kann hierbei mehrere Routen implementieren. Durch den Kommentar überhalb einer Methode werden die Routen angegeben, die zum Aufruf der Methode führen. Dabei können auch Parameter und Parameterbedingungen im Kommentar definiert werden. Ein Beschreibungstext zur Route muss auch in den Kommentar hinein.

#### Ausgeben von Daten

Daten, welche über die API ausgegeben werden sollen, werden einfach von der Methode zurückgegeben (via return). Dabei kann es sich um einfache Strings, Zahlen oder Objekte handeln.

Zum Versenden einer Liste von Objekten über die API sollte die Methode `paginated` verwendet werden, wie folgendes Beispiel zeigt:

```php
return $this->paginated($data, $total, $uriParameters);
```

$data enthält die zu sendenden Daten, $total die Anzahl der Datensätze (gesamter Datenbestand) und $uriParameters notwendige Parameter, die bei der Generierung von URIs für die aktuelle Route benötigt werden. Die Parameter "offset" und "limit" werden von der RouteMap-Klasse bereits intern behandelt und müssen nicht extra angegeben werden.


#### Überschreiben von Kernrouten

Es ist möglich, vorhandene Kernrouten zu überschreiben. Dabei sollte aber in jedem Fall sichergestellt sein, dass die Rückgabe dem eigentlichen Format entspricht. 

#### Beispiel eines REST-API Plugins

Das folgende Beispiel zeigt ein REST-API Plugin, welches eigene API-Routen implementiert:

Zuerst die Plugin-Klasse:

```php
<?php
/**
 * HelloAPIPlugin.class.php
 *
 * @author  Jan-Hendrik Willms <tleilax+studip@gmail.com>
 * @author  Moritz Strohm <strohm@data-quest.de>
 * @version 1.0
 */

require_once __DIR__ . '/HelloMap.class.php';

class HelloAPIPlugin extends StudIPPlugin implements RESTAPIPlugin
{
    public function getRouteMaps()
    {
        return new HelloMap();
    }
}
```

Die Plugin-Klasse liefert beim Aufruf der Methode getRouteMaps einfach eine Instanz der HelloMap-Klasse zurück.

Die HellpMap-Klasse sieht folgendermaßen aus:

```php
class HelloMap extends \RESTAPI\RouteMap
{
    // Called before the route is executed
    public function before() {}

    /**
     * Greets the caller
     *
     * @`GET` /hello
     * @`GET` /hello/:name
     * @condition name ^\w+$
     */
    public function sayHello($name = 'world')
    {
        return sprintf('Hello %s!', $name);
    }

    // Called after the route is executed
    public function after() {}
}
```

Über die HelloMap-Klasse stellt das Plugin die Routen /hello und /hello:name bereit. Der Aufruf dieser beiden Routen führt zum Aufruf der Methode `sayHello`. Ist der Parameter "name" gesetzt, so wird der entsprechende Name zurückgeliefert.
