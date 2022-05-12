import client from "@/http";
import Validator from "@/utils/validator";
import { reactive, ref } from "@vue/composition-api";

export default function usePathologies() {
    const pathologies = ref([]);
    const pathology = reactive({
        title: "",
    });
    const loading = ref(false);
    const validator = Validator({});
    const realtionItems = reactive({
        loading: false,
        documents: [],
        surveys: [],
    });

    async function getPathologies({ search = null } = {}) {
        loading.value = true;
        const response = await client
            .get("/backend/pathologies", { params: { search } })
            .finally(() => (loading.value = false));

        pathologies.value = response.data.data;
    }

    async function getPathology(id) {
        loading.value = true;

        const response = await client
            .get("/backend/pathologies/" + id)
            .finally(() => (loading.value = false));

        pathology.title = response.data.data.title;
    }

    function savePathology(data) {
        validator.reset();
        loading.value = true;

        return client
            .post("/backend/pathologies/", data)
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

    function updatePathology(id, data) {
        validator.reset();
        loading.value = true;

        return client
            .put("/backend/pathologies/" + id, data)
            .catch((error) => {
                if (validator.isValidationError(error)) {
                    validator.adaptErr(error);
                }

                throw error;
            })
            .finally(() => (loading.value = false));
    }

    function deletePathology(id) {
        return client.delete(`/backend/pathologies/${id}`);
    }

    function getRelationItems(id) {
        realtionItems.loading = true;

        return client
            .get(`/backend/pathologies/${id}/relation-items`)
            .then((res) => {
                realtionItems.documents = res.data.data.documents;
                realtionItems.surveys = res.data.data.surveys;
            })
            .finally(() => {
                realtionItems.loading = false;
            });
    }

    return {
        loading,
        errors: validator.errors,
        pathologies,
        pathology,
        realtionItems,
        getPathologies,
        getPathology,
        savePathology,
        updatePathology,
        deletePathology,
        getRelationItems,
    };
}
