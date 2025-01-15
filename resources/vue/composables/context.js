import { computed } from "vue";

export function useContext() {
    const id = computed(() => (isCourse.value ? window.STUDIP.URLHelper.parameters.cid : null));
    const isCourse = computed(() => "cid" in window.STUDIP.URLHelper.parameters);
    const userId = computed(() => window.STUDIP.USER_ID);

    return { id, isCourse, userId };
}
