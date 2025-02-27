---
id: pdf-exports
title: PDF-Exporte
sidebar_title: PDF-Exporte
---

In `lib/classes/exportdocuments/ExportPDF.class.php` liegt eine Klasse, mit der es einfach ist, aus in Stud.IP gängigem Text, der eventuell auch mit Stud.IP-Formatierung versehen ist, in ein PDF zu exportieren. Im Klartext heißt das: man hat einen Text wie den Inhalt eines Wikis und will daraus eine PDF bekommen, in der die Tabellen und Grafiken korrekt dartgestellt werden. Die Klasse ExportPDF erbt ihre schöne Einfachheit von der Klasse TCPDF, die noch über einige andere schicke Dinge verfügt. Das bedeutet, dass $doc auch alle Methoden von TCPDF benutzen kann.

Durch die direkte Ausgabe des PDF-Dokumentes wird jede andere Ausgabe natürlich ignoriert, die vorher oder nachher geschrieben worden ist. Sobald dispatch aufgerufen wird, wird nur noch das PDF übermittelt (mitsamt MIME-Type). Der Parameter von disptach ist überdies der Dateiname ohne das ".pdf", was automatisch angefügt wird.

## Generierung

### Aus PHP

Der Code zum Erzeugen eines PDF-Dokumentes aus Stud.IP Code besteht aus folgenden vier Zeilen:

```php
<?php
$doc = new ExportPDF();
$doc->addPage();
$doc->addContent('Hallo, %%wir%% benutzen :studip: -Formatierung.');
$doc->dispatch("test_pdf");
```

Zuerst muss man ein Dokument-Objekt initialisieren. In unserem Fall ist das `$doc`. Danach fängt man eine neue Seite an mit `$doc->addPage()` und befüllt diese Seite mittels der Methode `addContent` mit Inhalten, welche Stud.IP-Formatierungsangaben beinhalten müssen. In der letzten Zeile wird dieses Dokument an den Aufrufer der Seite ausgegeben.

### HTML-Template verwenden

Hat man ein Template, welches HTML-Code generiert, so kann dieser HTML-Code direkt an ExportPDF übergeben werden, um daraus ein HTML-Dokument zu bauen. Der Code zum Rendern des HTML-Codes und dessen Umwandlung in ein PDF-Dokument kann folgendermaßen aussehen:

```php
<?php
$templateFactory = new Flexi_TemplateFactory(__DIR__ . '/../templates/');
$template = $templateFactory->open('pdf_doc.php');
$template->set_attribute('attr1', $attr1);
$template->set_attribute('plugin', $this->plugin);

$htmlCode = $template->render(); //$htmlCode beinhaltet den gerenderten HTML-Code

//Konvertierung in ein PDF-Dokument:

$pdfdoc = new ExportPDF();
$pdfdoc->addPage();
$pdfdoc->writeHTML($htmlCode);

//PDF senden:

$pdfdoc->dispatch('pdf_doc');
```

Anstatt der Methode `addContent` wird `writeHTML` aufgerufen. Diesem wird zuvor gerenderter HTML-Code, welcher unter Zuhilfenahme eines Templates erzeugt wurde, übergeben.


### In Dateibereich speichern

Das via ExportPDF erzeugte PDF kann direkt im Dateibereich von Stud.IP abgespeichert werden. Der Code dazu ist fast identisch wie in obigen Fällen:

```php
<?php
$doc = new ExportPDF();
$doc->addPage();
$doc->addContent('Hallo, %%wir%% benutzen :studip: -Formatierung.');
$studip_dokument = $doc->save("test_pdf", $folder_id);
```

Nur die letzte Zeile wurde ausgewechselt. Die Methode `save` speichert das Dokument und liefert ein Objekt vom Typ StudipDocument zurück. Man kann mittels dieses Objekte mit `$studip_dokument->getId()` die md5-ID des Dokumentes bekommen. Gibt man zudem eine folder_id beim Aufruf von `save` an, so wird die Datei auch direkt in einen Dateibereich eingefügt und man muss sich um nichts mehr kümmern.


## Weiterführende Themen

* Dokumentation von TCPDF: [http://www.tcpdf.org/docs.php](http://www.tcpdf.org/docs.php).
