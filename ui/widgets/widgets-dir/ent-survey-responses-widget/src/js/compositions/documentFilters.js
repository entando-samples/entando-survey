import client from "@/http";
import { reactive } from "@vue/composition-api";

export default function useDocumentsFilter() {
    const filters = reactive({
    });

    function getFilters() {
        return client.get("/backend/documents-filter").then(({ data }) => {
        });
    }

    return {
        filters,
        getFilters,
    };
}
