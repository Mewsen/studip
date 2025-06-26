<template>
    <form method="post" :action="storeURL" class="default">
        <input type="hidden" :name="csrf.name" :value="csrf.value">
        <input v-for="(_, key) in items"
               :key="`input-${key}`"
               type="hidden"
               :name="key"
               :value="checkboxes[key] ? 1 : 0"
        >

        <fieldset>
            <legend>{{ $gettext('Angezeigte Veranstaltungsdaten') }}</legend>

            <label v-for="(label, key) in items" :key="key">
                <input :name="key"
                       type="checkbox"
                       v-model="checkboxes[key]"
                       :disabled="isDisabled(key)"
                >
                {{ label }}
            </label>
        </fieldset>

        <footer data-dialog-button>
            <button type="submit" class="accept button">
                {{ $gettext('Speichern') }}
            </button>
        </footer>
    </form>
</template>
<script setup lang="ts">
import { computed, reactive, unref } from "vue";
import { $gettext } from "../../assets/javascripts/lib/gettext";

type ValidField = 'course_number' | 'course_name' | 'lecturers' | 'rooms';

const props = defineProps({
    view: [String, null],
    weekday: [String, null],
    config: Object,
});

const checkboxes = reactive({...unref(props.config)});
if (!checkboxes.course_number && !checkboxes.course_name) {
    checkboxes.course_name = true;
}

const csrf = computed(() => window.STUDIP.CSRF_TOKEN);

const items: Record<ValidField, string> = {
    course_number: $gettext('Veranstaltungsnummer'),
    course_name: $gettext('Veranstaltungstitel'),
    lecturers: $gettext('Lehrende'),
    rooms: $gettext('Raum'),
};

const storeURL = window.STUDIP.URLHelper.getURL(`dispatch.php/admin/courseplanning/store_tilefilter/${props.view}/${props.weekday}`, {}, true);

function isDisabled(f: string): boolean {
    const field = f as ValidField;

    return (field === 'course_number' && !checkboxes.course_name)
        || (field === 'course_name' && !checkboxes.course_number);

}
</script>
