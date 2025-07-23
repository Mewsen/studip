import {ref, watch} from "vue";
import {$gettext} from "@/assets/javascripts/lib/gettext";

const getNestedValue = (object, path) => path.split('.').reduce((acc, key) => acc && acc[key], object);

export function useSortable(data) {
    const sortKey = ref(null);
    const sortOrder = ref('asc');
    const sortedData = ref([...data.value]);

    const sortData = () => {
        if (!sortKey.value) {
            sortedData.value = [...data.value];
            return;
        }

        sortedData.value = [...data.value].sort((a, b) => {
            const aValue = getNestedValue(a, sortKey.value);
            const bValue = getNestedValue(b, sortKey.value);
            if (aValue < bValue) return sortOrder.value === 'asc' ? -1 : 1;
            if (aValue > bValue) return sortOrder.value === 'asc' ? 1 : -1;
            return 0;
        });
    };

    watch([data, sortKey, sortOrder], sortData, { immediate: true });

    function sortBy(key, order = 'asc') {
        if (sortKey.value === key) {
            sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
        } else {
            sortKey.value = key;
            sortOrder.value = order;
        }
    }

    function getSortClass(key) {
        if (sortKey.value === key) {
            return  sortOrder.value === 'asc' ? ['sortasc'] : ['sortdesc'];
        }

        return [];
    }

    function getAriaSortString(key) {
        return key === sortKey.value
            ? (sortOrder.value === 'asc' ? 'ascending' : 'descending')
            : null;
    }

    function getAriaSortLabel(key, label) {
        if (sortKey.value !== key) {
            return null;
        }

        if (sortOrder.value === 'asc') {
            return $gettext('Es wird aufsteigend nach der Spalte „%{ label }“ sortiert.', { label });
        }

        return $gettext('Es wird absteigend nach der Spalte „%{ label }“ sortiert.', { label });
    }

    return {
        sortKey,
        sortedData,
        sortOrder,
        sortBy,
        getSortClass,
        getAriaSortString,
        getAriaSortLabel
    };
}
