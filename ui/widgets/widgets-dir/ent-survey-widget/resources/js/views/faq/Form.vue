<template>
    <div class="mt-5">
        <b-page-header title="FAQ" />

        <form class="bg-white p-4 rounded-md w-full" @submit.prevent="save">
            <div>
                <label for="question" class="font-semibold">Question</label>
                <div class="mt-1">
                    <input
                        type="text"
                        class="w-full border border-primary px-2 py-1 focus-within:outline-none"
                        v-model="form.question"
                        id="question"
                    />
                    <err-msg :message="errors.question"></err-msg>
                </div>
            </div>
            <div class="mt-3">
                <label for="answer" class="font-semibold">Answer</label>
                <div class="mt-1">
                    <textarea
                        class="w-full border border-primary px-2 py-1 focus-within:outline-none"
                        v-model="form.answer"
                        id="answer"
                        rows="5"
                    ></textarea>
                    <err-msg :message="errors.answer"></err-msg>
                </div>
            </div>
            <b-button class="mt-3" :disabled="loading">Save</b-button>
        </form>
    </div>
</template>

<script>
import { computed, defineComponent } from '@vue/composition-api';
import Vue from 'vue';
import router from '@/router';
import useFaq from '@/compositions/faq';

export default defineComponent({
    name: "FaqFormPage",
    setup(_, { root }) {
        const { errors, loading, qa: form, getQa, saveQa, updateQa } = useFaq();

        let isCreating = computed(() => root.$route.name === 'faq.create');

        onCreated();

        function onCreated() {
            if (isCreating.value) return;

            getQa(root.$route.params.id)
                .catch(err => {
                    if (err.response?.status === 404) {
                        router.back();
                    }

                    Vue.toasted.error(err?.response?.data?.message || err.message);
                })
        }

        function save() {
            if (isCreating.value) {
                saveQa(form)
                    .then((data) => {
                        Vue.toasted.success('saved successfully');

                        router.push({
                            name: "faq.edit",
                            params: { id: data.data.id }
                        })
                    })
                    .catch(err => {
                        Vue.toasted.error(err?.response?.data?.message || err.message);
                    })


                return;
            }

            updateQa(root.$route.params.id, form)
                .then(() => {
                    Vue.toasted.success('updated successfully');
                })
                .catch((err) => {
                    if (err.response?.status === 404) {
                        router.back();
                    }

                    Vue.toasted.error(err?.response?.data?.message || err.message)
                });
        }

        return {
            errors,
            form,
            loading,
            save
        }
    }
})
</script>
