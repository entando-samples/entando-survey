import client from "@/http";
import { ref } from "@vue/composition-api"

export default function useInfoDocuments() {
    const loading = ref(false);

    const documents = ref([]);
    function getDocuments(patientId) {
        loading.value = true;

        return client.get(`/backend/patients/${patientId}/info-documents`)
            .then(({ data }) => {
                documents.value = data.data;
            })
            .finally(() => loading.value = false)
    }

    function remindDocument(patientId, documentId) {
        return client.post(`/backend/patients/${patientId}/info-documents/${documentId}/remind`)
    }

    function getAllDocuments(){
        return client.get('/backend/documents');
    }

    async function getPatientInfo(patientId) {
        let response = await client.get(`/backend/patients/${patientId}/info`);
        return response.data.data;
    }

    return {
        loading,
        documents,
        getDocuments,
        remindDocument,
        getPatientInfo,
    }
}
