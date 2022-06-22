<template>
    <div class="mt-5">
        <b-page-header title="Crea nuovo documento" />
        <form class="bg-white p-4 rounded-md w-full">
            <div class="question-it">
                <label for="questionIt" class="font-semibold">Domanda (ITA)</label>
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
                <label for="questionDescriptionIt" class="font-semibold">Descrizione domanda (ITA)</label>
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
                <label for="answersIt" class="font-semibold">Risposte (ITA)</label>
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
            <div class="question-en mt-2">
                <label for="questionEn" class="font-semibold">Question (ENG)</label>
                <div class="mt-1">
                    <input 
                        v-model="form.localizedQuestion[1].question"
                        type="text"
                        class="w-full border border-primary px-2 py-1 focus-within:outline-none"
                        id="questionEn"
                    />
                </div>
            </div>
            <div class="question-description-en mt-2">
                <label for="questionDescriptionEn" class="font-semibold">Question's description (ENG)</label>
                <div class="mt-1">
                    <input 
                        v-model="form.localizedQuestionDescription[1].description"
                        type="text"
                        class="w-full border border-primary px-2 py-1 focus-within:outline-none"
                        id="questionDescriptionEn"
                    />
                </div>
            </div>
            <div class="answers-en mt-2">
                <label for="answersEn" class="font-semibold">Answers (ENG)</label>
                <div class="mt-1 flex flex-col">
                    <div class="flex">
                        <input 
                            v-model="localizedAnswersEn"
                            type="text"
                            class="border border-primary px-2 py-1 focus-within:outline-none"
                            id="answersEn"
                        />
                        <b-button class="ml-4" :disabled="!localizedAnswersEn" @click.prevent="addEnAnswer">Add possible answer</b-button>
                    </div>
                    <div v-if="form.localizedAnswers[1].answers.length" class="mt-1 flex flex-col">
                        <span>Possible answers:</span>
                        <div  v-for="(answer, index) in form.localizedAnswers[1].answers" :key="index" class="flex">
                            <span>{{ `${index + 1} - ${answer}` }}</span>
                            <b-button class="remove-answer-btn" @click.prevent="removeEnAnswer(index)">X</b-button>
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
                { question: '', lng: 'ita' },
                { question: '', lng: 'eng' },
            ],
            localizedQuestionDescription: [
                { description: '', lng: 'ita' },
                { description: '', lng: 'eng' },
            ],
            localizedAnswers: [
                { answers: [], lng: 'ita' },
                { answers: [], lng: 'eng' },
            ],
            primaryKey: '',
            order: '',
            protocolQuestion: '',
            scores: [],
            protocol: ''
        });
        
        let localizedAnswersIt = ref('');
        let localizedAnswersEn = ref('');
        
        const { errors, loading, saveQuestion } = useQuestions();

        function save() {
            console.log(form)
            saveQuestion(form).then(({ message }) => {
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