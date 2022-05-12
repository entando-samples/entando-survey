<template>
    <div class="mt-3">
        <b-page-header title="Send Message" />
        <b-block>
            <form @submit.prevent="save">
                <div>
                    <label class="font-semibold" for="topic">Topic</label>
                    <b-select
                        class="mt-1"
                        :options="topics"
                        label="title"
                        placeholder="select topic"
                        :reduce="ob => ob.id"
                        v-model="form.topic"
                    ></b-select>
                    <err-msg :message="errors.topic"></err-msg>
                </div>
                <div class="mt-3" v-if="!patientFromUrl">
                    <label class="font-semibold" for="topic">Patient</label>
                    <b-select
                        class="mt-1"
                        :options="computedPatients"
                        placeholder="search and select a patient"
                        @search="onPatientSearch"
                        label="label"
                        :filterable="false"
                        server-message="type to search patients..."
                        v-model="form.patient"
                        :reduce="ob => ob.id"
                    >
                        <template #option="{ option, isSelected }">
                            <div class="-mx-3 flex items-center space-x-2">
                                <input
                                    type="checkbox"
                                    :checked="isSelected"
                                    style="pointer-events: none;"
                                />
                                <p class="font-semibold">{{ option.name }}</p>
                            </div>
                            <p class="ml-2">{{ option.email }}</p>
                        </template>
                    </b-select>
                    <err-msg :message="errors.patient"></err-msg>
                </div>
                <div class="mt-3">
                    <label class="font-semibold" for="message">Message Body</label>
                    <textarea
                        class="mt-1 w-full border border-primary px-2 py-1 focus-within:outline-none"
                        id="message"
                        rows="10"
                        placeholder="write your message here"
                        v-model="form.body"
                    ></textarea>
                    <err-msg :message="errors.body"></err-msg>
                </div>
                <b-button class="mt-3" :disabled="loading">Send</b-button>
            </form>
        </b-block>
    </div>
</template>

<script>
import useMessages from "@/compositions/messages";
import useMessageTopics from "@/compositions/messageTopics";
import router from "@/router";
import { computed, defineComponent, reactive } from "@vue/composition-api";
import debounce from "lodash/debounce";
import Vue from "vue";

export default defineComponent({
    name: "MessageFormPage",
    setup(_, { root }) {
        const { topics, getTopics } = useMessageTopics();
        const { loading, errors, saveMessage } = useMessages();
        const form = reactive({
            topic: null,
            patient: root.$route.query.patient || null,
            body: null,
        });

        const patientFromUrl = root.$route.query.patient ? true : false;

        const computedPatients = computed(() => {
            return (patients.data || []).map(item => ({
                label: `${item.name} (${item.email})`,
                ...item
            }))
        })

        getTopics();

        function save() {
            saveMessage(form)
                .then(() => {
                    Vue.toasted.success("Message created successfully");

                    router.push({
                        name: 'messages.index.outbound'
                    })
                })
                .catch(err => {
                    if(errors.patient && patientFromUrl){
                        Vue.toasted.error(errors.patient);
                        return router.back();
                    }
                    
                    Vue.toasted.error(err?.response?.data?.message || err.message)
                })
        }

        const fetchPatients = debounce((search, loading) => {
            getPatients({ search })
                .then(loading(false))
        }, 500)

        const onPatientSearch = (search, loading) => {
            if (search) {
                loading(true);
                fetchPatients(search.trim(), loading)
            }
        }

        return {
            errors,
            loading,
            topics,
            computedPatients,
            form,
            patientFromUrl,
            onPatientSearch,
            save,
        }
    }
})
</script>
