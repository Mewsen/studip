---
title: VUEX
slug: /vuejs/vuex
sidebar_label: VUEX
---

Natürlich wollen wir die Stud.IP-JSONAPI auch gerne in Vue.js verwenden. Mit den bekannten Tools wie XMLHttpRequest und fetch bzw. mit Wrapper-Bibliotheken wie axios ist das einfach möglich. Können wir das aber noch ein bisschen praktisch nutzbarer gestalten?

Wichtig in dem Zusammenhang ist, dass Vue.js ein komponentenbasiertes Framework ist. Das generierte HTML entsteht aus mehrfach verwendbaren Komponenten, die letztlich einen Komponentenbaum bilden. 

### State-Management mit vuex

Ein häufig wiederkehrendes Problem in komponentenbasierten Web-Frameworks ist die Frage des State-Managements. Wie gestalte ich den Zugriff auf den State, die Daten, meiner Applikation. Die üblichen Ansätze, die in Vue.js verwendet werden, ist die Weitergabe von relevanten Daten von Elternkomponenten an ihre Kindkomponenten.

Ein Beispiel:

* Zwei Komponentenknoten benötigen dieselben Daten.
* Sie sind aber nur sehr weit entfernt miteinander verwandt, hängen also z.B. jeweils tief in zwei unterschiedlichen Hauptästen

Das führt in der Regel dazu, dass die Daten dann vom nächsten gemeinsamen Verwandten bereitgestellt und dann über die komplette Verwandtschaftslinien durchgereicht werden müssen, obwohl die dazwischen liegenden Knoten nichts mit diesen Daten zu tun haben.

An dieser Stelle hakt `vuex` ein und bietet einen zentralen Store an, auf den **alle** Komponenten zugreifen können.

Wenn wir Daten aus der JSONAPI auslesen, lohnt es sich also, diese in den Store einzuspeisen, damit es unter anderem klar definierte Punkte gibt, an denen auf die JSONAPI zugegriffen wird, um performanzproblematische Doppelungen zu verhindern.

**Wir wollen also unbedingt eine Kombination von JSONAPI und vuex haben.**

### `reststate-vuex`

Zum Glück gibt es schon ein paar Bibliotheken, um JSONAPI und `vuex` zu verknüpfen. Für die Implementation der *Courseware 5* haben wir uns für eine Bibliothek entschieden, die wir um weitere Möglichkeiten erweitert haben und gegenwärtig vom ELAN e.V. gepflegt wird:

https://github.com/elan-ev/reststate-vuex

### Setup

Das Setup, um `reststate-vuex` zu nutzen, konfiguriert eine `axios`-Instanz mit unserer JSONAPI-Schnittstelle. Diesen Code sehen wir ja in der Regel nur sehr selten. 

```javascript
const getHttpClient = () =>
    axios.create({
        baseURL: STUDIP.URLHelper.getURL(`jsonapi.php/v1`, {}, true),
        headers: {
            'Content-Type': 'application/vnd.api+json',
        },
    });

// []

const store = new Vuex.Store({
    modules: {
        courseware: CoursewareModule,
        ...mapResourceModules({
            names: [
                'courses',
                'courseware-blocks',
                'courseware-block-comments',
                'courseware-containers',
                'courseware-instances',
                'courseware-structural-elements',
                'courseware-user-data-fields',
                'courseware-user-progresses',
                'files',
                'file-refs',
                'folders',
                'users',
            ],
            httpClient,
        }),
    },
});
```

Im obigen Code machen wir u.a. die JSONAPI-Schemata "courses", "users", "files" usw. bekannt. Was können wir jetzt damit anstellen?

### Auslesen

Vielleicht wollen wir gerne alle Nutzer auslesen und in einer Vue.js-Komponente ausgeben:

```php
<template>
<div>
  <ul>
    <li v-for="user in allUsers" :key="user.id">
      {{ user.attributes['formatted-name'] }}
    </li>
  </ul>
</div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';

export default {
    name: 'users-list',
    mounted() {
        this.loadAllUsers();
    },
    methods: {
        ...mapActions({
            loadAllUsers: 'users/loadAll',
        }),
    },
    computed: {
        ...mapGetters({
            allUsers: 'users/all',
        }),
    },
};
</script>
```

 Das verschafft schon einen guten Überblick, was uns `reststate-vuex` bieten kann. Hier die wichtigsten Punkte:

