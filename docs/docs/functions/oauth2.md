---
title: OAuth2
---

# Einrichtung

Damit Stud.IP als Authorization Server im Rahmen von OAuth2 auftreten
kann, müssen zuvor notwendig Schlüsseldateien erzeugt werden.

Um zu überprüfen, ob eine Stud.IP-Installation bereits mit
Schlüsseldateien versorgt ist, kann diese URL von
`root`-berechtigten Nutzenden aufgerufen werden:
`https://<STUD.IP-URL>/dispatch.php/admin/oauth2`

Dort wird überprüft, ob diese Dateien vorhanden sind:

- `config/oauth2/private.key`
- `config/oauth2/public.key`
- `config/oauth2/encryption_key.php`

Ist das nicht der Fall, können diese Dateien mit folgendem Aufruf im
Verzeichnis der Stud.IP-Installation erzeugt werden.

```shell
cli/studip oauth2:keys
```

Prüfen Sie hinterher unter obiger URL, dass alles ordnungsgemäß
eingerichtet wurde.

# Hinzufügen neuer OAuth2-Clients

Auf der OAuth2-Konfigurationsseite (`https://<STUD.IP-URL>/dispatch.php/admin/oauth2`) haben Sie die Möglichkeit,
OAuth2-Clients einzurichten. Klicken Sie dazu in der Sidebar auf
"OAuth2-Client hinzufügen". Füllen Sie nun das angezeigte Formular aus.

Wenn Sie erfolgreich einen Client hinzugefügt haben, erhalten Sie die
`client_id` und ggf. das `client_secret`. **Beachten Sie bitte, dass
das `client_secret` nur einmalig hier angezeigt wird.**

# Verwalten von OAuth2-Clients

Sie können auf der OAuth2-Konfigurationsseite
(`https://<STUD.IP-URL>/dispatch.php/admin/oauth2`) vorhandene Clients einsehen und
löschen. Ein Ändern der Konfiguration ist nicht vorgesehen. Wenn Sie
die Details eines OAuth2-Clients ändern wollen, löschen Sie die
vorhandene Konfiguration und legen eine neue an.

# Konfiguration von OAuth2-Clients

Nachdem Sie einen OAuth2-Client in Ihrer Stud.IP-Installation
eingerichtet haben, besitzen Sie nun die `client_id` und ggfs. das
`client_secret`.

Außerdem benötigen Sie nun noch die notwendigen URLs:

- Authorization URL: `https://<STUD.IP-URL>/dispatch.php/api/oauth2/authorize`
- Access Token URL: `https://<STUD.IP-URL>/dispatch.php/api/oauth2/token`

# Was unterstützt der Stud.IP OAuth2 Authorization-Server?

## Grant Types

Es werden folgende Grant Types laut Spezifikation unterstützt:

- Authorization Code Grant
- Authorization Code Grant with PKCE
- Refresh Token Grant


## Scopes
Aktuell ist nur ein `scope` vorgesehen: `api`. Dieser Scope erlaubt
vollen Zugriff auf alle Funktionen, die durch OAuth2 abgesichert werden.

## PKCE-Verfahren
Wenn neue Clients angelegt werden, wird abgefragt, ob der Client in
der Lage ist, kryptografische Geheimnisse zu bewahren. Darunter fallen
ausdrücklich also alle Apps. In diesem Fall muss das PKCE-Verfahren
verwendet werden: https://oauth.net/2/pkce/

# Aufräumen

Mit der Zeit sammeln sich naturgemäß widerrufene oder abgelaufene
Token in der Datenbank. Daher sollten regelmäßig folgendes Kommando
im Verzeichnis der Stud.IP-Installation aufgerufen werden, um diese
Token zu entfernen:

```shell
cli/studip oauth2:purge
```
