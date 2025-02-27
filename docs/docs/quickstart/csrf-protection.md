---
id: csrf-protection
title: CSRFProtection - Schutz vor Cross-Site-Request-Forgeries
sidebar_label: CSRFProtection
---

CSRF (oder auch XSRF) steht für "Cross-Site Request Forgery"also in etwa seitenübergreifende Aufrufmanipulation. Diese Angriffsmethode funktioniert so, dass dem Nutzer schadhafter Code oder ein Link untergeschoben wird, der dann mit den Rechten des Nutzers eigentlich unauthorisierte Befehle ausführt (wie zum Beispiel das Absenden von kompromittierenden Nachrichten).

Einfache Beispiele lassen sich zum Beispiel bei [Wikipedia](http://de.wikipedia.org/wiki/CSRF#Beispiele) finden.

Vor zwei Jahren hatte data-quest einen entsprechenden Anlauf getätigt, der letztlich u.a. im Einbau des Bilder-Proxys endete. Gefährliche Aktionen können seitdem mit Stud.IP-Tickets abgesichert werden. Derzeit wird #check_ticket in 14 Dateien verwendet (wie zB den Studiengruppen, der Administration von Plugins und ihren Rollen, dem Gästebuch).

Augenscheinlich gibt es damit aber noch einige Stellen, die ebenfalls abgesichert werden müssten. Die derzeit verwendete Lösung müsste konsequenterweise an jeder entsprechenden Stelle auch tatsächlich eingesetzt werden. Außerdem bestehen Probleme bei der Verwendung mehrerer Tabs, da die gegenwärtige Lösung lediglich ein valides Ticket kennt.

Dieser vorgeschlagene StEP möchte CSRF/XSRF verhindern, indem jeder "gefährliche" Webrequest geprüft wird. Tim Berners-Lee [Axiomen](http://www.w3.org/DesignIssues/Axioms.html) folgend, werden alle Anfragen in seiteneffektfreie und -auslösende Unterschieden. Der Aufruf des Gästebuchs sollte seiteneffektfrei sein. Das Absenden eines Eintrags an das Gästebuch löst Seiteneffekte (naiv: Datenbankänderungen) aus. 

Vereinfacht sollen laut Berners-Lee seiteneffektfreie Request über GET und die übrigen mit POST gesendet werden.

Dieser StEP ergänzt jeden POST-Request um einen weiteren Wert, indem jedes Formular einen versteckten Parameter mitliefert. Der Server prüft beim Eintreffen eines POST-Request, ob der versteckte Parameter enthalten und valide ist. Ist das nicht der Fall, wird die Anfrage abgewiesen.

Dieser versteckte Parameter ist während der Sitzung immer derselbe. Damit ergeben sich keine Probleme mit der Persistenz und Invalidierung, wie das in der derzeitigen Lösung geschieht. Die Verwendung mehrere Tabs ist daher völlig unproblematisch.

Außerdem wird automatisch jeder Request aus zurkünftigem Code abgesichert, solange die Entwickler sich an die Semantik der HTTP-Verben halten (GET/POST)was allerdings bei Formularen als gegeben angenommen werden sollte.

#### Funktionsweise

Um Stud.IP vor gefälschten Request zu schützen, muss nun jeder POST-Request (aber nicht Ajax) einen zusätzlichen Parameter "security_token" mitschicken, dessen Wert mit einem in der Session befindlichen verglichen wird. Um genau zu sein, wird für jeden Nutzer zu Beginn seiner Session ein 256-Bit-Token erzeugt und in der $_SESSION abgelegt. Jedes(!) Stud.IP-POST-Formular wurde um ein [=input```phptype=hidden]-Element=] bereichert, dass diesen Token mitschickt. Sobald ein Request bei Stud.IP eintrifft, wird überprüft:

* ob es ein GET-Request ist, um dann die weitere Überprüfung abzubrechen
* ob es ein XHR ist (also Ajax mit jQuery oder prototype), um dann die weitere Überprüfung abzubrechen
* ob der mitgeschickte Parameter "security_token" existiert und mit dem Token aus der Session übereinstimmt

Diese Überprüfung findet automatisch am Ende von #page_open statt, in der Annahme, dass dann die notwendige Session existiert.

Fällt die Überprüfung negativ auf, wird ein Fehler (Status 403) gemeldet und die weitere Bearbeitung abgebrochen.


#### Anwendung

Zunächst ein Link zur API-Dokumentation [http://hilfe.studip.de/api/class_c_s_r_f_protection.html](http://hilfe.studip.de/api/class_c_s_r_f_protection.html)

Zukünftige Entwicklungen müssen beachten, dass form-Elemente, deren "method"-Attribut den Wert POST hat, ein weiteres, verstecktes input-Element benötigen, dessen Name "security_token" und dessen Wert dem Token aus der Session entspricht. Am einfachsten macht man es so:

```php
<form method="POST" ... >
<?= CSRFProtection::tokenTag() ?> 
...
</form>
```

**Ganz wichtig:** Diese Methode darf '+NICHT+' aufgerufen werden, wenn es sich um ein GET-Formular handelt, da dann der Token in die URL wandert und damit über den Referer-Header an Dritt-Seiten übertragen wird. In diesem Fall wird der Schutz unwirksam.

#### Schwierigkeiten

Der versteckte Parameter darf niemals in eine URL gelangen, da dies alle Bemühungen über den Haufen werfen würde.
Gerät der Token in die Hände eines Angreifers, kann dieser wieder beliebige Anfragen stellen.

Die vorhandenen Tickets, die über GET-Requests versendet werden, müssen ummodelliert werden. Bisher ist dort die Umstellung noch nicht erfolgt

Wenn ein seiteneffektbehafteter Request laut Stud.IP-Code eigentlich per POST verschickt werden soll, ein Angreifer aber kurzerhand einen GET-Link platziert, benötigt der Request keinen Sicherheits-Token, da automatisch lediglich bei POST-Requests überprüft wird. Daraus ergeben sich folgende Konsequenzen:

Formulare **müssen** trotzdem wie oben beschrieben behandelt werden.
Die Auswertung muss für jede Routine, die Seiteneffekte auslöst, händisch ausgewertet werden. D.h. dass also alle Stellen im Code identifiziert und um die Auswertung ergänzt werden müssen.
Vorbereitend wurde die Klasse CSRFProtection um die Methode [#verifyUnsafeRequest](http://hilfe.studip.de/api/class_c_s_r_f_protection.html#a5b6301200e525d59e4cc63e5ea36d6d3) ergänzt. Wenn man eine Stelle im Code gefunden hat, die eigentlich per POST (genauer: alle außer GET oder HEAD) einen Seiteneffekt bewirkt, muss dort folgender Code eingefügt werden:

```php
  CSRFProtection::verifyUnsafeRequest();
```


  Der Aufruf überprüft, dass:

* %alpha% es sich um einen unsicheren Request handelt (gemäß RFC 2616)
* %alpha% dieser Request das Sicherheits-Token trägt
* %alpha% das Token mit dem in der Session befindlichen übereinstimmt


Ist das nicht der Fall gibt es eine MethodNotAllowed-Exception, falls der Request nicht unsicher ist, 
oder eine InvalidSecurityTokenException, falls das Token nicht übereinstimmt.
