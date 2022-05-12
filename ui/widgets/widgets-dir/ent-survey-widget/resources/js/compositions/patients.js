import client from "@/http";
import { reactive, ref } from "@vue/composition-api"
import useDocumentsFilter from "./documentFilters";

export default function usePatients() {
    const loading = ref(false);
    const loadingMedicalData = ref(false);
    const loadingMessages = ref(false);
    const savingMedicalData = ref(false);

    const filters = reactive({
        pathologies: [],
        doctors: [],
        filterBySurvey: null,

    })
    const patients = reactive({
        data: [],
        meta: null,
    })

    const medicalData = reactive({
        pathology: null,
        initial_date: null,
        doctor: null,
    })

    const messages = ref([]);

    function getPatients({ page = 1, search = null, forListing = null, pathologies = [], doctors = [], filterBySurvey, filterByDocument, infoDocument,filterSurvey } = {}) {
        loading.value = true;

        return client.get("/backend/patients", { params: { search, page, forListing, pathologies, doctors, filterBySurvey, filterByDocument, infoDocument,filterSurvey } })
            .then(({ data }) => {
                patients.data = data.data;
                patients.meta = data.meta;
            })
            .finally(() => loading.value = false)
    }

    function getFilters() {
        const { filters: documentFilters, getFilters } = useDocumentsFilter();

        getFilters().then(() => filters.pathologies = documentFilters.pathologies);

        getDoctors()
            .then(() => filters.doctors = doctors.data.map(item => ({ label: `${item.name}`, ...item })));
    }

    function getMedicalData(patientId) {
        loadingMedicalData.value = true;

        return client.get(`/backend/patients/${patientId}/medical-data`)
            .then(({ data }) => {
                data = data.data;

                if (!data) return;

                medicalData.pathology = data.pathology;
                medicalData.initial_date = data.initial_date;
                medicalData.doctor = data.doctor;
            })
            .finally(() => loadingMedicalData.value = false)
    }

    function saveMedicalData(patientId, data) {
        savingMedicalData.value = true;

        return client.post(`/backend/patients/${patientId}/medical-data`, data)
            .finally(() => savingMedicalData.value = false);
    }

    function getMessages(patientId) {
        loadingMessages.value = true;

        return client.get(`/backend/patients/${patientId}/messages`)
            .then(res => {
                messages.value = res.data.data;
            })
            .finally(() => loadingMessages.value = false);
    }

    return {
        loading,
        patients,
        medicalData,
        savingMedicalData,
        loadingMedicalData,
        filters,
        loadingMessages,
        messages,
        getPatients,
        getFilters,
        getMedicalData,
        saveMedicalData,
        getMessages,
    }
}