* Wir verwenden wie gewohnt `mapActions` und `mapGetters`, um Actions und Getter von `reststate-vuex` aufzurufen.
* Die `loadAll`-Action lädt alle Daten des jeweiligen JSONAPI-Schemas und speichert diese im Store.
* Wir verwenden den `all`-Getter, um alle Ressourcen eines JSONAPI-Schemas aus dem Store zu erhalten.
* Der Zugriff auf eine `user`-Ressource erfolgt dann erwartungsgemäß mit `user.id` oder im `user.attributes`-Objekts.

Einzelne Ressourcen können über die Action `users/loadById` aus dem JSONAPI-Backend in den Store geladen und über den Getter `users/byId` aus dem Store geholt werden. 

### Ladefortschritt und Fehler

Damit wir nicht immer wieder Ladefortschritt- und Fehlerbehandlung implementieren müssen, sind nur wenige Änderungen notwendig. Zunächst ergänzen wir die vorhandenen Getter: 

```javascript
...mapGetters({
+      isLoading: 'users/isLoading',
+      isError: 'users/isError',
       allUsers: 'users/all',
     })
```

und können dann auf diese im Template zugreifen: 

```php
<template>
   <div>
-    <ul>
+    <p v-if="isLoading" v-translate>Laden...</p>
+    <p v-else-if="isError" v-translate>Fehler beim Laden.</p>
+    <ul v-else>
       <li
         v-for="user in allUsers"
```

### Anlegen von Ressourcen

Mit `reststate-vuex` können wir:

* Ressourcen im JSONAPI-Backend via `axios` anlegen
* und diese natürlich auch im `vuex` Store zugänglich machen

Dazu ergänzen wir nur eine Action. Hier ein Beispiel für die Courseware, um Blöcke zu erstellen: 

```javascript
methods: {
     ...mapActions({
+      createBlock: 'courseware-blocks/create',
     }),
```

Diese Action können wir jetzt im JavaScript-Code verwenden:

```javascript
// Der `container` stammt aus dem Store, aber das geht auch per Hand.
const container = { type: 'courseware-containers', id: '17' };

// Wir erstellen eine JSON-Repräsentation eines Courseware-Blocks:
//   - mit einem Beispiel-Block-Type.
//   - ohne `payload`
//   - mit Verknüpfung zu einem Courseware-Container
const block = {
    attributes: {
        'block-type': 'text',
        'payload': null,
    },
    relationships: {
        container: {
            data: { type: container.type, id: container.id },
        },
    },
};

this.createBlock('courseware-blocks/create', block);
```


### Löschen von Ressourcen

Um Ressourcen im JSONAPI-Backend und im `vuex`-Store zu löschen, verwenden wir einfach die entsprechende Action:

```javascript
methods: {
     ...mapActions({
+      deleteBlock: 'courseware-blocks/delete',
     }),
```

und verwenden diese Action dann einfach in unsere Vue.js-Komponente:

```php
+ <button @click="deleteBlock(block)" v-translate>
+   Block löschen
+ </button>
```

### Ändern von Ressourcen

Auch für das Ändern von Ressourcen im JSONAPI-Backend und im `vuex`-Store gibt es eine entsprechende Action:

```javascript
methods: {
     ...mapActions({
+      updateBlock: 'courseware-blocks/update',
     }),
```

und verwenden diese Action im JavaScript-Code

```javascript
const block = this.getBlock({ id: '17' });
block.attributes.payload = { foo: 'bar' };
this.updateBlock(block);
```

Da der `vuex` Store reaktiv ist, ändern sich dadurch wie gewohnt alle Komponenten, die mit dieser Ressource zusammenarbeiten und natürlich auch im JSONAPI-Backend.

### Mehr

Die `reststate-vuex`-Bibliothek bietet noch viele andere Möglichkeiten, die von der JSONAPI angeboten werden,  gut dokumentiert unter https://vuex.reststate.codingitwrong.com/
