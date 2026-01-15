<script setup>
import {computed, reactive} from 'vue';
import StudipIcon from '@/vue/components/StudipIcon.vue';
import {getDiscussionTypeStoreURL} from '@/vue/components/forum/helpers/urls';

const CSRF = STUDIP.CSRF_TOKEN;

const props = defineProps({
    discussion_type: {
        type: Object,
        default: () => ({})
    },
    icons: {
        type: Array,
        required: true
    }
});

const form = reactive({
    ...props.discussionType
});

const formActionURL = computed(() => {
    if (props.discussion_type.type_id) {
        return STUDIP.URLHelper.getURL(`dispatch.php/course/forum/discussion_types/save/${props.discussion_type.type_id}`);
    }

    return STUDIP.URLHelper.getURL(`dispatch.php/course/forum/discussion_types/save`);
});
</script>

<template>
    <form
        class="default forum"
        :action="formActionURL"
        method="post"
    >
        <input type="hidden" :name="CSRF.name" :value="CSRF.value">
        <fieldset>
            <legend class="hide-in-dialog">
                {{ $gettext('Neuen Diskussionstyp anlegen') }}
            </legend>

            <section>
                <label>
                    <span class="required">
                        {{ $gettext('Name') }}
                    </span>
                    <input
                        required
                        type="text"
                        name="name"
                        v-model="form.name"
                        maxlength="100"
                    />
                </label>
            </section>

            <section>
                <label for="studip_icons">
                    <span class="required">
                        {{ $gettext('Icon') }}
                    </span>
                </label>
                <div id="studip_icons" class="studip-icons-container">
                    <input type="hidden" v-model="form.icon" name="icon" />

                    <template v-for="icon in icons" :key="icon">
                        <button
                            class="button"
                            type="button"
                            :title="icon"
                            :class="{
                                'disabled': form.icon && form.icon !== icon,
                                'active': form.icon === icon
                            }"
                            @click="form.icon = icon">
                            <StudipIcon :shape="icon" :size="35" />
                        </button>
                    </template>
                </div>
            </section>
        </fieldset>
        <footer data-dialog-button>
            <button :disabled="!form.icon || !form.name" class="button accept">
                {{ $gettext('Speichern') }}
            </button>
            <button class="button cancel" type="button" data-dialog-close>
                {{ $gettext('Abbrechen') }}
            </button>
        </footer>
    </form>
</template>

