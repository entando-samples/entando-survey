import client from "@/http";
import Validator from "@/utils/validator";
import { reactive, ref } from "@vue/composition-api";

export default function useQuestions() {
    const questions = reactive({ data: [], meta: null });

    const loading = ref(false);
    const validator = Validator({});

    const filters = reactive({
        questions: [],
        protocols: [],
    });

    async function getQuestions({
        search = null,
        paginated = null,
        withAnswers = false,
        protocols = [],
        questions: fQuestions = [],
        page = 1,
    } = {}) {
        loading.value = true;
        const response = await client
            .get("/questions", {
                params: {
                    page,
                    search,
                    paginated,
                    withAnswers,
                    protocols,
                    questions: fQuestions,
                },
            })
            .finally(() => (loading.value = false));

        questions.data = response.data.data || [];
        questions.meta = response.data.meta;
    }

    function saveQuestion(data) {
        validator.reset();
        loading.value = true;

        return client
            .post("/backend/questions", data)
            .then((res) => {
                return res.data;
            })
            .catch((error) => {
                if (validator.isValidationError(error)) {
                    validator.adaptErr(error);
                }

                throw error;
            })
            .finally(() => (loading.value = false));
    }

    function deleteQuestion(id) {
        return client.delete(`/backend/questions/${id}`);
    }

    function getFilters() {
        return [];
    }

    return {
        errors: validator.errors,
        questions,
        loading,
        filters,
        getQuestions,
        saveQuestion,
        deleteQuestion,
        getFilters,
    };
}
