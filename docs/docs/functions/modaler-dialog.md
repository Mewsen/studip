---
id: modaler-dialog
title: Modaler Dialog
sidebar_label: Modaler Dialog
---

### Serverseitige erzeugte Abfragen

Um einen modalen Dialog zu erzeugen, kann man ganz einfach die Methode `PageLayout::postQuestion();` verwenden. Diese Methode kapselt in sich die Erzeugung eines entsprechenden `QuestionBox`-Objekts und setzt die entsprechenden Parameter. Die Abfrage wird dann analog zu den [`MessageBoxen`](MessageBox) bei nächstmöglicher Gelegenheit im System angezeigt.

Die `QuestionBox` kann die Antwort sowohl als `GET` als auch als `POST`-Request absetzen. Im Standardfall bei `PageLayout::postQuestion()` wird ein `POST`-Request abgesetzt, wodurch eine einfache Unterscheidung zwischen Bestätigung und Ablehnung der Frage schon alleine durch die genutzte Request-Methode erreicht werden kann.

Die Funktion bzw. die Erzeugung einer `QuestionBox` benötigt mindestens 1, maximal 3 Parameter.

#### Parameter
* `$question`: Der Frage bzw. die Aktion, die bestätigt werden soll
* `[$approveParams]`: optional, Link Parameter für den URLHelper im Falle einer positiven Antwort, in der Form `['name' => wert, 'name2' => wert2]`.
* `[$disapproveParams]`: optional, Link Parameter für den URLHelper im Falle einer negativen Antwort. 

Die Rückgabe der Methode ist ein `QuestionBox`-Objekt, welches noch weiter manipuliert werden kann.

#### Weitere Methoden der QuestionBox

Das `QuestionBox`-Objekt stellt darüber hinaus noch weitere Methoden zur Verfügung:

| Funktion | Beschreibung | 
| ---- | ---- |
| `setApproveParameters(array $parameters)` |  Setzt die Link Parameter für die positive Bestätigung|
| `setApproveURL($url)` | Setzt die URL, die bei einer positiven Bestätigung aufgerufen werden soll|
| `setDisapproveParameters(array $parameters)` | Setzt die Link Parameter für die negative Bestätigung |
| `setDisapproveURL($url)` | Setzt die URL, die bei einer negativen Bestätigung aufgerufen werden soll |
| `setBaseURL($url)` | Setzt die URLs für die positive und die negative Bestätigung auf den gleichen Wert |
| `setMethod($method)` | Setzt die zu nutzende Request-Methode (es ist anzuraten, immer `POST` zu nutzen; in dem Fall wird auch immer ein gültiges [CSRF-Token](CSRFProtection) in dem Request enthalten sein) |
| `includeTicket()` | Weist die QuestionBox an, ein frisches Stud.IP-Ticket beim Rendern einzufügen |


#### Beispiel
```php
PageLayout::postQuestion(_('Wollen Sie dies wirklich löschen?'), $accept_url = *, $decline_url = *);
```

#### Screenshot
![image](../assets/fbce782c9fa1a8778926c5f6ade1d5d4/image.png)


### Clientseitige Dialoge (*data-dialog"*)

Die Ziele dahinter sind sowohl ein einheitliches Verhalten von Dialogen innerhalb von Stud.IP als auch eine Erleichterung für den Entwickler. Im Idealfall muss kein JavaScript mehr angefasst werden, um Dialoge zu nutzen. Die einzige Anpassung auf Serverseite ist das Auszeichnen des HTML mit entsprechenden Attributen und das Entfernen des umgebenden Layouts, so dass nur der wirklich relevante Inhalt zurückgegeben wird.

#### Einbindung

Dialoge können im HTML an den Tags `<a>`, `<button>` und `<form>` über das Attribut **`data-dialog`** gesteuert werden. Derart ausgezeichnete Elemente werden bei aktiviertem Javascript ihre Inhalte in einem modalen Dialog anzeigen. Die Inhalte werden dabei per AJAX nachgeladen und mittels jQuery UI's Dialog-Widget angezeigt. Auf Serverseite kann ein Aufruf, der aus einem solchen Dialog erfolgte, an dem HTTP-Header `X-Dialog` erkannt werden.

Sollte bereits ein Dialog geöffnet sein und ein entsprechend ausgezeichnetes Element innerhalb des Dialogs aufgerufen werden, so wird der aktuelle Dialog aktualisiert, man verbleibt also im Dialog.

Zu beachten: In der Rückgabe enthaltene `<script>`-Tags werden gefiltert und ausgeführt. Dies ist kein Standardverhalten von jQuery UI's Dialog und sollte beachtet werden (auch wenn derartiges Inline-Javascript im Idealfall vermieden und stattdessen auf globale Handler zurückgegriffen werden sollte).

#### Parameter

Der Dialog kann über verschiedene Attributangaben oder HTTP-Header gesteuert werden, welche im Folgenden erläutert werden. Dabei gilt, dass die Attributangaben auch beliebig miteinander kombiniert werden können, bspw. `data-dialog="title=foo;size=auto;buttons=false"`.

