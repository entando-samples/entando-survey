import Validator from "@/utils/validator";
import { reactive, ref } from "@vue/composition-api";
import client from "@/http";

export default function useFaq() {
    const faq = ref([]);
    const qa = reactive({
        question: '',
        answer: '',
    })

    const loading = ref(false);
    const validator = Validator({});

    function getFaq({
        // search = null,
    } = {}) {
        loading.value = true;

        return client
            .get(`/backend/faq`, {
                params: {
                    // search,
                },
            })
            .then((response) => {
                faq.value = response.data.data;
            })
            .finally(() => (loading.value = false));
    }

    function saveQa(data) {
        loading.value = true;

        return client
            .post(`/backend/faq`, data)
            .then(res => res.data)
            .catch((error) => {
                if (error?.response?.status === 422) {
                    validator.adaptErr(error);
                }

                throw error;
            })
            .finally(() => (loading.value = false));
    }

    function getQa(id) {
        loading.value = true;

        return client
            .get(`/backend/faq/${id}`, {
                params: {
                    // search,
                },
            })
            .then(({ data }) => {
                qa.question = data.data.question;
                qa.answer = data.data.answer;
            })
            .finally(() => (loading.value = false));
    }

    function updateQa(id, data) {
        loading.value = true;

        return client
            .put(`/backend/faq/${id}`, data)
            .catch((error) => {
                if (error?.response?.status === 422) {
                    validator.adaptErr(error);
                }

                throw error;
            })
            .finally(() => (loading.value = false));
    }

    function deleteQa(id) {
        return client
            .delete(`/backend/faq/${id}`)
    }

    function saveSort(ids) {
        return client
            .post(`/backend/faq/sort`, { ids })
            .catch(err => {
                if (err?.response?.status === 422) {
                    getFaq();

                    throw new Error("Some items seems to be deleted, please sort again");
                }
                throw err;
            })
    }

    return {
        faq,
        loading,
        qa,
        errors: validator.errors,
        getFaq,
        saveQa,
        getQa,
        updateQa,
        deleteQa,
        saveSort,
    }
}
