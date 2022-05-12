import client from "@/http";
import Validator from "@/utils/validator";
import { reactive, ref } from "@vue/composition-api";

export default function useMessages() {
    const message = reactive({
        id: null,
        topic: null,
        body: null,
        attachments: [],
        voice_message: null,
        read_at: null,
        require_call: false,
        called_at: null,
        created_at: null,
        reply: null,
        sender: null,
        is_archived: false,
        from_bo: true,
    });

    const messages = reactive({
        data: [],
        meta: null,
    });

    const counts = reactive({
        inbound: 0,
        read: 0,
        require_call: 0,
        archived: 0,
        outbound: 0,
    });

    const topics = ref([]);
    const loading = ref(false);
    const validator = Validator({});

    function getParameters() {
        return client.get("/backend/messages/configs").then((response) => {
            topics.value = (response.data.topics || [])

            const resCounts = response.data.counts;

            counts.inbound = resCounts.inbound;
            counts.read = resCounts.read;
            counts.require_call = resCounts.require_call;
            counts.archived = resCounts.archived;
            counts.outbound = resCounts.outbound;
        });
    }

    function getMessages({
        listType = "inbound",
        page = 1,
        search = null,
        topics = [],
    } = {}) {
        loading.value = true;

        return client
            .get(`/backend/messages/${listType}/list`, {
                params: {
                    page,
                    search,
                    topics,
                },
            })
            .then((response) => {
                messages.meta = response.data.meta;
                messages.data = response.data.data || [];
            })
            .finally(() => (loading.value = false));
    }


    function saveMessage(data) {
        validator.reset();
        loading.value = true;

        return client.post(`/backend/messages/`, data)
            .then(res => {
                return res.data.data
            })
            .catch((error) => {
                if (error?.response?.status === 422) {
                    validator.adaptErr(error);
                }

                throw error;
            })
            .finally(() => loading.value = false)
    }

    function getMessage(id, markAsSeen) {
        loading.value = true;

        return client
            .get(`/backend/messages/${id}`, {
                params: {
                    markAsSeen
                }
            })
            .then(({ data }) => {
                data = data.data;

                message.id = data.id;
                message.topic = data.topic;
                message.body = data.body;
                message.attachments = data.attachments;
                message.voice_message = data.voice_message;
                message.created_at = data.created_at;
                message.read_at = data.read_at;
                message.require_call = data.require_call;
                message.called_at = data.called_at;
                message.reply = data.reply;
                message.sender = data.sender;
                message.is_archived = data.is_archived;
                message.from_bo = data.from_bo;
            })
            .finally(() => (loading.value = false));
    }

    function sendReply({ id, data } = {}) {
        loading.value = true;
        validator.reset();

        return client
            .post(`/backend/messages/${id}/reply`, {
                ...data
            })
            .catch((error) => {
                if (error?.response?.status === 422) {
                    validator.adaptErr(error);
                }

                throw error;
            })
            .finally(() => (loading.value = false));

    }

    function markAsRequireCall(id) {
        loading.value = true;

        return client
            .post(`/backend/messages/${id}/mark-as-require-call`)
            .finally(() => (loading.value = false));
    }

    function markAsCalled(id) {
        loading.value = true;

        return client
            .post(`/backend/messages/${id}/mark-as-called`)
            .finally(() => (loading.value = false));
    }

    return {
        errors: validator.errors,
        loading,
        topics,
        counts,
        message,
        messages,
        errors: validator.errors,
        getParameters,
        getMessages,
        saveMessage,
        getMessage,
        sendReply,
        markAsRequireCall,
        markAsCalled
    };
}