##### Titel

Der Titel eines Dialogs ist standardmässig der Inhalt des title-Attributs  des zugrundeliegenden Elements (sofern vorhanden) und fällt bei den Tags `<a>` und `<button>` auf den Textinhalt des Elements zurück.  Der Titel kann über verschiedene Parameter gesteuert werden:

| Parameter | Beschreibung | 
| ---- | ---- |
|`data-dialog="title='Test Titel'"` |Setzt den Titel auf **Test Titel** |
|HTTP-Header `X-Title: Test Titel 2` |Setzt den Titel auf **Test Titel 2** |

##### Größe

Die Größe des Dialogs ist standardmässig 2/3 der Breite und Höhe und des Browserfensters. Dieser Wert kann über optionale Parameter gesteuert werden, wobei die minimale Größe des Dialogs auf 320x200 Pixel festgesetzt wurde:

| Parameter | Beschreibung |
| ---- | ---- |
|`data-dialog="width=X"` |Setzt die Breite des Dialogs auf **X** Pixel |
|`data-dialog="height=Y"` |Setzt die Höhe des Dialogs auf **Y** Pixel | 
|`data-dialog="size=XxY"` |Fasst die Angabe der Breite von **X** Pixeln und Höhe von **Y** Pixeln zusammen |
|`data-dialog="size=X"` |Erzeugt einen quadratischen Dialog mit **X** Pixeln Breite und Höhe | 
|`data-dialog="size=auto"` |Versucht, die Größe des Dialogs an den geladenen Inhalt anzupassen. |
|`data-dialog="size=big"` |Erzeugt einen großen Dialog mit viel Platz. | 
|`data-dialog="size=medium"` |Erzeugt einen Dialog mit moderat viel Platz. | 
|`data-dialog="size=medium-43"` |Erzeugt einen Dialog im 4:3 Verhältnis von Lange zu Breite. | 
|`data-dialog="size=big"` |Erzeugt einen kleinen Dialog der wenig Platz einnimmt. | 

##### Buttons

Standardmässig enthält jeder Dialog einen Button *Abbrechen* am unteren Rande des Dialogs, welcher den Dialog schliesst.

Es wird auch versucht, Buttons aus der Rückgabe zu extrahieren, damit diese sich ebenfalls in der Button-Leiste des Dialogs einreihen können. Dabei werden nur die Tags `<a>` und `<button>` berücksichtigt, welche entweder direkt mit dem Attribut `data-dialog-button` ausgezeichnet sind oder sich unterhalb eines Elements befinden, welche mit dem Attribut `data-dialog-button` ausgezeichnet wurde.

Sowohl Links als auch Formulare können auf diese Weise aufgerufen werden. Dies bedeutet im Besonderen dass, sich auch ein Speichern-Button eines Formulars am unteren Rande des Dialogs befinden kann.

Zu beachten ist, dass ein vorhandener Link/Button mit dem Inhalt *Abbrechen* mit dem Standardbutton überschrieben wird, welcher den Dialog schliesst. Dieses Verhalten ist gewollt und sollte beim Entwickeln berücksichtigt werden.

Die Button-Leiste kann über die folgenden Mechanismen komplett ausgeschaltet werden, was auch bedeutet, dass die Buttons nicht aus dem zurückgegebenen Inhalt extrahiert werden:

| Parameter | Beschreibung | 
| ---- | ---- |
| `data-dialog="buttons=false"` | HTTP-Header `X-No-Buttons` |


##### Weitere Optionen

Ein Dialog kann über die folgenden Mechanismen geschlossen werden:

