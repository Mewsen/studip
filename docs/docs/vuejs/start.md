---
title: Stud.IP VUEJS
slug: /vuejs/
sidebar_label: Einführung
---

Seit Stud.IP 4.5 ist [Vue.js](https://vuejs.org/) in Stud.IP verfügbar und wird für einige Komponenten genutzt.


** `STUDIP.Vue`**

Mit der Version 5.0 von Stud.IP soll die Nutzung von Vue.js weiter vorangetrieben werden. Dafür werden über das Objekt `STUDIP.Vue` in Javascript Methoden bereitgestellt, die die Integration erleichtern sollen, indem manche Dinge abstrahiert bzw. generalisiert werden.

Vue.js wird nun als Chunk geladen und ist somit nicht mehr sofort auf jeder Seite verfügbar. Alle über `STUDIP.Vue` verfügbaren Methoden kümmern sich um das Laden und sind daher der präfertierte Weg, Vue.js zu nutzen.

** `STUDIP.Vue.load()` **

Über diese Methode wird Vue.js geladen und über ein Promise zurückgegeben.

```Javascript
STUDIP.Vue.load().then(({Vue, createApp, eventBus, store}) => {
    // ...
});
```

** `Vue` **

Das verwendete `Vue`-Objekt kann dazu genutzt werden, um Komponenten oder Direktiven global zu registrieren.

** `createApp(options)`**

Diese Methode lädt den Vue-Chunk und erstellt eine App. Die Parameter sind wie folgt:

| Parameter | Beschreibung |
| ---- | ---- |
|`options`|Optionen wie `data`, `methods` oder `computed`|

Anschliessend kann diese App mittels `app.$mount(element)` auf das gewünschte Element gemountet werden.

Diese Abstraktion ist trivial, aber soll die Erstellung einer App kapseln, falls sich die API von Vue.js ändert und einen leichten Einstieg bieten.

** `eventBus` **

Der `eventBus` kann genutzt werden, um global Events abzusetzen bzw. auf diese zu horchen.

** `store` **

Der `store` ist eine [vuex](https://vuex.vuejs.org/guide/)-Instanz, die für die Datenhaltung zuständig ist.

Weitere Details hierzu werden folgen...

** `STUDIP.Vue.emit(eventName, ...args)` und `STUDIP.Vue.on(eventName, ...args)` **

Über diese beiden Methoden können Nachrichten und Daten zwischen Vue-Komponenten bzw. dem umliegenden System und Vue-Apps ausgetauscht werden. Intern wird ein Event-Bus realisiert, der über ein globales Mixin in jeder Vue-Komponente über die Methoden `globalEmit(eventName, ...args)` bzw. `globalOn(eventName, ...args)` bereitgestellt werden.

** `[data-vue-app]` **

Über das HTML-Attribut `[data-vue-app]` kann ohne weitere Initialisierung eine Vue-App erstellt werden. Über den Inhalt des Attributs können noch weitere Angaben zu den Daten oder verwendeten Komponenten gemacht werden, indem ein entsprechendes Objekt übergeben wird. Mögliche Optionen:

| Parameter | Beschreibung |
| ---- | ---- |
|`id`|Die Id der App. Ist diese gesetzt und es gibt ein Objekt `STUDIP.AppData`, welches unter der übergebenen Id Daten bereithält, so werden diese Daten der App übergeben |
|`components`|Die von der App verwendeten Komponenten als Array.|
