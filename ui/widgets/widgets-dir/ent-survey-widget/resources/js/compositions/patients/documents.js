import client from "@/http";
import { ref } from "@vue/composition-api"

export default function useDocuments() {
    const loading = ref(false);

    const folders = ref([]);

    function getDocuments(patientId) {
        loading.value = true;

        return client.get(`/backend/patients/${patientId}/documents`)
            .then(({ data }) => {
                folders.value = data.data;
            })
            .finally(() => loading.value = false)
    }

    function markAsRead(id) {
        return client.post(`/backend/patients/documents/${id}/mark-as-read`)
            .then(res => {
                return res.data
            })
    }

    return {
        loading,
        folders,
        getDocuments,
        markAsRead,
    }
}
