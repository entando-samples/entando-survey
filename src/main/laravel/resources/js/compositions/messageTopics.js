import client from "@/http";
import Validator from "@/utils/validator";
import { reactive, ref } from "@vue/composition-api";

export default function useMessageTopics() {
    const topics = ref([]);
    const topic = reactive({
        title: "",
    });
    const loading = ref(false);
    const validator = Validator({});
    const messagesCount = reactive({
        loading: false,
        value: 0,
    });

    async function getTopics({ search = null } = {}) {
        loading.value = true;
        const response = await client
            .get("/backend/message-topics", { params: { search } })
            .finally(() => (loading.value = false));

        topics.value = response.data.data;
    }

    async function getTopic(id) {
        loading.value = true;

        const response = await client
            .get("/backend/message-topics/" + id)
            .finally(() => (loading.value = false));

        topic.title = response.data.data.title;
    }

    function saveTopic(data) {
        validator.reset();
        loading.value = true;

        return client
            .post("/backend/message-topics/", data)
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

    function updateTopic(id, data) {
        validator.reset();
        loading.value = true;

        return client
            .put("/backend/message-topics/" + id, data)
            .catch((error) => {
                if (validator.isValidationError(error)) {
                    validator.adaptErr(error);
                }

                throw error;
            })
            .finally(() => (loading.value = false));
    }

    function deleteTopic(id) {
        return client.delete(`/backend/message-topics/${id}`);
    }

    function getRelatedMessagesCount(id) {
        messagesCount.loading = true;

        return client
            .get(`/backend/message-topics/${id}/messages-count`)
            .then((res) => {
                messagesCount.value = res.data.data.count;
            })
            .finally(() => {
                messagesCount.loading = false;
            });
    }

    return {
        loading,
        errors: validator.errors,
        topics,
        topic,
        messagesCount,
        getTopics,
        getTopic,
        saveTopic,
        updateTopic,
        deleteTopic,
        getRelatedMessagesCount,
    };
}
