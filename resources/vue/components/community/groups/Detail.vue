<template>
    <div class="studip-community-group-detail">
        <h2>Basis-Layout für Gruppe #{{ $route.params.id }}</h2>
        
        <nav class="group-tabs">
            <router-link :to="{ name: 'GroupMembers', params: { id: $route.params.id } }">Übersicht/Mitglieder</router-link>
            <router-link :to="{ name: 'GroupChat', params: { id: $route.params.id } }">Chatraum</router-link>
            <router-link :to="{ name: 'GroupPinboard', params: { id: $route.params.id } }">Pinnwand</router-link>
            <router-link :to="{ name: 'GroupsOverview' }">Zurück zur Index-Liste</router-link>
        </nav>
        
        <hr>

        <router-view />
    </div>
</template>
<script setup></script>
