import { ref } from 'vue';

export function useCourseEditorNotifications() {
    const showSuccess = ref(false);
    const showError = ref(false);
    const showPublishSuccess = ref(false);

    function notifySuccess(duration = 3000) {
        showSuccess.value = true;
        setTimeout(() => {
            showSuccess.value = false;
        }, duration);
    }

    function notifyError(duration = 4000) {
        showError.value = true;
        setTimeout(() => {
            showError.value = false;
        }, duration);
    }

    function notifyPublishSuccess(duration = 3000) {
        showPublishSuccess.value = true;
        setTimeout(() => {
            showPublishSuccess.value = false;
        }, duration);
    }

    return {
        showSuccess,
        showError,
        showPublishSuccess,
        notifySuccess,
        notifyError,
        notifyPublishSuccess,
    };
}
