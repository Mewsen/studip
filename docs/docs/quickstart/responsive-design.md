---
title: Responsive Design
---

Damit Stud.IP auch für Endgeräte mit kleinen Bildschirmgrößen verwendbar sein kann, werden ein paar "media queries" verwendet, um sinnvolle "breakpoints" für Layout und GUI zu Verfügung zu stellen. Diese "breakpoints" unterscheiden sich nur in der minimalen Breite des "viewports", also die Breite des virtuellen Fensters, in das der (mobile) Browser die Seite hineinrendert. Abhängig von dieser Breite können Elemente passend skaliert oder überhaupt gezeigt bzw. versteckt werden. 

In Stud.IPs LESS/CSS werden die folgenden "media query"-Intervalle verwendet:

```CSS
/* tiny: kleine Smartphones (im Hochformat) mit einer Breite von weniger als 576px. */ 

/* small: Smartphones (im Querformat) mit einer Breite von 576px oder mehr */
@media (min-width: 576px) {  }

/* medium: z.B. Tablets mit einer Breite von 768px oder mehr */
@media (min-width: 768px) {  }

/* large: Desktops ab einer Breite von 1200px oder mehr */
@media (min-width: 1200px) {  }
```

Damit man diese "media queries" im LESS-Code leichter schreiben kann, gibt es dort spezielle Mixins, die einem helfen, responsive Regel zu schreiben:

```CSS
.media-breakpoint-tiny-up({  });
.media-breakpoint-small-up({  });
.media-breakpoint-medium-up({  });
.media-breakpoint-large-up({  });

/* Beispiel für die Nutzung: */

.calhead label {
  cursor: pointer;
  &:hover {
    color: @base-color-40;
  }

  .media-breakpoint-small-down({
    .button();
  });
}

/* oder auch: */
.media-breakpoint-tiny-down({
     #barTopStudip img {
         height: 33px;
         margin-top: 5px;
    }
});
```

Es können auch "media queries" verwendet werden, die in die andere Richtung (also kleiner als eine bestimmte Größe) gehen:

```CSS
/* tiny: kleine Smartphones (im Hochformat) mit einer Breite von weniger als 576px. */ 
@media (max-width: 575px) {  }

/* small: Smartphones (im Querformat) mit einer Breite kleiner als 768px */
@media (max-width: 767px) {  }

/* medium: z.B. Tablets mit einer Breite kleiner als 1200px */
@media (max-width: 1199px) {  }

/* large: Desktops ab einer Breite von 1200px oder mehr */
/* braucht man nicht, da es ja keine obere Grenze gibt */
```

Auch hier gibt es wieder LESS-Mixins:

```CSS
.media-breakpoint-tiny-down({  });
.media-breakpoint-small-down({  });
.media-breakpoint-medium-down({  });
.media-breakpoint-large-down({  });


.hidden-tiny-up
.hidden-small-up
.hidden-medium-up
.hidden-large-up

.hidden-tiny-down
.hidden-small-down
.hidden-medium-down
.hidden-large-down
```


# Darstellungsvarianten

Stud.IP ist eine Software, die sowohl auf verschiedenen Endgeräten/Gerätegrößen möglichst den vollständigen Funktionsumfang anzubieten versucht als auch für unterschiedliche Zielgruppen auf diesen verschiedenen Geräten möglichst gut bedienbar sein soll.

**Unterstützte Geräteklassen und deren Kennzeichen:**

**A. Smartphone**: Diese Geräte sind dadurch gekennzeichnet, dass
die maximale Breite sehr schmal ist (bis zu 767 Pixel bei regulärer Auflösung),
üblicherweise das Scrollen leicht möglich ist, die Seiten also durchaus lang werden dürfen,
die Geräte ausschließlich per Touch, also mit dem Finger bedient werden und auf diesen Geräten keine komplexen Inhalte erstellt werden. Die übliche Display-Ausrichtung ist hochkant.

**B. Tablet/kleine Desktopgeräte:** Diese Geräte sind dadurch gekennzeichnet, dass
die maximale Breite begrenzt ist (bis zu. 1024 Pixel bei regulärer Auflösung),
diese in der Regel per Touch bedient werden (Mausbedienung sollte ebenfalls möglich sein),
auf diesen Geräten selten komplexe Inhalte erstellt werden. Die überwiegende Display-Ausrichtung ist quer, hochkant in einigen Anwendungsfällen.

**C. Desktop/große Displays:** Diese Geräte sind dadurch gekennzeichnet, dass
die Breite mehr als 1024 Pixel aufweist (und quasi unbeschränkt ist),
diese ganz überwiegend per Maus bedient werden. Die übliche Display-Ausrichtung ist quer.

Siehe hierzu auch die neuen Darstellungsstufen unter https://gitlab.studip.de/studip/studip/-/wikis/Responsive-Navigation.

**Unterstützte UseCases und zugeordnete Nutzendengruppen:**

Neben verschiedenen Geräteklassen gibt es zwei wichtige Nutzendengruppen mit unterschiedlichen UseCases. Zwischen den Gruppen gibt es teils fließende Übergänge und Schnittmengen bei den UseCases. Letztlich stellen beide Gruppen daher unterschiedliche Pole dar, für die es jeweils Optimierungen gibt.

