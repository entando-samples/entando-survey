import client from "@/http";
import Validator from "@/utils/validator";
import { reactive, ref } from "@vue/composition-api";

export default function useSurveys() {
    const surveys = reactive({ data: [], meta: null });

    const survey = reactive({
        title: "Untitled survey",
        description: "",
        questions: [],
        warning_answers: [],
    });


    const loading = ref(false);
    const validator = Validator({});

    async function getSurveys({
        page = 1,
        search = "",
    } = {}) {
        loading.value = true;
        const response = await client
            .get("/backend/surveys", { params: { page, search } })
            .finally(() => (loading.value = false));

        surveys.meta = response.data.meta;
        surveys.data = response.data.data || [];
    }

    async function getSurvey(id) {
        loading.value = true;

        const response = await client
            .get("/backend/surveys/" + id)
            .finally(() => (loading.value = false));
        
        survey.title = response.data.data.title;
        survey.description = response.data.data.description;
        survey.questions = response.data.data.questions.map((item) => item.id);
    }

    function saveSurvey(data) {
        validator.reset();
        loading.value = true;

        return client
            .post("/backend/surveys", data)
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

    function updateSurvey(id, data) {
        validator.reset();
        loading.value = true;

        return client
            .put("/backend/surveys/" + id, data)
            .catch((error) => {
                if (validator.isValidationError(error)) {
                    validator.adaptErr(error);
                }

                throw error;
            })
            .finally(() => (loading.value = false));
    }

    function deleteSurvey(id) {
        return client.delete(`/backend/surveys/${id}`);
    }

    function answerSurvey(surveyId, questionId, data) {
        validator.reset();
        loading.value = true;
        console.log('answerSurvey', surveyId, questionId, data); 
        return client
            .post(`/surveys/${surveyId}/questions/${questionId}/answer`, data)
            .catch((error) => {
                if (validator.isValidationError(error)) {
                    validator.adaptErr(error);
                }
                throw error;
            })
            .finally(() => (loading.value = false));
    }

    return {
        errors: validator.errors,
        surveys,
        survey,
        loading,
        getSurveys,
        getSurvey,
        saveSurvey,
        updateSurvey,
        deleteSurvey,
        answerSurvey,
    };
}
