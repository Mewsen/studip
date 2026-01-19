import { ref } from 'vue';

export function useLoadingBuffer(delay = 800) {
    const showLoading = ref(false);
    const isProcessing = ref(false);
    let timer = null;

    async function runWithLoading(task) {
        isProcessing.value = true;
        timer = setTimeout(() => {
            showLoading.value = true;
        }, delay);

        try {
            return await task();
        } finally {
            clearTimeout(timer);
            showLoading.value = false;
            isProcessing.value = false;
        }
    }

    return { showLoading, isProcessing, runWithLoading };
}