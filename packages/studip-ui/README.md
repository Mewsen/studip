# 📝 README: UI-Kit (Stud.IP)

Das UI-Kit ist die zentrale Bibliothek für wiederverwendbare Vue-Komponenten und Styling-Utilities, die in allen modernen Teilen der Stud.IP-Anwendung zum Einsatz kommen. Es stellt die **Design-Grundlage (Design Tokens)** und das **Look & Feel** sicher, das für eine konsistente Nutzererfahrung erforderlich ist.

| Metrik | Wert |
| :--- | :--- |
| **Framework** | Vue 3 (Composition API) |
| **Build-Tool** | **Vite** |
| **Test-Runner** | **Vitest** (mit Coverage) |
| **Code-Qualität** | **ESLint** und **Prettier** |
| **Dokumentation** | **Storybook** |

---

## 🛠️ Entwicklung & Setup

### 1. Lokale Installation

Stellen Sie sicher, dass Sie die Abhängigkeiten installiert haben:

```bash
npm install
```

## 🛠️ Entwicklung & Setup

### 2. Lokale Entwicklungsserver

| Ziel | Befehl | Beschreibung |
| :--- | :--- | :--- |
| **Komponenten-Entwicklung** | `npm run dev` | Startet den **Vite**-Entwicklungsserver. |
| **Storybook-Entwicklung** | `npm run storybook` | Startet den isolierten Entwicklungsserver für die Komponenten-Dokumentation unter **`http://localhost:6006`**. |
| **Build-Vorschau** | `npm run preview` | Dient zur schnellen lokalen Vorschau des produktionsfertigen Bundles. |

---

## 🧪 Tests & Code-Qualität

Wir verwenden **Vitest** für Unit-Tests und **ESLint/Prettier** zur Einhaltung der Code-Standards.

### 1. Testen

| Ziel | Befehl | Beschreibung |
| :--- | :--- | :--- |
| **Unit-Tests ausführen** | `npm run test:unit` | Führt alle Tests aus und generiert einen **Code Coverage Report**. |

### 2. Linting & Formatierung

| Ziel | Befehl | Beschreibung |
| :--- | :--- | :--- |
| **Formatierung korrigieren** | `npm run format` | Formatiert alle Dateien im **`src/`**-Verzeichnis automatisch mit **Prettier**. |
| **Code-Qualität prüfen** | `npm run lint:check` | Prüft den Code mit **ESLint** ohne Fehlerkorrektur. Dieser Schritt ist Teil des `build`-Prozesses. |
| **Code-Qualität korrigieren** | `npm run lint` | Prüft den Code mit **ESLint** und versucht, alle behebbaren Fehler zu korrigieren. |

---

## 📦 Build-Prozess

Der Build-Prozess ist mehrstufig und stellt die Konsistenz und Verfügbarkeit aller Assets sicher.

| Ziel | Befehl | Schritte |
| :--- | :--- | :--- |
| **Gesamter Build** | `npm run build` | 1. Code-Qualität prüfen (`lint:check`). 2. Komponenten mit **Vite** bauen. 3. **Icon-Assets** erstellen. |
| **Storybook Build** | `npm run build-storybook` | Erstellt einen statischen Build der gesamten Storybook-Dokumentation (für Hosting). |
| **Storybook Build Zip** | `npm run build-storybook:zip` | Erstellt aus dem statischen Build der gesamten Storybook-Dokumentation eine Zip-Datei. |
| **Icons bauen** | `npm run build:icons` | **Separater Schritt**, um Icon-Assets/Sprite-Sheets zu generieren. |

---

## 💡 Wichtige Konventionen

* **Tests:** Für die Entwicklung wird empfohlen, **`vitest --watch`** in einem separaten Terminal zu starten, da `test:unit` direkt den Coverage-Report erstellt (der länger dauert).
* **Styling:** Verwenden Sie ausschließlich **CSS Custom Properties (`var(--...)`)** für Farben, Abstände und Schriftgrößen, um die Theming-Fähigkeit zu gewährleisten.
* **Accessibility (A11Y):** Achten Sie auf korrekte **`aria-labels`** und **`role`**-Attribute bei interaktiven Komponenten.