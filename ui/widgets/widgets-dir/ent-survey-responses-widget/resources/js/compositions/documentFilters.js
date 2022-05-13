import client from "@/http";
import { reactive } from "@vue/composition-api";

export default function useDocumentsFilter() {
    const filters = reactive({
        pathologies: [],
    });

    function getFilters() {
        return client.get("/backend/documents-filter").then(({ data }) => {
            filters.pathologies = data.data.pathologies;
        });
    }

    return {
        filters,
        getFilters,
    };
}
