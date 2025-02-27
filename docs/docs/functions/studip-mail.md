---
id: studip-mail
title: Stud.IP-Mail
sidebar_label: Stud.IP-Mail
---

Diese Klasse [StudipMail](https://gitlab.studip.de/studip/studip/-/blob/main/lib/classes/StudipMail.class.php) bietet Möglichkeiten eine Email zu erzeugen und zu versenden. Der Versand der Nachricht wird nicht direkt von der Klasse vorgenommen, sondern von einer Instanz einer weiteren Klasse für den Emailtransport. Die dazu benutzen Klassen befinden sich in `vendor/email_message`, hier gibt es verschiedene Möglichkeiten für den Transport z.B. über die php Funktion Mail oder über smtp.


## schnelles Beispiel

```php
$mail = new StudipMail(); 
$mail->addRecipient('suchi@data-quest.de', 'Stefan Suchi') 
     ->addRecipient('elmar.ludwig@uos.de', 'Elmar Ludwig', 'Cc') 
     ->setSubject('Test der Mail Klasse') 
     ->addStudipAttachment($dokument_id) 
     ->setBodyText("Hallo,\nBlablubb") 
     ->send();
```

## Mail Transport
Welcher Transport für die Mail benutzt wird, kann ich der `config/config_local.inc.php` bei den Mail-Einstellungen mit $MAIL_TRANSPORT konfiguriert werden. Hier stehen zur Auswahl:

| Art | Beschreibung |
| ---- | ---- |
| `smtp` | direkte smtp Verbindung zu `$MAIL_HOST_NAME` |
| `php` | php `mail()` Funktion wird genutzt |
| `sendmail` | direktes Aufrufen des lokalen sendmail skriptes |
| `qmail` | direktes Aufrufen des lokalen qmail skriptes |
| `debug` | mails werden nicht verschickt sondern in eine Datei in `$TMP_PATH` geschrieben |
| `blackhole` | mails werden nicht verschickt, sondern einfach verworfen |

Aufgrund dieser Einstellung wird automatisch in `lib/phplib_local.inc.php` eine Instanz der entsprechenden Transporterklasse erzeugt und mit `StudipMail::setDefaultTransporter($mail_transporter)` als Standardweg zum verschicken hinterlegt.

Es ist aber möglich dieses Objekt auszutauschen, entweder über den obigen Aufruf von `StudipMail::setDefaultTransporter()` oder aber beim Aufruf von StudipMail::send(), da die `send()` Methode als optionalen Parameter ein von `email_message_class` abgeleitetets Objekt akzeptiert. 

Damit ist es auch möglich den automatischen Versand von Mails temporär zu verhindern, wie es z.B. die Klasse `UserManagement` bei Änderungen eines Nutzers macht:

```php
require_once 'vendor/email_message/blackhole_message.php';
$umanager = new UserManagement();
$suck_it_down = new blackhole_message_class();
$default_mailer = StudipMail::getDefaultTransporter();
StudipMail::setDefaultTransporter($suck_it_down);
$umanager->createNewUser($data));
StudipMail::setDefaultTransporter($default_mailer);
```

## Mails erstellen
Um eine Mail zu erstellen, erzeugt man ein neues `StudipMail` Objekt und füllt es über die diversen `add` und `set` Methoden mit Inhalt. Alle `add` und `set` Methoden liefern immer das aktuelle Objekt zurück, damit man mehrere Aufrufe hintereinandersetzen kann ("fluent interface"). Die wesentlichen Methoden sind:

| Funktion | Beschreibung |
| ------ | ------ |
| `setSenderEmail($mail)` | setzt die Mailadresse des Absenders. Die Absendeadresse wird im Konstruktor auf `$MAIL_ENV_FROM` vorbelegt. |
| `setSenderName($name)` | setzt den Namen des Absenders, wird auf `$MAIL_FROM` vorbelegt |
| `setReplyToEmail($mail)` | setzt die Mailadresse für das reply-to, wird mit `$MAIL_ABUSE` vorbelegt. |
| `setSubject($subject)` | setzt den Titel der Mail |
| `addRecipient($mail, $name = *, $type = 'To')` | fügt einen Empfänger hinzu. Der erste Parameter muss die Mailadresse enthalten, der nächste enthält optional den Namen. Mit dem dritten Parameter kann man einstellen ob es sich um einen Standardempfänger ('To'), einen Kopieempfänger ('Cc') oder einen Blindkopieempfänger ('Bcc') handelt. |
| `addDataAttachment($data, $name, $type = 'automatic/name', $disposition = 'attachment')` | fügt einen Datenanhang der Mail hinzu. Über den $type Parameter sollte man eine korrekten mime-type mit angeben |
| `addFileAttachment($file_name, $name = *, $type = 'automatic/name', $disposition = 'attachment')`| fügt einen Dateianhang der Mail hinzu. Über den `$type` Parameter kann man einen korrekten mime-type mit angeben, ansonsten wird versucht auf Basis des Dateinamens zu entscheiden. $file_name muss den kompletten Pfad zur Datei enthalten. |
|`addStudipAttachment($dokument_id)` | fügt einen Dateianhang der Mail hinzu, es muss nur die Stud.IP Dokumenten ID übergeben werden, die anderen Parameter werden dann direkt aus der Datenbank befüllt.  |
| `setBodyText($body)` | setzt den Textinhalt der Mail |
| `setBodyHtml($body)` | setzt den HTML Inhalt der Mail. Es wird dann immer eine multipart Mail erzeugt, wenn der Textinhalt fehlt wird dafür ein Hilfstext eingefügt.|
| `send(email_message_class $transporter = null)` | versendet die Mail, mit dem optionalem Parameter kann das transport Objekt vorgegeben werden. Die Methode liefert true zurück wenn die Mail verschickt werden konnte. |
