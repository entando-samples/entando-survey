<template>
    <div class="mt-5">
        <b-page-header title="Crea nuovo documento" />
        <form class="bg-white p-4 rounded-md w-full" @submit.prevent="save">
            <div>
                <label for="title" class="font-semibold">Title</label>
                <div class="mt-1">
                    <input
                        id="title"
                        type="text"
                        v-model="form.title"
                        class="w-full border border-primary px-2 py-1 focus-within:outline-none"
                    />
                    <err-msg :message="errors.title"></err-msg>
                </div>
            </div>
            <b-button class="mt-3">Save</b-button>
        </form>
    </div>
</template>

<script>
import router from '@/router';
import Vue from 'vue';
import { computed, defineComponent } from '@vue/composition-api';
import useMessageTopics from '@/compositions/messageTopics';

export default defineComponent({
    name: "MessageTopicFormPage",
    setup(_, { root }) {
        const { loading, topic: form, getTopic, errors, saveTopic, updateTopic } = useMessageTopics();

        let isCreating = computed(() => root.$route.name === 'messageTopics.create');

        onCreated();

        function onCreated() {
            if (isCreating.value) return;

            getTopic(root.$route.params.id)
                .catch(err => {
                    if (err.response?.status === 404) {
                        router.back();
                    }

                    Vue.toasted.error(err?.response?.data?.message || err.message);
                })
        }

        function save() {
            const data = {
                title: form.title,
            };

            if (isCreating.value) {
                saveTopic(data)
                    .then(() => {
                        Vue.toasted.success('saved successfully');

                        router.push({
                            name: 'messageTopics.index',
                        })
                    })
                    .catch((err) => {
                        Vue.toasted.error(err?.response?.data?.message || err.message)
                    });

                return;
            }

            updateTopic(root.$route.params.id, data)
                .then(() => {
                    Vue.toasted.success('updated successfully');

                    router.push({
                        name: 'messageTopics.index',
                    })
                })
                .catch((err) => {
                    if (err.response?.status === 404) {
                        router.back();
                    }

                    Vue.toasted.error(err?.response?.data?.message || err.message)
                });
        }

        return {
            isCreating,
            form,
            save,
            errors,
            loading,
        }
    }
})
</script>

