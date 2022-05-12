import client from "@/http";
import Validator from "@/utils/validator";
import { ref } from "@vue/composition-api";

export default function useCredit() {
    const content = ref("");
    const loading = ref(false);
    const validator = Validator();

    function getCredit() {
        loading.value = true;

        return client
            .get("/backend/credit")
            .then(({ data }) => {
                content.value = data.data.content;
            })
            .finally(() => loading.value = false)
    }

    function updateCredit(content) {
        loading.value = true;

        return client
            .post("/backend/credit", {
                content
            })
            .catch((error) => {
                if (error?.response?.status === 422) {
                    validator.adaptErr(error);
                }

                throw error;
            })
            .finally(() => loading.value = false)
    }

    return {
        loading,
        content,
        errors: validator.errors,
        getCredit,
        updateCredit
    }
}
