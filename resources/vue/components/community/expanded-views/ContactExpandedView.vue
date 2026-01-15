<template>
    <div class="contact-card">
        <div class="contact-card-avatar">
            <img v-bind:src="contact?.meta?.avatar?.medium" alt="User avatar" >
            <p><a href="">{{ $gettext('vCard herunterladen') }}</a></p>
        </div>
        <div class="contact-card-info">
            <dl>
                <dt>{{ $gettext('Name') }}</dt>
                <dd>{{ contact?.['formatted-name'] }}</dd>
                <dt>{{ $gettext('E-Mail') }}</dt>
                <dd>{{ contact?.email }}</dd>
                <dt>{{ $gettext('Einrichtungen') }}</dt>
                <dd>Einrichtung...</dd>
                <dt>{{ $gettext('Status') }}</dt>
                <dd>Autor/Tutor/...</dd>
                <dt>{{ $gettext('Studiengang') }}</dt>
                <dd>Studiengang</dd>
            </dl>
        </div>
    </div>
</template>
<script setup>
    import { computed, ref, onMounted } from 'vue';
    import { storeToRefs } from 'pinia';
    import { useCommunityOverviewStore } from '@/vue/store/pinia/community/community-overview.js';
    import { useContactStore } from '@/vue/store/pinia/community/community-contacts.js';
    import { useContext } from '../../../composables/context.js';

    const overviewStore = useCommunityOverviewStore();
    const contactStore = useContactStore();
    const context = useContext();

    const { drawerProps } = storeToRefs(overviewStore);


    const contactId = computed(() => {
        return drawerProps.value.contactId;
    });

    const contact = computed(() => {
        return contactStore.byId(contactId.value);
    });

    onMounted(() => {
        console.log(context.userId.value);
    });

</script>
<style lang="scss" scoped>
    .contact-card {
        display: inline-flex;
        flex-flow: row wrap;
        gap: 15px;
    }
    .contact-card-avatar {
        display: inline-flex;
        flex-direction: column;
        gap: 5px;
        p {
            margin: auto;
        }
    }
    .contact-card-info {
        margin-left: 10px;
        dl {
            margin: auto;
        }
    }
</style>