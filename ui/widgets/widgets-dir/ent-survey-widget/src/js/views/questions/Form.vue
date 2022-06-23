<template>
    <div class="mt-5">
        <b-page-header title="Crea nuovo documento" />
        <form class="bg-white p-4 rounded-md w-full">
            <div class="question-it">
                <label for="questionIt" class="font-semibold">Domanda</label>
                <div class="mt-1">
                    <input 
                        v-model="form.localizedQuestion[0].question"
                        type="text"
                        class="w-full border border-primary px-2 py-1 focus-within:outline-none"
                        id="questionIt"
                    />
                </div>
            </div>
            <div class="question-description-it mt-2">
                <label for="questionDescriptionIt" class="font-semibold">Testo domanda</label>
                <div class="mt-1">
                    <input 
                        v-model="form.localizedQuestionDescription[0].description"
                        type="text"
                        class="w-full border border-primary px-2 py-1 focus-within:outline-none"
                        id="questionDescriptionIt"
                    />
                </div>
            </div>
            <div class="answers-it mt-2">
                <label for="answersIt" class="font-semibold">Risposte</label>
                <div class="mt-1 flex flex-col">
                    <div class="flex">
                        <input 
                            v-model="localizedAnswersIt"
                            type="text"
                            class="border border-primary px-2 py-1 focus-within:outline-none"
                            id="answersIt"
                        />
                        <b-button class="ml-4" :disabled="!localizedAnswersIt" @click.prevent="addItAnswer">Aggiungi possibile risposta</b-button>
                    </div>
                    <div v-if="form.localizedAnswers[0].answers.length" class="mt-1 flex flex-col">
                        <span>Possibili risposte:</span>
                        <div v-for="(answer, index) in form.localizedAnswers[0].answers" :key="index" class="flex">
                            <span>{{ `${index + 1} - ${answer}` }}</span>
                            <b-button class="remove-answer-btn" @click.prevent="removeItAnswer(index)">X</b-button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-3 flex flex-row-reverse">
                <b-button @click.prevent="save" :disabled="loading">Save</b-button>
            </div>
        </form>
    </div>
</template>

<script>
import { defineComponent, reactive, ref } from '@vue/composition-api';
import useQuestions from '@/compositions/questions';
import Vue from 'vue';
import router from '@/router';

export default defineComponent({
    name: "QuestionFormPage",
    setup(props, { root }) {
        const form = reactive({
            localizedQuestion: [
                { question: '' },
            ],
            localizedQuestionDescription: [
                { description: '' },
            ],
            localizedAnswers: [
                { answers: [] },
            ],
            primaryKey: '',
            order: 0,
            protocolQuestion: '',
            scores: [],
            protocol: ''
        });
        
        let localizedAnswersIt = ref('');
        let localizedAnswersEn = ref('');
        
        const { errors, loading, saveQuestion } = useQuestions();

        function save() {
            form.primaryKey = form.localizedQuestion[0].question
                .toLowerCase()
                .replace(/[^a-z0-9]+/g, "-")
                .replace(/^-+|-+$/g, "-")
                .replace(/^-+|-+$/g, '');
            saveQuestion({json: JSON.stringify(form)}).then(({ message }) => {
                Vue.toasted.success(message);
                router.replace('/questions');
            })
        }

        function addItAnswer() {
            if (localizedAnswersIt.value) {
                form.localizedAnswers[0].answers.push(localizedAnswersIt.value)
                localizedAnswersIt.value = ''
            }
        }

        function removeItAnswer(index) {
            form.localizedAnswers[0].answers.splice(index, 1);
        }
        
        function addEnAnswer() {
            if (localizedAnswersEn.value) {
                form.localizedAnswers[1].answers.push(localizedAnswersEn.value)
                localizedAnswersEn.value = ''
            }
        }

        function removeEnAnswer(index) {
            form.localizedAnswers[1].answers.splice(index, 1);
        }

        return {
            errors,
            form,
            loading,
            localizedAnswersIt,
            localizedAnswersEn,
            save,
            addItAnswer,
            addEnAnswer,
            removeItAnswer,
            removeEnAnswer,
        }
    }
})
</script>

<style lang="scss">
.remove-answer-btn {
    background-color: red;
    padding: 0px 4px;
    border-color: red;
    margin-left: 8px;
}
</style>