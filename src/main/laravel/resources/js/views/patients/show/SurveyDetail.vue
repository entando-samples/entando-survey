<template>
    <div>
        <b-page-header title="Dettagilo questionario" />

        <p v-if="loading">Loading detail...</p>

        <template v-else>
            <div>
                <h2 class="capitalize text-2xl font-bold">{{ survey.title }}</h2>
                <div class="mt-2 grid grid-cols-2">
                    <table
                        class="border-separate"
                        style="border-spacing: 10px; margin-left: -10px;"
                    >
                        <tr>
                            <td>Inviato il:</td>
                            <td class="font-semibold">{{ survey.sent_at | date }}</td>
                        </tr>
                        <tr>
                            <td>Sato:</td>
                            <td class="font-semibold">
                                <p v-if="survey.completed_at" class="text-green-500">Compilato</p>
                                <p v-else class="text-gray-500">Non Compilato</p>
                            </td>
                        </tr>
                        <tr>
                            <td>Compilator il:</td>
                            <td class="font-semibold">
                                <p v-if="survey.completed_at">{{ survey.completed_at | date }}</p>
                                <p v-else>---</p>
                            </td>
                        </tr>
                        <tr v-if="survey.completed_at">
                            <td>Reviewed il:</td>
                            <td class="font-semibold">
                                <p v-if="survey.reviewed_at">{{ survey.reviewed_at | date }}</p>
                                <p v-else>---</p>
                            </td>
                        </tr>
                    </table>
                    <div>
                        <p class="font-semibold">Description:</p>
                        <p class="mt-1" v-html="survey.description"></p>
                    </div>
                </div>
            </div>

            <b-block class="mt-10 py-5">
                <table class="table-auto w-full">
                    <thead class="uppercase text-white bg-ternary">
                        <tr class="font-semibold text-left">
                            <td class="p-2">#</td>
                            <td class="p-2">Titolo</td>
                            <td class="p-2">Domanda</td>
                            <td class="p-2">Riposta</td>
                            <td class="p-2">Alert</td>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <tr v-if="!survey.answers.length">
                            <td class="py-3" colspan="10">
                                <p class="text-center text-sm text-gray-500">No answers found</p>
                            </td>
                        </tr>
                        <tr
                            v-else
                            v-for="(answer, index) in survey.answers"
                            :key="answer.id"
                            :class="index % 2 === 0 ? 'bg-gray-100' : ''"
                        >
                            <td class="px-2 py-3 font-semibold">{{ index + 1 }}.</td>
                            <td
                                class="px-2 py-3 font-semibold"
                                style="max-width: 100px;"
                            >{{ answer.question.title }}</td>
                            <td
                                class="px-2 py-3"
                                style="max-width: 150px;"
                            >{{ answer.question.description }}</td>
                            <td class="px-2 py-3" style="max-width: 100px;">{{ answer.body }}</td>
                            <td class="px-2 py-3">
                                <svg
                                    v-if="survey.warning_answers.includes(answer.id)"
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-6 w-6 fill-current text-yellow-500"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </b-block>

            <div class="mt-5" v-if="survey.completed_at">
                <form @submit.prevent="save">
                    <label class="block font-semibold" for="note">Note:</label>
                    <textarea
                        class="block mt-1 p-2 border-2"
                        cols="60"
                        rows="5"
                        v-model="survey.note"
                    ></textarea>
                    <b-button
                        class="mt-3"
                        type="submit"
                        :disabled="savingNote || loading"
                    >{{ survey.reviewed_at ? "Update" : "Save and Mark as reviewed" }}</b-button>
                </form>
            </div>
        </template>
    </div>
</template>

<script>
import useSurveys from "@/compositions/patients/surveys";
import { defineComponent, ref } from "@vue/composition-api";
import Vue from "vue";

export default defineComponent({
    name: "SurveyDetailPage",
    setup(_, { root }) {
        const { loading, survey, getSurvey, saveNote } = useSurveys();
        const savingNote = ref(false);

        getSurvey(root.$route.params.patientId, root.$route.params.surveyId).catch(err => {
            Vue.toasted.error(err?.response?.data?.message || err.message)
        });

        function save() {
            savingNote.value = true;

            saveNote(root.$route.params.patientId, root.$route.params.surveyId, {
                note: survey.note
            })
                .then(({ data }) => {
                    survey.reviewed_at = data.data.reviewed_at;
                    Vue.toasted.success("Note saved successfully");
                })
                .finally(() => {
                    savingNote.value = false;
                })
                .catch(err => {
                    Vue.toasted.error(err?.response?.data?.message || err.message)
                })
        }

        return {
            loading,
            survey,
            savingNote,
            save,
        };
    }
})
</script>
