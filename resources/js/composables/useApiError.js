import { ref } from 'vue';

export const useApiError = () => {
  const toastMessage = ref('');
  const toastType = ref('error');
  const toastVisible = ref(false);

  const showToast = (message, type = 'info') => {
    toastMessage.value = message;
    toastType.value = type;
    toastVisible.value = true;
    setTimeout(() => {
      toastVisible.value = false;
    }, 5000);
  };

  const getErrorMessage = (error, fallback = 'Something went wrong') => {
    return error?.response?.data?.message || error?.message || fallback;
  };

  const handleApiError = (error, fallback = 'Something went wrong') => {
    showToast(getErrorMessage(error, fallback), 'error');
  };

  const closeToast = () => {
    toastVisible.value = false;
  };

  return {
    toastMessage,
    toastType,
    toastVisible,
    showToast,
    handleApiError,
    closeToast,
    getErrorMessage,
  };
};
