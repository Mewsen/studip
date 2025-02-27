---
title: QRCodes erzeugen
---

Bei den Fragebögen gibt es ein nettes, kleines Feature: Man kann auf ein QR-Code-Icon klicken und bekommt im Vollbildmodus einen QR-Code des Links zum Fragebogen angezeigt.

Auch Plugins oder andere Stellen in Stud.IP können relativ einfach einen QR-Code auf diese Weise erzeugen. Hat man einen `<a>`-Link, so muss man ihn nur mit dem Tag `data-qr-code` anreichern.

```html
<a href="http://localhost/studip_trunk/dispatch.php/questionnaire/answer/c9b030df1bd556c8383dc56259d0f9c3?cid=c0fd14b93d003f35bed648d2056346aa" 
   data-cr-code="Bitte nehmen Sie schnell teil.">
    Link zur Umfrage
</a>
```

Der Text in dem data-qr-code Tag ist optional und bietet eine Beschreibung, die unter dem erzeugten QR-Code angezeigt wird.
