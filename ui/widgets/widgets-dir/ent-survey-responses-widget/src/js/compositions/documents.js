import client from "@/http";
import Validator from "@/utils/validator";
import { reactive, ref } from "@vue/composition-api";

export default function useDocuments() {
    const documents = reactive({ data: [], meta: null });
    const infoDocuments = reactive({ data: [], meta: null });
    const document = reactive({
        title: "",
        body: "",
        youtube_url: "",
        cover_image: null,
        file: null,
        images: [],
        pathologies: [],
        creator: null,
        created_at: null,
        last_updator: null,
        updated_at: null,
    });

    const loading = ref(false);

    const validator = Validator({});

    async function getAllInfoDocuments() {
        loading.value = true;
        const response = await client.get("/backend/documents")
            .finally(() => {
                loading.value = false;
            })
        infoDocuments.data = response.data.data;
    }
    async function getDocuments({
        page = 1,
        search = "",
        pathologies = [],
    } = {}) {
        loading.value = true;

        const response = await client
            .get("/backend/documents", {
                params: {
                    page,
                    search,
                    pathologies,
                },
            })
            .finally(() => (loading.value = false));

        documents.meta = response.data.meta;
        documents.data = response.data.data || [];
        console.log(documents.data);
    }

    async function saveDocument(data) {
        validator.reset();

        loading.value = true;

        return client
            .post("/backend/documents", data)
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

    async function getDocument(id) {
        loading.value = true;
        const response = await client
            .get(`/backend/documents/${id}`)
            .finally(() => (loading.value = false));

        const responseDoc = response.data.data;

        document.title = responseDoc.title;
        document.body = responseDoc.body;
        document.youtube_url = responseDoc.youtube_url;
        document.cover_image = responseDoc.cover_image;
        document.file = responseDoc.file;
        document.images = responseDoc.images || [];
        document.pathologies = (responseDoc.pathologies || []).map(
            (item) => item.id
        );
        document.creator = responseDoc.creator;
        document.created_at = responseDoc.created_at;
        document.last_updator = responseDoc.last_updator;
        document.updated_at = responseDoc.updated_at;
    }

    function updateDocument(documentId, data) {
        validator.reset();

        loading.value = true;

        return client
            .put(`/backend/documents/${documentId}`, data)
            .catch((error) => {
                if (error?.response?.status === 422) {
                    validator.adaptErr(error);
                }

                throw error;
            })
            .finally(() => {
                loading.value = false;
            });
    }

    async function deleteDocument(documentId) {
        return client.delete(`/backend/documents/${documentId}`)
            ;
    }

    async function assignDocumentToPatient(data) {
        validator.reset();

        loading.value = true;
        return await client.post('/backend/patient/info-document', data)
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
        errors: validator.errors,
        loading,
        document,
        documents,
        infoDocuments,
        getDocuments,
        getAllInfoDocuments,
        saveDocument,
        getDocument,
        updateDocument,
        deleteDocument,
        assignDocumentToPatient
    };
}
