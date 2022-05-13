<template>
    <div class="mt-5">
        <b-page-header title="Crea nuovo documento" />
        <form class="bg-white p-4 rounded-md w-full" @submit.prevent="save">
            <div>
                <label for="json" class="font-semibold">JSON</label>
                <div class="mt-1">
                    <textarea
                        class="w-full border border-primary px-2 py-1 focus-within:outline-none"
                        v-model="form.json"
                        id="json"
                        rows="10"
                        placeholder="paste json here..."
                    ></textarea>
                    <err-msg :message="errors.json"></err-msg>
                </div>
            </div>
            <b-button class="mt-3" :disabled="loading">Save</b-button>
        </form>
    </div>
</template>

<script>
import { defineComponent, reactive } from '@vue/composition-api';
import useQuestions from '@/compositions/questions';
import Vue from 'vue';
import router from '@/router';

export default defineComponent({
    name: "QuestionFormPage",
    setup(props, { root }) {
        const form = reactive({
            json: ''
        });
        const { errors, loading, saveQuestion } = useQuestions();

        function save() {
            saveQuestion(form).then(({ message }) => {
                Vue.toasted.success(message);

                router.replace('/questions');
            })
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
