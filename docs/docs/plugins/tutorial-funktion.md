Dieses Tutorial stellt den zweiten Teil eines Plugin Tutorials dar.
Im [ersten Teil](Plugin-Tutorial-I-(Plugin-Struktur)) wurde bereits die grundlegende Plugin-Struktur erstellt
und einige Basiskonzepte erläutert, welches beides allgemein für jedes Plugin gelten.  
Dieser Teil implementiert nun ein explizites Beispiel eines Plugins und soll nur veranschaulichen,
wie beispielsweise gewünschte Funktionalität in einem Plugin umgesetzt werden kann.

Es ist daher natürlich stark zu empfehlen, den ersten Teil nachverfolgt zu haben
und es sollten die Basiskonzepte wie [Trails](Trails) verstanden worden sein.
Am Ende dieser Seite ist wieder eine ZIP-Datei mit dem erstellten Code und zusätzlicher phpDoc angehangen.

Nun wird der Kontext des zu implementierenden wirklich relevant:  
Auf einer Übersichtsseite sollen Nutzende alle erstellten "Texte" einsehen können.  
Sie sollen außerdem die Möglichkeit erhalten,
Texte selber zu erstellen und ihre existierenden Texte zu bearbeiten.  


## Übersichtsseite
Als simples Beispiel einer action inklusive ihrer view,
erstellen wir eine Übersichtsseite auf der alle existierenden Texte in einer Tabelle angezeigt werden.
Dazu befüllen wir die bereits im ersten Teil erstellte `index_action` und ihre `index.php`-view.

### index action
In der action-Methode laden wir also alle nötigen Daten, welche die View benötigt.
In diesem Fall benötigen wir nur alle Text-Objekte,
die wir mit `$this->all_texts = \TextPlugin\Text::findBySQL("1");` laden und der view zur Verfügung stellen können.
Außerdem aktivieren wir mit `Navigation::activateItem('text_root/text_overview');` das aktuelle Navigationselement
und setzen mit `PageLayout::setTitle($this->_('Texte Übersicht'));` den Seitentitel,
der in der Sidebar der Seite angezeigt wird.

```php
    public function index_action()
    {
        Navigation::activateItem('text_root/text_overview');
        PageLayout::setTitle($this->_('Texte Übersicht'));

        $this->all_texts = \TextPlugin\Text::findBySQL("1");
    }
```

