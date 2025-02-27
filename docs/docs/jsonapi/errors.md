---
title: Fehler
---


Die Stud.IP-JSON:API verwendet die Fehler-Codes, die auch in der <a
href="http://jsonapi.org/format">JSON-API-Spezifikation</a> verwendet
werden.


Fehler Code | Bedeutung
---------- | -------
401 | Unauthorized – Sie haben nicht die erforderliche Berechtigung.
403 | Forbidden – Die gewünschte Operation steht nicht zur Verfügung.
404 | Not Found – Die gewünschte Ressource oder Relation konnte nicht gefunden werden.
409 | Conflict  – Beim Anlegen oder Ändern von Ressourcen oder Relationen werden Beschränkungen im Stud.IP verletzt. Beispiel: Eine Ressource falschen Typs soll einer Relation hinzugefügt werden.
500 | Internal Server Error – Es gibt ein Problem auf dem Server. Versuchen Sie es später erneut!
