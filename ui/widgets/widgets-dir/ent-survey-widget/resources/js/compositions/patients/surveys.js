import client from "@/http";
import { reactive, ref } from "@vue/composition-api"
import Validator from "@/utils/validator";

export default function useSurveys() {
    const loading = ref(false);

    const surveys = ref([]);
    const validator = Validator({});

    const survey = reactive({
        title: "",
        description: "",
        answers: [],
        completed_at: null,
        reviewed_at: null,
        note: null,
        sent_at: null,
        warning_answers: [],
    });

    function getSurveys(patientId) {
        loading.value = true;

        return client.get(`/backend/patients/${patientId}/surveys`)
            .then(({ data }) => {
                surveys.value = data.data;
            })
            .finally(() => loading.value = false)
    }

    function getSurvey(patientId, surveyId) {
        loading.value = true;

        return client.get(`/backend/patients/${patientId}/surveys/${surveyId}`)
            .then(({ data }) => {
                data = data.data;

                survey.title = data.title;
                survey.description = data.description;
                survey.reviewed_at = data.pivot.reviewed_at;
                survey.completed_at = data.pivot.completed_at;
                survey.note = data.pivot.note;
                survey.sent_at = data.pivot.created_at;
                survey.warning_answers = (data.warning_answers || []).map(ans => ans.id);
                survey.answers = data.patient_answers;

            })
            .finally(() => loading.value = false)

    }

    function saveNote(patientId, surveyId, data) {
        return client.post(`/backend/patients/${patientId}/surveys/${surveyId}/review`, data)
    }

    function remindSurvey(patientId, surveyId) {
        return client.post(`/backend/patients/${patientId}/surveys/${surveyId}/remind`)
    }

    async function sendSurveyToPatient(data) {
        validator.reset();
        loading.value = true;
        return await client.post('/backend/patient/assign-survey', data)
            .then((res) => {
                return res.data;
            })
            .catch((error) => {
                if (validator.isValidationError(error)) {
                    validator.adaptErr(error);
                }
                throw error;
            })
            .finally(() => {
                loading.value = false;
            });
    }

    return {
        errors:validator.errors,
        loading,
        surveys,
        survey,
        getSurveys,
        getSurvey,
        remindSurvey,
        saveNote,
        sendSurveyToPatient
    }
}