### index view
Die geladenen `Text`-Objekte sind nun also in der `overview/index.php` über die Variable `$all_texts` zugreifbar.
Die view befüllen wir mit einer Tabelle, die den Titel, den Typ, den oder die Autorin und das Erstellungsdatum anzeigt.
```html
<table class="default sortable-table">
    <caption>
        <?= $_('Texte') ?>
    </caption>
    <colgroup>
        <col>
        <col style="width: 80px">
        <col style="width: 20%">
        <col style="width: 80px">
    </colgroup>
    <thead>
    <tr>
        <th data-sort="text"><?= $_('Titel'); ?></th>
        <th data-sort="text"><?= $_('Typ'); ?></th>
        <th data-sort="text"><?= $_('Autor*in'); ?></th>
        <th data-sort="digit"><?= $_('Erstellt am'); ?></th>
    </tr>
    </thead>
    <tbody>
    <? if ($all_texts): ?>
        <? foreach ($all_texts as $text_obj) : ?>
            <tr>
                <td><?= htmlReady($text_obj->title); ?></td>
                <td><?= $text_obj->getTypeDescription(); ?></td>
                <td>
                    <a href="<?= URLHelper::getLink('dispatch.php/profile?username=' . $text_obj->author->username) ?>">
                        <?= htmlReady($text_obj->author->getFullName()) ?>
                    </a>
                <td><?= strftime('%x', $text_obj->mkdate); ?></td>
            </tr>
        <? endforeach; ?>
    <? else: ?>
        <tr>
            <td colspan="4">
                <?= MessageBox::info($_('Es wurden keine Texte gefunden.')) ?>
            </td>
        </tr>
    <? endif; ?>
    </tbody>
</table>

```
Details zur view:
* Die Tabelle kann sortierbar gemacht werden, indem die class `sortable-table` für das `<table>` element genutzt wird 
und jeweils für die `<th>` elemente ein typ mit `data-sort` angegeben wird ([Tablesorter](Templates#Tabellen))
* Die Strings wie `Titel` sind mit `$_($string)`als übersetzbar gekennzeichnet
* `$all_texts` ist die aus der action-methode geladene variable und ist ein Array aus `Text`-Objekten,
weshalb bspw. mit `$text_obj->title` der Titel des aktuellen `Text`-Objekts ausgegeben werden kann.
Da wir in der `configure()`-Methode der Text-Modelklasse angegeben hatten,
dass `author` ein `User`-Objekt ist,
welches mit dem Fremdschlüssel `author_id` identifiziert wird,
kann mit `$text_obj->author` direkt auf das `User`-Objekt zugegriffen werden
und so der Name mit `getFullName()` ausgegeben werden (siehe [Relationen in SORM](SimpleORMap#Relationen)).
* `$text_obj->getTypeDescription();` wird im nächsten Unterabschnitt ausführlich erläutert
* Mit `URLHelper::getLink('dispatch.php/profile?username=' . $text_obj->author->username)` wird ein Link erstellt,
welcher auf das Profil des Nutzers führt (siehe [URLHelper](URLHelper)).
* Mit `strftime('%x', $text_obj->mkdate)` wird der Unix-Timestamp der in mkdate gespeichert ist,
als Datum ausgegeben (siehe [php strftime](https://www.php.net/manual/de/function.strftime.php)).
* Wenn keine Texte existieren, wird mit `MessageBox::info()` eine Info-Box erstellt (siehe [MessageBox](MessageBox)).

### Typ eines Text-Objekts
In der migration hatten wir den `type` eines `Text`-Objekts als `TINYINT(2) NOT NULL DEFAULT 1` festgelegt,
sodass dort ein Integer abgespeichert ist.
Die Idee des `type`-Feldes ist es, dass wir einen text-typen wie "Kurzgeschichte", "Roman" etc. angeben können.
Dazu kann man ein `enum` nutzen, da wir nur vorgefertigte typen zu lassen.
Dies würde aber bedeuten, dass wir jedes mal die Datenbank anpassen müssen,
wenn wir bspw. einen neuen Typen hinzufügen oder einen umbenennen möchten.  
Stattdessen nutzen wir einen Integer und definieren uns die Typen im Code,
an genau einer Steller, sodass wir minimale Änderungen durchführen müssen,
wenn wir die verfügbaren Typen anpassen.

In einer statischen `getTypes()` Methode in der `Text`-Modelklasse definieren wir alle verfügbaren Typen.

```php
public static function getTypes(): array
{
    return [
        1 => dgettext(Plugin::GETTEXT_DOMAIN, 'Kurzgeschichte'),
        2 => dgettext(Plugin::GETTEXT_DOMAIN, 'Roman'),
    ];
}
```
Hier kann auch beispielhaft gesehen werden, wie Strings innerhalb von Modelklassen als übersetzbar gekennzeichnet werden.  
Um den aktuellen Typen eines Texts als Klartext und nicht als Integer zu erhalten,
fügen wir auch noch eine `getTypeDescription()`-Methode hinzu.

```php
public function getTypeDescription(): string
{
    return self::getTypes()[$this->type] ?? 'Unbekannter Typ!';
}
```

Wenn wir nun einen neuen Typen hinzufügen möchten,
können wir diesen einfach in den array innerhalb von `Text::getTypes()` ergänzen.  
Da Integers zur Identifizierung genutzt werden, werden auch Tippfehler beim Vergleichen des Typs vermieden,
bspw. `if ($text_obj->type === 'kurzgeshcichte') [...]`.  
Das Definieren eines enums kann jedoch in einigen Fällen trotzdem sinnvoll sein,
falls beispielsweise klar ist, dass die enums nie erweitert werden.

## Sidebar
Die Übersichtsseite zeigt nun alle Texte an,
jedoch gibt es noch keine Möglichkeit, Texte zu erstellen.
Dazu erstellen wir eine Aktion in der Seitenleiste (Sidebar) der Übersichtsseite,
mit der eine neue Ansicht aufgerufen wird,
in der dann ein Text erstellt werden soll.

Im `OverviewController` erstellen wir eine neue Methode,
die nur zum Aufbauen der Sidebar dient.
Wie die Sidebar zu nutzen ist, ist in [Sidebar](Sidebar) erläutert.
```php
    private function buildSidebar()
    {
        $sidebar = Sidebar::Get();

        $actionWidget = $sidebar->addWidget(new ActionsWidget());
        $actionWidget->addLink(
            $this->_('Text erstellen'),
            $this->url_for('overview/edit_text'),
            Icon::create('add'),
            ['data-dialog' => true]
        );
    }
```
Wir erstellen ein neues `ActionsWidget` und geben einen Link zu einer neuen action `edit_text` an,
die wir später erstellen und befüllen werden (siehe [Trails](Trails)).  
Als [Icon](Icon) wird ein `add`-Symbol übergeben.
Eine Übersicht der Icons ist in [Visual-Style-Guide](Visual-Style-Guide#Icons) verfügbar.  
Außerdem soll die view zum Erstellen eines Texts in einem dialog geöffnet werden.
Views können in Stud.IP allgemein in einem dialog geöffnet werden,
indem `data-dialog` gesetzt wird.
Weiteres zu Dialogen in Stud.IP ist in [ModalerDialog](ModalerDialog) einsehbar.  
Schließlich sollte noch dran gedacht werden, die neu erstellte Methode in der `index_action`-methode aufzurufen,
damit die Sidebar auch auf der Übersichtsseite angezeigt wird.

## Texte erstellen
Nun geht es darum, die action und view zum Erstellen eines Texts zu bauen.

### Form erstellen
Um auch das nachträgliche Bearbeiten von Texten zu ermöglichen und redundanten Code zu vermeiden,
kombinieren wir direkt das Erstellen und das Bearbeiten von Texten.
Es kann jedoch je nach Situation und Kontext auch sinnvoll sein, dass Erstellen
und Bearbeiten in einzelne actions und views aufzuteilen, bspw. wenn die beiden
Sichten sich stark unterscheiden.  
Da wir beim Erstellen und Bearbeiten keinen Unterschied machen möchten,
rufen wir auch immer `edit_text_action` auf, wenn wir einen Text erstellen möchten.

In der `edit_text_action` laden wir wieder alle nötigen Daten,
die die View benötigt.
In diesem Fall ist das lediglich ein `Text`-Objekt.

```php
    public function edit_text_action(string $text_id = '')
    {
        PageLayout::setTitle($this->_('Text bearbeiten'));
        $this->text_obj = \TextPlugin\Text::find($text_id);
        if (!$this->text_obj) {
            $this->text_obj = new \TextPlugin\Text();
        }
    }
```

Da die `edit_text_action` für das Erstellen und Bearbeiten von Texten verantwortlich ist,
versuchen wir erst einmal einen angegebenen Text zum Bearbeiten zu finden.
Falls keine `$text_id` angegeben wurde, erstellen wir stattdessen ein `Text`-Objekt.
Angemerkt werden sollte, dass hier lediglich ein `Text`-Objekt erstellt wird,
welches aber noch nicht in die Datenbank gespeichert wird.

Da die `edit_text_action` im OverviewController ist,
erstellen wir eine `edit_text.php` im Verzeichnis `views/overview`.
Die View enthält dabei ein Text-Input für den Titel,
eine Textarea für die Beschreibung und eine Select Tag für den Typen.

```html
<? use Studip\Button; ?>
<form class="default collapsable" action="<?= $controller->link_for('overview/store_text', $text_obj->id) ?>"
      method="post">
    <?= CSRFProtection::tokenTag() ?>
    <fieldset data-open="bd_basicsettings">
        <legend>
            <?= $_('Grunddaten') ?>
        </legend>

        <div>
            <label class="required">
                <?= $_('Titel') ?>
            </label>
            <input name="title" required value="<?= $text_obj->title ?>">
        </div>

        <div>
            <label>
                <?= $_('Beschreibung') ?>
            </label>
            <textarea name="description"><?= $text_obj->description ?></textarea>
        </div>

        <div>
            <label class="required">
                <?= $_('Text Typ') ?>
            </label>
            <select name="type" required>
                <? foreach (\TextPlugin\Text::getTypes() as $type_key => $type_label): ?>
                    <option value="<?= $type_key ?>" <? if ($type_key == $text_obj->type) echo 'selected' ?>>
                        <?= $type_label ?>
                    </option>
                <? endforeach; ?>
            </select>
        </div>

    </fieldset>

    <footer data-dialog-button>
        <?= Button::create($_('Übernehmen')) ?>
    </footer>
</form>
```
Details zur View:
* Mit `$controller->link_for()` erstellen wir direkt einen Link für das `<form>`-element,
welcher auf eine `store_text_action` im OverviewController verweist,
in der wir später die angegebenen Daten abspeichern.
* Mit `CSRFProtection::tokenTag()` erstellen wir einen Token,
den wir in der `store_text_action` nutzen, um "Cross-Site Request Forgery" zu verhindern ([CSRFProtection](CSRFProtection))
* Mit `Button::create($string)` wird ein submit-button erstellt (siehe [Buttons](Buttons)).  
Der submit-button wird im `<footer>`-element mit `data-dialog-button` erstellt,
damit er in dialog-Fenstern neben dem "Schließen"-button angezeigt wird.

### Form-Daten speichern
Schließlich müssen noch die Nutzereingaben abgespeichert werden.
Dazu erstellen wir im OverviewController eine `store_text_action`,
auf die die form in `edit_text.php` beim Submit bereits weiterleitet.

```php
    public function store_text_action(string $text_id = '')
    {
        CSRFProtection::verifyRequest();
        $this->text_obj = \TextPlugin\Text::find($text_id);
        if (!$this->text_obj) {
            $this->text_obj = new \TextPlugin\Text();
            $this->text_obj->author_id = $GLOBALS['user']->id;
        }
        $this->text_obj->setData([
            'title' => Request::get('title'),
            'description' => Request::get('description'),
            'type' => Request::int('type')
        ]);

        if ($this->text_obj->store() !== false) {
            PageLayout::postSuccess($this->_('Der Text wurde erfolgreich gespeichert'));
        } else {
            PageLayout::postError($this->_('Beim Speichern des Texts ist ein Fehler aufgetreten'));
        }
        $this->redirect('overview/index');
    }
```
Die `store_text_action` speichert auch noch nicht existierende Texte,
daher wird wie in `edit_text_action` ein neues Objekt erstellt,
wenn kein existierendes gefunden wird.  
Mit `setData()` werden die Nutzereingaben dem `Text`-Objekt zugewiesen.  
Auf die Nutzereingaben können über `Request` zugegriffen werden,
wobei der angegebene Parameter mit dem html-`name` des input-elements (`<input>`, `<textarea>` etc.)
übereinstimmen muss. In [Request](Request) sind noch weitere Informationen zu `Request` erläutert.  
Eine id für den Primärschlüssel `text_id` und das `mkdate` wird von SimpleORMap für neue Einträge automatisch erstellt
und `chdate` wird automatisch aktualisiert.  
Die `author_id` wird jedoch nicht automatisch gesetzt,
sodass wir sie für neue Texte einmalig eintragen.  
Je nachdem, ob das Speichern erfolgreich ist,
geben wir auf der nächsten Seite eine Erfolgs- oder Fehlermeldung an (siehe [PageLayout](PageLayout)).  
Das Speichern eines Texts benötigt keine View,
wir möchten lediglich die Daten abspeichern und dann wieder die Übersichtsseite aufrufen,
weshalb wir mit `$this->redirect('overview/index');` auf `index_action` umleiten
und auch keine `store_text.php`-view Datei benötigen.

### Bearbeiten ermöglichen
Texte sind technisch nun bearbeitbar,
jedoch gibt es für Nutzende noch keine Möglichkeit,
die Bearbeitungsansicht für existierende Texte aufzurufen.
Dafür fügen wir der Übersichtstabelle in `overview/index.php` eine weitere Spalte `Aktion` hinzu,
in der wir Aktionen für explizite Texte ermöglichen wie bspw. eine Detailansicht, das Löschen oder halt das Bearbeiten.

In der `<colgroup>` ergänzen wir `<col style="width: 40px">`.  
Im `<thead>` fügen wir `<th data-sort="false"><?= $_('Aktion'); ?></th>` hinzu.  
Um Aktionen einzufügen, sollte das [ActionMenu](ActionMenu) genutzt werden,
welches sehr ähnlich zu `ActionsWidget` funktioniert.  
Wir erstellen also ein neues `ActionMenu` innerhalb des `<tbody>` und übergeben einen Link zur `edit_text_action`.
```php
<td>
    <? $actions = ActionMenu::get(); ?>
    <? $actions->addLink(
        $controller->url_for('overview/edit_text/' . $text_obj->id),
        $controller->_('Bearbeiten'),
        Icon::create('edit'),
        ['data-dialog' => true]
    ); ?>
    <?= $actions ?>
</td>

```
Wichtig hierbei ist, dass wir auch die id des jeweiligen `Text`-Objekt übergeben,
sodass kein neues Objekt erstellt wird, sondern das existierende bearbeitet werden kann.  
Da `ActionMenu` die Methode `__toString` zum rendern nutzt,
können wir das erstellte Menü auch einfach mit `<?= $actions ?>` rendern.

### Erlaubnis für Objekte
Was wir nun erreicht haben, ist das alle Nutzende, alle Texte bearbeiten können.
Wir sollten also sicherstellen, dass nur berechtigte Nutzer die Texte bearbeiten können.
Für ein `Text`-Objekt legen wir fest, dass nur der Ersteller und root-Nutzer das Objekt bearbeiten dürfen.
Dafür erstellen wir eine `hasPermission`-methode in `models/Text.php`, in der wir genau dies sicherstellen.

```php
public function hasPermission(): bool
{
    return $GLOBALS['user']->id === $this->author_id || $GLOBALS['user']->perms === 'root';
}
```
Mit `$GLOBALS['user']` lässt sich auf das `User`-Objekt vom aktuellen Nutzenden zugreifen,
sodass so überprüft werden kann, ob es sich um den oder die Autor*in oder einen `root`-User handelt.  
Allgemein ist es sinnvoll root-Usern alle Berechtigungen zu geben,
das erleichtert die Entwicklung in Testumgebungen, da sich nicht ständig umgeloggt werden muss,
und hilft in Produktivsystemen den System-Administrationen,
da Sie dann Feedback von Nutzenden besser nachverfolgen können.

`hasPermission` müssen wir nun überall überprüfen,
wo kritische Aktionen wie das Bearbeiten oder Löschen von Texten geschieht.
In unserem Fall ist dies in der `store_text_action` des OverviewControllers.
Wir überprüfen also, direkt nachdem wir das `Text`-Objekt geladen haben,
ob der Nutzer das Objekt speichern darf.  
Wenn keine Berechtigung vorhanden ist,
geben wir einen Fehler aus und leiten auf die Übersichtsseite um.

```php
    public function store_text_action(string $text_id = '')
    {
        CSRFProtection::verifyRequest();
        $this->text_obj = \TextPlugin\Text::find($text_id);
        if (!$this->text_obj) {
            $this->text_obj = new \TextPlugin\Text();
            $this->text_obj->author_id = $GLOBALS['user']->id;
        }
        if (!$this->text_obj->hasPermission()) {
            PageLayout::postError($this->_('Sie haben keine Berechtigung dazu, den Text anzupassen'));
            $this->redirect('overview/index');
            return;
        }
        $this->text_obj->setData([
        [...]
    }
```

Wir haben nun das notwendigste getan, um das unerlaubte Ändern von Texten zu verhindern.
Wir sollten aber auch verhindern, dass Nutzende die Bearbeitungsseite überhaupt aufrufen können,
damit Ihnen nicht suggeriert wird, dass Sie Texte bearbeiten können,
nur um Ihnen dann eine Fehlermeldung anzuzeigen.

Dafür fügen wir in der `overview/index.php` die `edit_text_action` nur hinzu,
wenn der aktuelle Nutzer die jeweilige Berechtigung hat.
Effektiv fügen wir also nur eine if-Abfrage der `hasPermission` Methode vor dem Hinzufügen des Links hinzu.

```php
    <? $actions = ActionMenu::get(); ?>
    <? if ($text_obj->hasPermission()): ?>
        <? $actions->addLink(
            $controller->url_for('overview/edit_text/' . $text_obj->id),
            $controller->_('Bearbeiten'),
            Icon::create('edit'),
            ['data-dialog' => true]
        ); ?>
    <? endif; ?>
    <?= $actions ?>
```
Da Nutzende die `edit_text_action` noch mit dem Manipulieren der Browser-URL aufrufen können,
überprüfen wir in der `edit_text_action`-methode wie in der `store_text_action`-methode die Berechtigung des Nutzers
und geben ggf. einen Fehler aus. Da wir nun verhindern, dass Nutzende auf legitimen Wege die action aufrufen,
können wir statt einen Fehler anzuzeigen auch eine Exception werfen,
sowohl in `edit_text_action`, als auch `store_text_action`.

```php
public function edit_text_action(string $text_id = '')
{
    PageLayout::setTitle($this->_('Text bearbeiten'));
    $this->text_obj = \TextPlugin\Text::find($text_id);
    if (!$this->text_obj) {
        $this->text_obj = new \TextPlugin\Text();
        $this->text_obj->author_id = $GLOBALS['user']->id;
    }
    if (!$this->text_obj->hasPermission()) {
        throw new AccessDeniedException($this->_('Sie haben keine Berechtigung dazu, den Text anzupassen'));
    }
}
```

Da wir wissen, dass für `hasPermission` die `author_id` gesetzt sein muss,
setzen wir wie in der `store_text_action` die `author_id`,
wenn wir ein neues `Text`-Objekt erstellen.  
Das heißt, dass wir eigentlich immer bevor wir `hasPermission` aufrufen,
sicher gehen sollten, dass eine `author_id` gesetzt ist.
Da dies sehr Fehleranfällig ist, sollte stattdessen festlegen werden,
dass beim Erstellen eines neuen `Text`-Objekts die `author_id` automatisch gesetzt wird.
Dies würde man am sinnvollsten im `after_initialize`-callback innerhalb der `configure()`-Methode
der `Text.php`-Modelklasse festlegen.
Wie `after_initialize`-callbacks und andere Model-spezifischen callbacks wie `after_store`, `before_delete`
programmiert werden, kann in [SimpleORMap](SimpleORMap) nachgelesen werden.

## Weitere Funktionalitäten
Die Grundfunktionalität sind nun vorhanden.
Nutzer können alle Texte erstellen, bearbeiten und einsehen.  
In diesem Abschnitt werden noch Beispiele für weitere Funktionalitäten aufgeführt und erläutert,
die bei Interesse oder Bedarf zusätzlich betrachtet werden können.

Im Folgenden eine kurze Übersicht zu den Unterkapiteln:
* `I18n` ermöglicht, dass Nutzer Texte in mehreren Sprachen eingeben können.
Die Ansicht zum Bearbeiten von Texten wird also mithilfe von `I18n` so angepasst,
dass Nutzende den Titel und die Beschreibung von Texten in mehreren Sprachen angeben können.
Die Texte werden dann in der jeweiligen Sprache angezeigt,
die die jeweiligen Nutzenden in Stud.IP eingestellt haben.
* `wysiwyg` ist ein ausführlicher Text-Editor und ermöglicht Nutzenden,
die Formatierung von Texten.
In diesem Unterabschnitt wird das Beschreibungsfeld eines Textes zu einem wysiwyg Feld umgebaut,
sodass Nutzende ihre Texte mit verschiedenen Formatierungen anpassen können.
* Wir möchten eine Detail-Seite für Texte einführen,
in der alle Nutzenden Details zu jeweiligen Texten einsehen können.
Diese Funktion wird vor allem wichtig,
wenn Texte lange Beschreibungen haben oder zukünftig mehr Attribute erhalten,
da die Tabelle auf der Übersichtsseite dann unübersichtlich wird.  
Dieser Abschnitt ist jedoch allgemein relativ simpel
und ist sicherlich auch ohne Erläuterung mit dem jetzigen Wissen selbst erstellbar.
* Auf der Übersichtsseite soll das Filtern von Texten ermöglicht werden,
indem in der Sidebar nach Titel und Beschreibung gesucht werden kann.
* Da eventuell sehr viele Texte auf der Übersichtsseite angezeigt werden könnte,
da ja schließlich alle geladen werden, soll eine Pagination hinzugefügt werden,
um immer nur eine feste Anzahl an Texten pro Seite zu laden und die Ladezeit damit zu verkürzen.

### I18n
[gettext](Howto/Internationalisierung#internationalisierung-im-php-code) wird dazu genutzt,
String die von den Entwicklern fest implementiert werden auf andere Sprachen zu übersetzen.  
I18n bietet im Gegensatz dazu den Nutzenden die Möglichkeit,
Texte die sie angeben, in verschiedenen Sprachen anzugeben.
Die Texte werden dann je nach der jeweiligen eingestellten Sprache der Nutzenden ausgegeben.

Im TextPlugin sollen Nutzende die Möglichkeit erhalten,
den Titel des Texts und die Beschreibung auch in mehreren Sprachen anzugeben.  
Wie in [I18n](Howto/Internationalisierung#i18n) sollten dafür drei Punkte beachtet werden

#### Feld als i18n_field kennzeichnen
Die jeweiligen Felder sollten als `i18n_field` gekennzeichnet werden.
Wir fügen also in der `configure()`-Methode der `Text`-Modelklasse folgendes hinzu
```php
$config['i18n_fields']['title'] = true;
$config['i18n_fields']['description'] = true;
```

#### I18n Inputs nutzen
Es müssen statt html-Input-Elementen entsprechende I18n-Input-Elemente genutzt werden.  
Wir passen also das `<input>` und das `<textarea>`-Element in der `edit_text.php`-view dementsprechend an.
```php
<div>
    <label class="required">
        <?= $_('Titel') ?>
    </label>
    <?= I18N::textarea('title', $text_obj->title, ['required' => true]) ?>
</div>

<div>
    <label>
        <?= $_('Beschreibung') ?>
    </label>
    <?= I18N::textarea('description', $text_obj->description) ?>
</div>
```
#### Request::i18n
Für `I18n`-Felder muss `Request::i18n()` zum Erhalten der jeweiligen form-Daten ausgeführt werden.  
In unserem Plugin betrifft das nur die `store_text_action` im `OverviewController`.
```php
$this->text_obj->setData([
    'title' => Request::i18n('title'),
    'description' => Request::i18n('description'),
    'type' => Request::int('type')
]);
```

### wysiwyg
Nutzende sollen außerdem verschiedene Formatierungsoptionen für die Beschreibung eines Texts erhalten.
In Stud.IP wird dies mithilfe des [Wysiwyg](Wysiwyg)-Editors erreicht.
Da Nutzende diesen aber in Stud.IP auch abstellen können, bieten wir alternativ auch eine simplere toolbar an.

Dazu geben wir in die `<textarea>` der Beschreibung in `edit_text.php` als html-class `wysiwyg add_toolbar` an.
```php
<?= I18N::textarea('description', $text_obj->description, ['class' => 'wysiwyg add_toolbar']) ?>
```
bzw. ohne i18n-feld
```html
<textarea name="description" class="wysiwyg add_toolbar"><?= $text_obj->description ?></textarea>
```

Wenn die Beschreibung ausgegeben wird, sollte daran gedacht werden `formatReady()` statt `htmlReady()` zu verwenden.

### Detail-Ansicht
Als weiteres Beispiel einer action mit view wird eine Detail-Ansicht für Texte hinzugefügt,
in der deutlich mehr Informationen angezeigt werden können, als in der Übersichtstabelle.

Dazu fügen wir in das `ActionMenu` der `index.php` einen weiten Link zu der neuen `view_text_action` hinzu.
```php
<? $actions = ActionMenu::get(); ?>
<? $actions->addLink(
    $controller->url_for('overview/view_text/' . $text_obj->id),
    $controller->_('Ansehen'),
    Icon::create('log'),
    ['data-dialog' => true]
); ?>
<? if ($text_obj->hasPermission()): ?>
    [...]
<? endif; ?>
<?= $actions ?>
```
Da erstmal alle Nutzenden die Detailansicht aufrufen können, fragen wir hier nicht `hasPermission` ab.  
In den `OverviewController` erstellen wir dann die `view_text_action`.
```php
public function view_text_action(string $text_id)
{
    PageLayout::setTitle($this->_('Text ansehen'));
    $this->text_obj = \TextPlugin\Text::find($text_id);
    if (!$this->text_obj) {
        throw new InvalidArgumentException($this->_('Der angefragte Text konnte nicht gefunden werden'));
    }
}
```

Schließlich erstellen wir noch die `view_text`-view, in der einfach alle gewünschten Attributen ausgegeben werden.
```html
<table class="default">
    <colgroup>
        <col style="width: 40%">
    </colgroup>
    <tbody>
    <tr>
        <td><strong><?= $_('Titel') ?></strong></td>
        <td><?= htmlReady($text_obj->title) ?></td>
    </tr>
    <tr>
        <td><strong><?= $_('Beschreibung') ?></strong></td>
        <td><?= formatReady($text_obj->description) ?></td>
    </tr>
    <tr>
        <td><strong><?= $_('Typ') ?></strong></td>
        <td><?= $text_obj->getTypeDescription() ?></td>
    </tr>
    <tr>
        <td><strong><?= $_('Autor') ?></strong></td>
        <td><?= htmlReady($text_obj->author->getFullname()) ?></td>
    </tr>
    <tr>
        <td><strong><?= $_('Erstellt am') ?></strong></td>
        <td><?= strftime('%x', $text_obj->mkdate) ?></td>
    </tr>
    <tr>
        <td><strong><?= $_('Letzte Änderung am') ?></strong></td>
        <td><?= strftime('%x', $text_obj->chdate) ?></td>
    </tr>
    </tbody>
</table>
```
Falls für die Beschreibung der `wysywig`-Editor zur Verfügung gestellt wurde,
sollte hier darauf geachtet werden `formatReady()` statt `htmlReady()` zu nutzen.

### Texte suchen und filtern
[TODO]

### Pagination
[TODO]

## Zusammenfassung
In diesem Teil wurde
* Eine Übersichtsseite für alle Texte mit entsprechender action und view erstellt ([Trails](Trails))
* Ein Sidebar-Widget erstellt ([Sidebar](Sidebar))
* Eine action und view zum Bearbeiten und eine action zum Speichern der form-Daten erstellt 

Der komplette bisher erstellte Code zuzüglich phpDoc ist hier ([TextPlugin.zip](../assets/0e34a84cb91701dda04150e39bd3067f/TextPlugin.zip)) als ZIP-Datei verfügbar.
