<script setup>
import {computed, onMounted, reactive, useTemplateRef} from "vue";
import {$gettext} from "../../../../assets/javascripts/lib/gettext";

const CSRF = STUDIP.CSRF_TOKEN;

const props = defineProps({
    category: {
        type: Object
    }
});

const categoryForm = reactive({
    ...props.category
});

const formActionURL = computed(() => {
    if (props.category.category_id) {
        return STUDIP.URLHelper.getURL(`dispatch.php/course/forum/categories/save/${props.category.category_id}`);
    }

    return STUDIP.URLHelper.getURL(`dispatch.php/course/forum/categories/save`);
});

const nameInput = useTemplateRef('name-input');

onMounted(() => {
    nameInput.value.focus();
});
</script>

<template>
    <div class="forum" style="display: flex;">
        <form
            class="default use-utility-classes forum-form"
            :action="formActionURL"
            method="post"
        >
            <input type="hidden" :name="CSRF.name" :value="CSRF.value">
            <fieldset>
                <legend v-if="category.category_id" class="hide-in-dialog">
                    {{ $gettext('Kategorie bearbeiten') }}
                </legend>
                <legend v-else class="hide-in-dialog">
                    {{ $gettext('Neue Kategorie anlegen') }}
                </legend>

                <section>
                    <label class="studiprequired m-0">
                        <span class="textlabel">{{ $gettext('Name') }}</span>
                        <span :title="$gettext('Name ist ein Pflichtfeld')" aria-hidden="true" class="asterisk">*</span>
                        <input
                            required
                            type="text"
                            name="name"
                            ref="name-input"
                            v-model="categoryForm.name"
                            class="max-w-full" />
                    </label>
                </section>

                <section>
                    <label>
                        {{ $gettext('Beschreibung') }}
                        <textarea rows="5" name="description" v-model="categoryForm.description"></textarea>
                    </label>
                </section>

                <section>
                    <label class="m-0">
                        <span class="required">
                            {{ $gettext('Farbe') }}
                        </span>
                        <input
                            type="color"
                            name="color"
                            v-model="categoryForm.color" />
                    </label>
                </section>
            </fieldset>
            <footer data-dialog-button>
                <button class="button accept">
                    {{ $gettext('Speichern') }}
                </button>
                <button class="button cancel" type="button" data-dialog-close>
                    {{ $gettext('Abbrechen') }}
                </button>
            </footer>
        </form>
    </div>
</template>

