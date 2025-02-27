---
title: End-to-end Tests (Playwright)
---

# Welches Tool verwenden wir?

Um Stud.IP mit Tests zu versehen verwenden wir seit der Version 5.4
das Werkzeug Playwright, siehe https://playwright.dev

Playwright ist ein Werkzeug für End-to-End-Tests von Webanwendungen:
- Unterstützt mehrere Browser (Chromium, Firefox, WebKit)
- Führt Tests auf verschiedenen Betriebssystemen aus
- Ermöglicht browserübergreifende Tests mit einer einheitlichen API
- Wartet automatisch auf interaktionsbereite UI-Elemente
- Führt Tests parallel aus
- Kann Videos und Screenshots der Testausführung aufzeichnen

Playwright ist schon als Abhängigkeit in `package.json` enthalten und
wird über `npm install` installiert.

## Wie konfiguriere ich Playwright?

Um mit Playwright arbeiten zu können, benötigst du eine laufende
Stud.IP-Installation, die folgende SQL-Dumps eingespielt hat:

- `db/studip.sql`
- `db/studip_default_data.sql`
- `db/studip_demo_data.sql`
- `db/studip_resources_default_data.sql`

Dann müssen Umgebungsvariablen gesetzt werden:

- `PLAYWRIGHT_BASE_URL`

Das passiert am einfachsten in einer `.env`-Datei. Also zum Beispiel:

```
PLAYWRIGHT_BASE_URL="http://localhost:8080"
```

## Wie führe ich Playwright aus?

Dazu kannst du auf der Kommandozeile folgenden Aufruf starten:

```shell
npx playwright test
```

Da aktuell die A11y-Tests fehlschlagen, kannst du auch die mit dem Tag
`a11y` versehenen Tests überspringen:

```shell
npx playwright test -gv a11y
```
