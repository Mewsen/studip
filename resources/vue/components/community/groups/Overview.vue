<template>
    <h2>Übersicht Gemeinschaften</h2>
    <studip-data-set-viewer
        :data="myGroups"
        :availableViews="['card', 'list', 'table']"
        title-key="groupName"
    >

    </studip-data-set-viewer>
</template>
<script setup>
import StudipDataSetViewer from '@/vue/components/data-set-viewer/StudipDataSetViewer.vue';

const myGroups =  [
  {
    groupId: 101,
    groupName: 'JS-Frameworks Expertenrunde',
    description: 'Austausch und Deep Dives zu aktuellen Trends in Vue, React und Svelte.',
    membersCount: 12,
    topic: 'Webentwicklung & UI/UX',
    lastActivity: '2025-12-08',
    isPublic: true,
    keywords: ['Vue3', 'React', 'Frontend', 'TypeScript']
  },
  {
    groupId: 102,
    groupName: 'Stud.IP Customizing & API',
    description: 'Für Entwickler, die eigene Plugins und Erweiterungen für Stud.IP erstellen.',
    membersCount: 4,
    topic: 'Stud.IP Entwicklung',
    lastActivity: '2025-11-20',
    isPublic: false, // Private Gruppe
    keywords: ['PHP', 'Backend', 'API', 'Plugin-Entwicklung']
  },
  {
    groupId: 103,
    groupName: 'Masterarbeits-Supportgruppe',
    description: 'Regelmäßige Treffen zur gegenseitigen Motivation und Korrektur von Masterarbeiten.',
    membersCount: 8,
    topic: 'Forschung & Abschlussarbeiten',
    lastActivity: '2025-12-09',
    isPublic: true,
    keywords: ['Wissenschaft', 'Methodik', 'Kolloquium']
  },
  {
    groupId: 104,
    groupName: 'KI und Machine Learning Basics',
    description: 'Einführung in Python, TensorFlow und die Grundlagen neuronaler Netze.',
    membersCount: 25,
    topic: 'Informatik & Data Science',
    lastActivity: '2025-12-07',
    isPublic: true,
    keywords: ['Python', 'TensorFlow', 'Neuronale Netze']
  }
];;

</script>