| Parameter | Beschreibung | 
| ---- | ---- |
| `data-dialog="close"`| HTTP-Header `X-Dialog-Close |
| HTTP-Header `X-Location: <url>` | Bei der Auswertung einer Rückgabe kann auch ein Verweis auf eine andere Seite angegeben werden, welche den Dialog verlässt. Dies geschieht über folgenden Mechanismus: |
| `data-dialog="reload-on-close"` | Die den Dialog umgebende Seite kann beim Schliessen des Dialogs automatisch neu geladen werden |
| `data-dialog="resize=false"` |Der Dialog kann starr eingestellt werden, er kann also in der Größe nicht vom Nutzer verändert werden |
| HTTP-Header `X-WikiLink: <url>` | Über den HTTP-Header `X-WikiLink` kann eingestellt werden, zu welcher Seite das im Titel angezeigte Hilfe-Icon verweisen soll: |
| `data-dialog="center-content"` | Der Inhalt des Dialogs kann sowohl horizontal als auch vertikal zentriert werden |
| HTTP-Header `X-Dialog-Execute: <JS-Funktion, bspw. STUDIP.Foo.bar>` | Aus der Rückgabe heraus, kann  eine beliebige JavaScript-Funktion aufgerufen werden, welcher der Body des Requests übergeben wird (falls dieser JSON ist, wird er entsprechend umgewandelt). Ist die übegebene JavaScript-Funktion ungültig (nicht definiert oder keine Funktion), so wird ein entsprechender Fehler geworfen. |
| HTTP-Header `X-Dialog-Execute: {func: <JS-Funktion, bspw. STUDIP.Foo.bar>, payload: []}`| Alternativ kann in diesem Header ein JSON-kodiertes Array mit dem verpflichtendem Eintrag `func` als Funktionsnamen und dem optionalen Eintrag `payload` übergeben werden. Dies ist in Situationen notwendig, wo zwar der Dialog aktualisiert werden soll (als HTTML über den Body der AJAX-Response) aber auch über die angegebene Funktion `func` mittels des gelieferten Payloads Änderungen stattfinden sollen. |


Über die **CSS-Klasse** `hide-in-dialog` können Inhalte gezielt in Dialogen versteckt werden.

##### Unterstützte Events

Beim Öffnen und Schliessen des Dialogs werden jeweils JavaScript-Events getriggert, um dem Entwickler die Möglichkeit zu geben, das Verhalten der Inhalte dynamisch zu erweitern/ändern.

* Beim **Öffnen** wird der Event `dialog-open` getriggert
* Beim **Öffnen** und beim **Ändern** des Inhalts des Dialogs via AJAX wird der Event `dialog-update` getriggert
* Beim **Schliessen** wird der Event `dialog-close` getriggert

Beiden Events wird der aktuelle Dialog sowie die Optionen beim Aufruf übergeben. Exemplarischer Beispielcode:

```javascript
(function ($) {
   $(document).on('dialog-open', function (event, parameters) {
    var dialog  = parameters.dialog;
    var options = parameters.options;

    $(dialog).dialog('title', options.title + ' - adjusted');
  });
}(jQuery));
```

Beim Laden der Daten über AJAX wird nach dem Laden der Event `dialog-load` getriggert, welchem die Optionen und das verwendete jQuery-XMLHttp-Request (als `xhr`) übergeben wird.

Je nachdem, wie der Dialog aufgerufen wurde, erfolgen die Events an unterschiedlichen Stellen:

Wurde der Dialog implizit über das `data-dialog`-Attribut an einem Element geöffnet, werden die Events an eben diesem Element getriggert, während sie im expliziten/programmatischen Fall am globalen *document*-Objekt getriggert werden. Eine Ausnahme bildet der Event `dialog-update`. Dieser wird immer global am *document*-Objekt getriggert, damit er immer aufgerufen wird - unabhängig davon, ob das auslösende Element vorhanden ist oder nicht.

### Clientseitige Dialoge zur Dateneingabe

Dialoge, welche serverseitig zur Eingabe von Daten in Formularen geladen werden, sollen im Fehlerfall eine Fehlermeldung im Dialog anzeigen. Bei erfolgreicher Dateneingabe soll der Dialog geschlossen werden und die Seite, welche im Hintergrund des Dialoges sichtbar ist (und aus welcher der Dialog geladen wurde) neu geladen werden. Um dies zu ermöglichen, müssen folgende Dinge im Quellcode eingebaut werden:

* Das Formular, welches im Dialog angezeigt wird, muss das data-dialog Attribut besitzen, welches den Wert "reload-on-close" besitzt.
* Der Dialog wird über einen Link aufgerufen, bei dem ebenfalls das data-dialog Attribut mit dem Wert "reload-on-close" gesetzt ist.
* In der Aktion im Controller wird im Fehlerfall eine Fehlermeldung via PageLayout::postError (oder PageLayout::postMessage(MessageBox::error())) ausgegeben.
* Im Erfolgsfall wird in der Aktion im Controller der Header X-Dialog-Close hinzugefügt und nichts gerendert:

```php
<?php
$this->response->add_header('X-Dialog-Close', '1');
$this->render_nothing();
```

Damit wird das oben beschriebene Verhalten des Dialoges erreicht.

### Clientseitige Abfragen

Über die Methode `STUDIP.Dialog.confirm(question, yes_callback, no_callback);` kann eine Bestätigung einer Aktion abgefragt werden. Der Parameter `question` enthält den Text für die Bestätigung (wie bspw "Sind Sie sicher, dass Sie dieses Element löschen wollen?") und der `yes_callback` wird anschliessend aufgerufen, falls die Abfrage positiv bestätigt wurde. Der `no_callback` ist optional und würde in dem Fall aufgerufen werden, dass die Abfrage negativ bestätigt wird.
Der Handler kann auch als [Deferred](http://api.jquery.com/category/deferred-object/) genutzt werden:

```javascript
STUDIP.Dialog.confirm('Sind Sie sicher?'.toLocaleString()).done(function () {
    alert('Aktion wurde bestätigt');
}).fail(function () {
    alert('Aktion wurde nicht bestätigt');
});
```

Als Frage kann auch ein boole'scher Wert übergeben werden, was dazu führt, dass die Abfrage sofort als bestätigt bzw. abgelehnt gehandhabt wird.