**1. Erstellung und Administration komplexer Inhalte**
- Die üblichen Stud.IP-Gruppen für diesen UseCases sind **Admins** und (eingeschränkt) auch Lehrende
- Der UseCase ist geprägt dadurch, dass über einen längeren Zeitraum Inhalte erstellt oder komplexe Inhalte bearbeitet werden
- typische genutzte Elemente sind große Tabellen (viele Elemente, viele Spalten, viele mögliche Aktionen) und umfangreiche Inhalte bestehend aus mehreren Medien-Objekten (Fließtext, Film, inaktive Elemente) die zudem in sich gegliedert sind (zB. durch ein Inhaltsverzeichnis oder Überschriften)
- Die vollständige Bedienung (insbesondere Navigation) des Systems und Nutzung von Kommunikationsfunktionen wird weiterhin erwartet und bleibt möglich
- Funktionen/Systembereiche können gewechselt, Aktionen der Sidebar ausgeführt und Kommunikationsfunktionen können aufgerufen werden
- Wichtige Anforderungen: Möglichst viel Platz für die zu bearbeitenden Elemente bei gleichzeitig noch möglicher Navigation

**2. Konsum und Interaktion mit Inhalten ohne diese zu verändern („Lernen“)**
- Die üblichen Stud.IP-Rechtestufen dieser Gruppe sind **Studierende** und (eingeschränkt) auch Lehrende
- Der UseCase ist geprägt dadurch, dass über einen längerer Zeitraum Inhalte rezipiert (Texte gelesen, Filme geschaut) werden
- typische Elemente sind umfangreiche Fließtexte, Medienobjekte (Audio oder Video) und interaktive Elemente (Fragen, Quizzes, Prüfungen)
- Die vollständige Bedienung tritt in den Hintergrund, üblicherweise wird über längere Zeit der gleiche Kontext dargestellt
- Zentrales Ziel ist: Möglichst keine (optische) Ablenkung durch Elemente des Systems, die über eine längere Zeit nicht benötigt werden (dabei auch keine Ablenkung durch Interaktionselementen, die Aufmerksamkeit binden) bei gleichzeitig möglich viel Platz für die Interaktion
- Es bleibt kein Platz für die gemeinsame Darstellung des Contents und der Bedienelemente/Navigation
- Zentrale Anforderung: Ausblenden aller störenden oder ablenkende Elemente und möglichst viel Platz für den Content


**Darstellungsmodus zur Unterstützung der beiden UsesCases**


**I. Vollbildmodus**

Der reguläre Vollbildmodus steht auf allen Seiten zur Verfügung, um die Nutzungsgruppe 1 (Admins/Lehrende) bei der Erstellung und Bearbeitung durchweg zu unterstützen. Der Modus ist auf alle Geräteklassen (A, B, C) optimiert.

Kennzeichen des aktivierten Vollbildmodus sind:

- Die blaue Kopfzeile bleibt eingeblendet und ermöglicht Zugriff auf die vollständige Navigation („Hamburger-Menu“) in allen Geräteklassen
- Der Browser selbst ist normal sichtbar (und damit auch alle anderen Fenster/Elemente des Betriebssystems)
- ~~Der Footer kann eingeblendet bleiben (noch zu klären - für mich brauchen wir den nicht wegzulassen)~~ Der Footer ist ausgeblendet, alle Navigationselemente des Footers sind Teil des Hamburgermenüs
- Um möglichst viel Platz für die Bedienung zu schaffen, wird die Sidebar über ein Einblendicon sichtbar bzw. wieder unsichtbar, im Default ausgeblendet
- Noch zu diskutieren: Geräteklassen B und C (Tablet/Desktop) könnten entsprechend dem UseCase auch Schnellsuche und Notification zeigen. Derzeit haben wir sie bewusst weggelassen, evtl. verwässert dies den Modus jedoch

Anzumerken ist, dass bei langen Seiten auf den Geräteklassen B und C beim Herunterscrollen die Resonsive Navigation durch das Einblenden des Hamburger Menüs ebenfalls verwendet wird, um ein schnelles Wechseln ohne Hochscrollen weiterhin zu ermöglichen.

**II. Fokusmodus**

Der UseCase 2 optimierte Fokusmodus kann in der Version 5.3 nur auf Seiten mit der neuen ContentBar (derzeit Courseware, Wiki und Material imOER-Campus) aktiviert werden, da davon ausgegangen werden kann, das dieser UseCase nur auf Seiten benutzt werden kann, auf denen aktiv Inhalte rezipiert werden. Der Fokusmodus ist insbesondere auf die Klasse B (Tablets) optimiert, da davon ausgegangen wird, dass damit das Lernen und Lesen am besten funktioniert. Aber auch auf Desktop (C) ist der Modus nutzbar, wenn ggf. weniger sinnvoll.

Kennzeichen des aktivierten Fokusmodus sind:

- Die blaue Kopfzeile wird ausgeblendet um sowohl maximalen Platz zu schaffen als auch die (versehentliche/aktive) Navigation zu unterbinden.
- Der Browser-eigene Vollbildmodus wird ebenfalls aktiviert, da davon auszugehen ist, das auch keinerlei Ablenkungen anderer Tabs oder Bedienelemente bzw. versehentliches Antippen (auf Touchgeräten, Klasse A und B) zu verhindern. Alle anderen Fenster/Elemente des Betriebssystems werden damit (soweit wir möglich) ausgeblendet.
- Die Sidebar wird ausgeblendet und kann nicht aktiviert werden
- Der Footer ist nicht sichtbar
- Die einzigen verbleibenden Bedienelemente außerhalb des Contents sind alle Elemente, die eine Bedienung innerhalb des Contents des gewählten Kontextes ermöglichen (zB. Inhaltsverzeichnis)
